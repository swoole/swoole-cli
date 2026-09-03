<?php
/**
 * 导出当前构建配置的依赖关系为 JSON，供 sapi/deps-viewer 生成交互式依赖图
 *
 * @var SwooleCli\Preprocessor $this
 *
 * 说明：
 * - 库名与扩展名会重复（curl、openssl、zlib、sqlite3、readline、gettext、gmp、imagick…），
 *   因此节点 id 必须带 lib: / ext: 前缀
 * - Project::$license 存的是许可证文档 URL，不是名称；$licenseType 才是类型枚举
 * - 未调用 withLicense() 时 license='' 且 licenseType=LICENSE_SPEC(0)，此时应显示为 Unknown，
 *   与「设置了自定义许可证链接」的 Custom 区分开
 */

use SwooleCli\Preprocessor;

$rootDir = $this->getRootDir();

$readVersion = function (string $file): string {
    return trim((string)@file_get_contents($file));
};
$phpVersion = $readVersion($rootDir . '/sapi/PHP-VERSION.conf');
$swooleVersion = $readVersion($rootDir . '/sapi/SWOOLE-VERSION.conf');

$licenseTypeNames = [
    SwooleCli\Project::LICENSE_SPEC => 'Custom',
    SwooleCli\Project::LICENSE_APACHE2 => 'Apache-2.0',
    SwooleCli\Project::LICENSE_BSD => 'BSD',
    SwooleCli\Project::LICENSE_GPL => 'GPL',
    SwooleCli\Project::LICENSE_LGPL => 'LGPL',
    SwooleCli\Project::LICENSE_MIT => 'MIT',
    SwooleCli\Project::LICENSE_PHP => 'PHP License',
];

/**
 * 从源码包文件名解析版本号
 *
 * 实际文件名形态很杂，需覆盖：
 *   openssl-3.6.0.tar.gz        -> 3.6.0
 *   aom-v3.10.0.tar.gz          -> 3.10.0
 *   libxml2-v2.9.14.tar.gz      -> 2.9.14
 *   c-ares-1.24.0.tar.gz        -> 1.24.0
 *   ImageMagick-v7.1.2-8.tar.gz -> 7.1.2-8
 *   icu4c-73_2-src.tgz          -> 73.2
 *   lcms2.17.tar.gz             -> 2.17
 *   sqlite-autoconf-3430200.tar.gz -> 3.43.2
 *   libx265_master.tar.gz       -> master
 *   libyuv-b0f72309.tar.gz      -> git:b0f7230
 */
$versionOfFile = function (string $file): string {
    if ($file === '') {
        return '';
    }
    $base = preg_replace('/\.(tar\.(?:gz|bz2|xz|lz|lzma|Z)|tgz|tbz2|txz|zip|tar)$/i', '', basename($file));

    // 分支名
    if (preg_match('/[_-](master|main|HEAD|trunk)$/i', $base, $m)) {
        return $m[1];
    }
    // sqlite-autoconf 风格：3430200 -> 3.43.2
    // 必须排在 git hash 规则之前：纯数字串也会被 [0-9a-f]{7,40} 匹配到
    if (preg_match('/[_-](\d{7})$/', $base, $m)) {
        $v = $m[1];
        return ltrim(substr($v, 0, 1), '0') . '.' . ltrim(substr($v, 1, 2), '0') . '.' . ltrim(substr($v, 3, 2), '0');
    }
    // git commit hash：要求至少含一个 a-f 字母，避免误伤纯数字版本号
    if (preg_match('/[_-](?=[0-9a-f]*[a-f])([0-9a-f]{7,40})$/i', $base, $m)) {
        return 'git:' . substr($m[1], 0, 7);
    }
    // icu4c-73_2-src 风格
    if (preg_match('/[_-](\d+)_(\d+)[_-]/', $base, $m)) {
        return $m[1] . '.' . $m[2];
    }
    // 标准 x.y.z / x.y，允许 v 前缀与末尾 -N 后缀
    if (preg_match('/[_-]v?(\d+\.\d+(?:\.\d+)?(?:[.\-]\d+)?)$/', $base, $m)) {
        return ltrim($m[1], 'vV');
    }
    // 无分隔符，版本号紧跟名称：lcms2.17 -> 2.17
    if (preg_match('/(\d+\.\d+(?:\.\d+)?)$/', $base, $m)) {
        return $m[1];
    }
    if (preg_match('/[_-]v?(\d+)$/', $base, $m)) {
        return $m[1];
    }
    return '';
};

$nodes = [];
$edges = [];

$makeNode = function (SwooleCli\Project $project, string $prefix, string $type) use (
    &$nodes,
    $versionOfFile,
    $licenseTypeNames,
    $phpVersion,
    $swooleVersion
): void {
    $version = $versionOfFile($project->file);
    $versionSource = $version !== '' ? 'source-file' : '';
    $isBuiltin = false;

    if ($type === 'extension') {
        if (!empty($project->peclVersion)) {
            // PECL 扩展，版本在 peclVersion 上
            $version = $project->peclVersion;
            $versionSource = 'pecl';
        } elseif ($project->name === 'swoole') {
            $version = $swooleVersion;
            $versionSource = 'swoole';
        } else {
            // php-src 内置扩展：没有独立版本，随 PHP 源码一起发布
            $isBuiltin = true;
            if ($version === '') {
                $version = $phpVersion;
                $versionSource = 'php';
            }
        }
    }

    $licenseType = $project->licenseType;
    $licenseName = $licenseTypeNames[$licenseType] ?? 'Custom';
    $licenseInferred = false;
    if ($project->license === '' && $licenseType === SwooleCli\Project::LICENSE_SPEC) {
        if ($isBuiltin) {
            // 内置扩展是 php-src 的一部分，采用 PHP License；标记为推断值以便前端提示
            $licenseName = 'PHP License';
            $licenseInferred = true;
        } else {
            // 未声明许可证
            $licenseName = 'Unknown';
        }
    }

    $nodes[] = [
        'id' => $prefix . ':' . $project->name,
        'type' => $type,
        'name' => $project->name,
        'version' => $version,
        'versionSource' => $versionSource,
        'licenseType' => $licenseType,
        'licenseName' => $licenseName,
        'licenseInferred' => $licenseInferred,
        'licenseUrl' => $project->license,
        'homePage' => $project->homePage,
        'manual' => $project->manual,
        'sourceFile' => $project->file,
        'options' => $type === 'extension' ? $project->options : '',
    ];
};

foreach ($this->libraryList as $lib) {
    $makeNode($lib, 'lib', 'library');
}
foreach ($this->extensionList as $ext) {
    $makeNode($ext, 'ext', 'extension');
}

// 只保留指向已知节点的边，避免出现悬空引用
$known = [];
foreach ($nodes as $node) {
    $known[$node['id']] = true;
}

$addEdge = function (string $source, string $target, string $kind) use (&$edges, $known): void {
    if (isset($known[$source]) && isset($known[$target])) {
        $edges[] = ['source' => $source, 'target' => $target, 'kind' => $kind];
    }
};

foreach ($this->libraryList as $lib) {
    foreach ($lib->deps as $dep) {
        $addEdge('lib:' . $lib->name, 'lib:' . $dep, 'lib-lib');
    }
}
foreach ($this->extensionList as $ext) {
    foreach ($ext->deps as $dep) {
        $addEdge('ext:' . $ext->name, 'lib:' . $dep, 'ext-lib');
    }
    foreach ($ext->dependentExtensions as $dep) {
        $addEdge('ext:' . $ext->name, 'ext:' . $dep, 'ext-ext');
    }
}

echo json_encode(
    [
        'meta' => [
            'generator' => 'swoole-cli',
            'generatedAt' => date('c'),
            'phpVersion' => $phpVersion,
            'swooleVersion' => $swooleVersion,
            'osType' => $this->getOsType(),
            'buildType' => $this->getBuildType(),
            'counts' => [
                'library' => count($this->libraryList),
                'extension' => count($this->extensionList),
                'edge' => count($edges),
            ],
        ],
        'licenseTypeNames' => $licenseTypeNames,
        'nodes' => $nodes,
        'edges' => $edges,
    ],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);

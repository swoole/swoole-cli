# 构建依赖可视化工具教程

用 `sapi/deps-viewer` 把当前 swoole-cli 构建配置里的库与扩展依赖关系，
生成一个**单文件交互式 HTML 页面**：可以看到每个库/扩展的版本、开源许可证，
以及它们之间的依赖图。

产物是单个 `index.html`，CSS/JS/数据全部内联，**双击即可打开**，
不需要起服务器，也不联网（无 CDN 依赖）。

## 快速开始

在源码根目录执行：

```shell
# 1. 导出依赖数据
php prepare.php --with-dependency-json=1
#   产物：bin/dependency-graph.json

# 2. 生成网页
cd sapi/deps-viewer
node bin/deps-viewer.mjs build
#   产物：sapi/deps-viewer/dist/index.html
```

然后直接打开 `sapi/deps-viewer/dist/index.html` 即可。

> 工具零 npm 依赖（只用 Node 内置模块），无需 `npm install`。
> 需要 Node >= 18。

## 三个子命令

```shell
node bin/deps-viewer.mjs check    # 校验数据并打印统计（不生成网页）
node bin/deps-viewer.mjs build    # 生成 dist/index.html
node bin/deps-viewer.mjs verify   # 验证力导向布局算法
```

也可以走 npm scripts：

```shell
cd sapi/deps-viewer
npm run json      # 等价于步骤 1（会 cd 到项目根执行 php prepare.php）
npm run check
npm run build
npm run all       # json -> check -> build
```

`check` 的输出示例（当前配置）：

```
=========================== 依赖数据校验 ===========================
PHP 8.4.14 / swoole v6.2.0 / linux / release

库        : 48
扩展      : 38
节点合计  : 86
依赖边    : 150  lib-lib=104  ext-lib=39  ext-ext=7

许可证分布:
  PHP License    35  40.7%
  Custom         13  15.1%
  MIT            12  14.0%
  GPL             8   9.3%
  BSD             8   9.3%
  LGPL            6   7.0%
  Apache-2.0      4   4.7%

版本来源:
  source-file    48
  php            33
  pecl            4
  swoole          1

未声明许可证: 无
缺少版本    : 无
推断许可证  : 33 个（php-src 内置扩展）

被依赖最多:
  lib:zlib         17
  lib:openssl       9
  lib:libiconv      8
  lib:libxml2       8
  ...
```

## 界面功能

### 顶部

- **版本信息**：PHP、swoole、OS、构建类型
- **搜索框**：按名称或 id 模糊搜索，下拉结果可直接跳转并居中
- **主题切换**：暗色 / 亮色

### 统计卡片

库数量、扩展数量、依赖边数，以及**许可证分布条**（按占比着色，下方图例显示
每种许可证的数量）。

### 左侧筛选栏

- **类型**：第三方库 / PHP 扩展
- **许可证**：按名称多选（MIT、BSD、GPL…）
- **依赖边**：库→库、扩展→库、扩展→扩展
- **数据质量**：列出未声明许可证、缺少版本、孤立节点等异常情况

筛选只切换显示，**不会重跑布局**，因此图不会跳动。

### 依赖图

- **布局**：力导向，库偏左、扩展偏右；依赖越多的节点半径越大
- **拖拽节点**：拖动后松手会重新收敛
- **缩放平移**：滚轮以光标为锚点缩放，拖拽空白处平移，右下角有 `+` `−` `⟲` `✧` 按钮
- **点击节点**：高亮它的上下游——
  橙色描边是**依赖它的**（上游），绿色描边是**它依赖的**（下游），其余淡出
- **悬停**：显示名称、类型、版本、许可证

### 右侧详情面板

选中节点后展示：

- 类型徽章、版本、**版本来源**（说明版本号是怎么来的）
- 许可证徽章，可点开许可证全文链接
- 主页 / 文档外链、源码包文件名、编译选项
- **依赖**与**被依赖**列表，点击可跳转到对应节点

## 数据是怎么来的

### 版本

| 节点类型 | 版本来源 | 示例 |
|---|---|---|
| 第三方库 | 源码包文件名解析 | `openssl-3.6.0.tar.gz` → `3.6.0` |
| PECL 扩展 | `peclVersion` 配置 | `mongodb` → `2.3.1` |
| swoole 扩展 | `sapi/SWOOLE-VERSION.conf` | `v6.2.0` |
| php-src 内置扩展 | `sapi/PHP-VERSION.conf` | `curl` → `8.4.14` |

文件名形态很杂，解析规则已覆盖这些特例：

```
libxml2-v2.9.14.tar.gz          -> 2.9.14     （v 前缀）
ImageMagick-v7.1.2-8.tar.gz     -> 7.1.2-8    （末尾 -N 后缀）
icu4c-73_2-src.tgz              -> 73.2       （下划线版本 + -src 后缀）
sqlite-autoconf-3430200.tar.gz  -> 3.43.2     （7 位数字，sqlite 惯例）
lcms2.17.tar.gz                 -> 2.17       （无分隔符）
gmp-6.3.0.tar.lz                -> 6.3.0      （lzip 后缀）
libx265_master.tar.gz           -> master     （分支名）
libyuv-b0f72309.tar.gz          -> git:b0f7230（commit hash）
```

内置扩展显示的是 PHP 版本号，详情面板的"版本来源"会标注
**PHP 源码版本（内置扩展无独立版本）**，避免误读为扩展自己的版本。

### 许可证

`Project::$licenseType` 是类型枚举（`Project.php`），
`licenseName` 由它映射而来：

| licenseType | 显示名 |
|---|---|
| 0 `LICENSE_SPEC` | `Custom`（自定义许可证）或 `Unknown`（未声明） |
| 1 `LICENSE_APACHE2` | `Apache-2.0` |
| 2 `LICENSE_BSD` | `BSD` |
| 3 `LICENSE_GPL` | `GPL` |
| 4 `LICENSE_LGPL` | `LGPL` |
| 5 `LICENSE_MIT` | `MIT` |
| 6 `LICENSE_PHP` | `PHP License` |

两个容易混淆的点：

1. **`license` 字段存的是许可证文档的 URL，不是名称**——名称看 `licenseType`
2. **区分 `Unknown` 与 `Custom`**：两者 `licenseType` 都是 0，
   靠 `license` 是否为空判断。未调用 `withLicense()` 的是 `Unknown`；
   调用了 `withLicense($url, LICENSE_SPEC)` 的是 `Custom`

另外，php-src 内置扩展（33 个）在配置里并未声明许可证，工具按
"内置扩展属于 php-src，采用 PHP License"推断为 `PHP License`，
并在数据中以 `licenseInferred: true` 标记，界面上显示为带"推断"角标的徽章。

## 常见问题

### `找不到依赖数据: bin/dependency-graph.json`

还没导出数据。先执行：

```shell
php prepare.php --with-dependency-json=1
```

### 想看全部库/扩展，而不只是当前构建启用的

目前只展示当前构建配置实际参与的部分（48 库 + 38 扩展），与实际产物一致。
`builder/` 目录下还定义了未启用的库（如 `libavif`、`liburing`、`mimalloc`），
暂未纳入展示。

### 许可证显示 `Unknown` 怎么办

说明该扩展/库的配置里没调用 `withLicense()`。可以到
`sapi/src/builder/{extension,library}/<name>.php` 里补上，
参考 `openssl.php`：

```php
->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
```

判断许可证应以源码包内的 `LICENSE` / `COPYING` 文件为准。注意有些包会在子目录里
bundled 不同许可证的第三方代码，需要一并检查（参见
[libphp.md 中关于 mongodb 的 SSPL 提示](libphp.md)）。

### 布局能否复现

可以。初始位置用黄金角确定性生成（无随机数），因此同一份数据每次生成的布局
完全一致。`verify` 子命令会断言这一点，同时检查无 NaN、最小节点间距 > 12px、
耗时 < 150ms。

## 实现说明

数据流：

```
php prepare.php --with-dependency-json=1
        │  sapi/src/template/dependency_json.php
        ▼
bin/dependency-graph.json
        │  sapi/deps-viewer/src/render.mjs（内联 CSS/JS/数据）
        ▼
sapi/deps-viewer/dist/index.html
```

- **PHP 侧**：新增 `--with-dependency-json` 选项（与既有 `--with-dependency-graph`
  并列），由 `Preprocessor` 输出 JSON。选项通过 `getInputOption()` 读取，
  无需注册，也支持 `SWOOLE_CLI_WITH_DEPENDENCY_JSON` 环境变量
- **节点 id 带 `lib:` / `ext:` 前缀**：因为库名与扩展名存在同名
  （`curl`、`openssl`、`zlib`、`sqlite3`、`readline`、`gettext`、`gmp`、`imagick`）
- **依赖边**取自对象自身字段：`Library->deps`、`Extension->deps`、
  `Extension->dependentExtensions`
- **力导向布局**自实现（Fruchterman-Reingold 变体，`src/web/layout.mjs`），
  86 节点约 30ms，额外加了水平分层力使库偏左、扩展偏右
- **单文件产物**：`src/render.mjs` 把 CSS、两个 JS 片段、JSON 数据内联进模板。
  构建时会剥掉 `import`/`export` 并包进 IIFE——因为 `file://` 下
  `<script type="module">` 会被 CORS 阻断

开发相关（目录结构、JSON schema、脚本职责）见
[`sapi/deps-viewer/README.md`](../sapi/deps-viewer/README.md)。

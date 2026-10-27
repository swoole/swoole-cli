<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;
use SwooleCli\Preprocessor;

final class BuildStateTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/swoole-cli-build-state-' . bin2hex(random_bytes(8));
        foreach (['main', 'Zend', 'TSRM', 'ext', 'sapi/scripts', 'libs', 'var', 'mock-bin'] as $directory) {
            mkdir($this->workspace . '/' . $directory, 0755, true);
        }

        // These commands only write text fixtures. No compiler or linker runs.
        file_put_contents($this->workspace . '/mock-bin/make', <<<'SH'
#!/bin/sh
set -eu
if [ "$#" -ne 3 ] || [ "$1" != -j ] || [ "$2" != 1 ] || [ "$3" != libs/libphp.a ]; then
    exit 99
fi
if [ ! -f Zend/probe.o ]; then
    printf '%s\n' "$MOCK_TARGET" > Zend/probe.o
fi
if [ "$MOCK_MAKE_STATUS" -ne 0 ]; then
    exit "$MOCK_MAKE_STATUS"
fi
printf 'mock archive\n' > libs/libphp.a
SH
        );
        chmod($this->workspace . '/mock-bin/make', 0755);
        file_put_contents($this->workspace . '/sapi/scripts/merge-iphoneos-libphp.sh', <<<'SH'
#!/bin/sh
set -eu
printf '%s\n' "$IOS_SDK_NAME" > merge-sdk
exit "$MOCK_MERGE_STATUS"
SH
        );
    }

    protected function tearDown(): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->workspace, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($this->workspace);
    }

    public function testSwitchingTargetsRebuildsObjectsAndSelectsTheSdk(): void
    {
        $this->assertSame(0, $this->runBuild('iphoneos-arm64'));
        $this->assertSame("iphoneos-arm64\n", $this->read('var/.typephp-ios-object-target'));
        $this->assertSame("iphoneos\n", $this->read('merge-sdk'));

        file_put_contents($this->workspace . '/Zend/probe.o', "same-target-cache\n");
        $this->assertSame(0, $this->runBuild('iphoneos-arm64'));
        $this->assertSame("same-target-cache\n", $this->read('Zend/probe.o'));

        $this->assertSame(0, $this->runBuild('iphonesimulator-arm64'));
        $this->assertSame("iphonesimulator-arm64\n", $this->read('Zend/probe.o'));
        $this->assertSame("iphonesimulator-arm64\n", $this->read('var/.typephp-ios-object-target'));
        $this->assertSame("iphonesimulator\n", $this->read('merge-sdk'));
        $this->assertFileDoesNotExist($this->workspace . '/var/.typephp-ios-object-target.tmp');

        $this->assertSame(0, $this->runBuild('iphoneos-arm64'));
        $this->assertSame("iphoneos-arm64\n", $this->read('Zend/probe.o'));
    }

    public function testFailedSimulatorBuildInvalidatesThePreviousTarget(): void
    {
        $this->assertSame(0, $this->runBuild('iphoneos-arm64'));
        $this->assertSame(17, $this->runBuild('iphonesimulator-arm64', 17));
        $this->assertFileDoesNotExist($this->workspace . '/var/.typephp-ios-object-target');
        $this->assertSame("iphonesimulator-arm64\n", $this->read('Zend/probe.o'));

        $this->assertSame(0, $this->runBuild('iphoneos-arm64'));
        $this->assertSame("iphoneos-arm64\n", $this->read('Zend/probe.o'));
    }

    public function testFailedMergeInvalidatesThePreviousTarget(): void
    {
        $this->assertSame(0, $this->runBuild('iphoneos-arm64'));
        $this->assertSame(19, $this->runBuild('iphonesimulator-arm64', 0, 19));
        $this->assertFileDoesNotExist($this->workspace . '/var/.typephp-ios-object-target');

        $this->assertSame(0, $this->runBuild('iphoneos-arm64'));
        $this->assertSame("iphoneos-arm64\n", $this->read('Zend/probe.o'));
    }

    private function read(string $path): string
    {
        return file_get_contents($this->workspace . '/' . $path);
    }

    private function runBuild(string $target, int $makeStatus = 0, int $mergeStatus = 0): int
    {
        $root = dirname(__DIR__, 3);
        $preprocessor = new class ($this->workspace, $target, $root) extends Preprocessor {
            public function __construct(string $workspace, string $target, string $root)
            {
                parent::__construct();
                $this->targetType = $target;
                $this->setOsType('macos');
                $this->setRootDir($root);
                $this->setWorkDir($workspace);
                $this->setGlobalPrefix($workspace . '/var/' . $target . '/deps');
                $this->setPhpSrcDir($workspace . '/php-src');
                $this->setConfigureVariables('');
                $this->setMaxJob(1);
                $this->setLogicalProcessors('1');
            }
        };
        $generated = $this->workspace . '/make.generated.sh';
        $this->assertTrue($preprocessor->generateFile($root . '/sapi/src/template/make.php', $generated));
        $script = file_get_contents($generated);
        $start = strpos($script, 'make_libphp() {');
        $end = strpos($script, 'make_archive() {', $start);
        $driver = $this->workspace . '/run-build.sh';
        file_put_contents(
            $driver,
            "export_variables() { :; }\n" . substr($script, $start, $end - $start) . "\nmake_libphp\n"
        );

        $environment = array_merge(getenv(), [
            'PATH' => $this->workspace . '/mock-bin:' . getenv('PATH'),
            'MOCK_TARGET' => $target,
            'MOCK_MAKE_STATUS' => (string) $makeStatus,
            'MOCK_MERGE_STATUS' => (string) $mergeStatus,
        ]);
        $process = proc_open(
            ['bash', $driver],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $this->workspace,
            $environment
        );
        $this->assertIsResource($process);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($process);
        $this->assertSame('', $stdout . $stderr);
        return $status;
    }
}

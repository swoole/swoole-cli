<?php
/**
 * Copy shared library dependencies of swoole-cli to runtime/libs directory
 */

// Define paths
$binaryPath = __DIR__ . '/../../bin/swoole-cli';
$targetDir = __DIR__ . '/../../runtime/libs';

// Libraries that should be ignored (system/core libraries)
$ignoredLibraries = [
    'libc.so',
    'libm.so',
    'libpthread.so',
    'libdl.so',
    'librt.so',
    'ld-linux',
];

// Check if binary exists
if (!file_exists($binaryPath)) {
    echo "Error: Binary file not found at $binaryPath\n";
    exit(1);
}

// Create target directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true) || die("Error: Failed to create directory $targetDir\n");
    echo "Created directory: $targetDir\n";
}

$binaryPath = realpath($binaryPath);
$targetDir = realpath($targetDir);

// Get dependencies using ldd
echo "Getting dependencies for $binaryPath...\n";
$lddOutput = shell_exec('ldd ' . escapeshellarg($binaryPath));

if (empty($lddOutput)) {
    echo "Error: Failed to get dependencies\n";
    exit(1);
}

// Parse ldd output
$dependencies = [];
$lines = explode("\n", $lddOutput);

foreach ($lines as $line) {
    // Match lines with => (typical ldd output)
    if (preg_match('/^\s*.*\s+=>\s+(\/.*)\s+\(0x[0-9a-f]+\)$/', $line, $matches)) {
        $libPath = trim($matches[1]);
        if (file_exists($libPath)) {
            $dependencies[] = $libPath;
        }
    }
    // Match lines without => (statically linked or direct paths)
    elseif (preg_match('/^\s+(\/.*)\s+\(0x[0-9a-f]+\)$/', $line, $matches)) {
        $libPath = trim($matches[1]);
        if (file_exists($libPath)) {
            $dependencies[] = $libPath;
        }
    }
}

// Remove duplicates
$dependencies = array_unique($dependencies);

// Filter out ignored libraries
$filteredDependencies = [];
foreach ($dependencies as $libPath) {
    $fileName = basename($libPath);
    foreach ($ignoredLibraries as $ignoredLib) {
        if (strpos($fileName, $ignoredLib) !== false) {
            echo "Skipping system library: $fileName\n";
            continue 2;
        }
    }
    $filteredDependencies[] = $libPath;
}

echo "Found " . count($filteredDependencies) . " dependencies (excluding system libraries)\n";

// Copy each dependency to target directory
$successCount = 0;
foreach ($filteredDependencies as $libPath) {
    $fileName = basename($libPath);
    $targetPath = "$targetDir/$fileName";

    echo "Copying $libPath to $targetPath... ";

    if (copy($libPath, $targetPath)) {
        echo "SUCCESS\n";
        $successCount++;
    } else {
        echo "FAILED\n";
    }
}

echo "Successfully copied $successCount out of " . count($filteredDependencies) . " dependencies\n";

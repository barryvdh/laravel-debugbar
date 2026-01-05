<?php

use DebugBar\Bridge\Symfony\SymfonyMailCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\TemplateCollector;
use DebugBar\StandardDebugBar;

include __DIR__ . '/../vendor/autoload.php';

// Rquires `php vendor/bin/phpunit --filter=testItInjectsOnDocs`
$generatedScripts = file_get_contents(__DIR__ . '/docs/render.html');
// Remove first style/script
$generatedScripts = explode('</script>', $generatedScripts, 2)[1];

// Read the main.html template
$templatePath = __DIR__ . '/../docs/overrides/main.html';
$template = file_get_contents($templatePath);

// Replace the scripts block content between specific markers
$startMarker = "<!-- Start Debugbar -->";
$endMarker = "<!-- End Debugbar -->";

// Find the positions
$startPos = strpos($template, $startMarker);
$endPos = strpos($template, $endMarker);

if ($startPos !== false && $endPos !== false) {
    $startPos += strlen($startMarker);

    // Replace the content between markers
    $newTemplate = substr($template, 0, $startPos)
        . "\n" . $generatedScripts . "\n"
        . substr($template, $endPos);

    // Write back to the file
    file_put_contents($templatePath, $newTemplate);

    echo "✓ Updated docs/overrides/main.html with generated debugbar scripts\n";
} else {
    echo "✗ Could not find script markers in main.html\n";
    exit(1);
}

// Copy dist folder to docs/assets/dist
$distSource = __DIR__ . '/docs/assets';
$distDest = __DIR__ . '/../docs/assets/dist';

if (!is_dir($distSource)) {
    echo "✗ dist folder not found at $distSource\n";
    exit(1);
}

// Create docs/assets directory if it doesn't exist
if (!is_dir(__DIR__ . '/../docs/assets')) {
    mkdir(__DIR__ . '/../docs/assets', 0755, true);
}

// Remove existing dist folder if it exists
if (is_dir($distDest)) {
    deleteDirectory($distDest);
}

// Copy dist folder
copyDirectory($distSource, $distDest);

echo "✓ Copied dist folder to docs/assets/dist\n";

// Update mkdocs.yml with current timestamp
$mkdocsPath = __DIR__ . '/../mkdocs.yml';
$mkdocsContent = file_get_contents($mkdocsPath);
$timestamp = time();

$mkdocsContent = preg_replace(
    '/debugbar\.css\?v=\d+/',
    'debugbar.css?v=' . $timestamp,
    $mkdocsContent
);

$mkdocsContent = preg_replace(
    '/debugbar\.js\?v=\d+/',
    'debugbar.js?v=' . $timestamp,
    $mkdocsContent
);

file_put_contents($mkdocsPath, $mkdocsContent);

echo "✓ Updated mkdocs.yml with timestamp: $timestamp\n";

function copyDirectory($source, $dest) {
    mkdir($dest, 0755, true);

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $destPath = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathname();
        if ($item->isDir()) {
            mkdir($destPath, 0755, true);
        } else {
            copy($item, $destPath);
        }
    }
}

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir()) {
            rmdir($item);
        } else {
            unlink($item);
        }
    }

    rmdir($dir);
}

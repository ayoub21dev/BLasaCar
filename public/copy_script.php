<?php
$source = dirname(__DIR__) . '/resources/carRod.svg';
$destDir = __DIR__ . '/images';
$dest = $destDir . '/carRod.svg';

if (!is_dir($destDir)) {
    mkdir($destDir, 0777, true);
}

if (copy($source, $dest)) {
    echo "Success";
} else {
    echo "Failed";
}

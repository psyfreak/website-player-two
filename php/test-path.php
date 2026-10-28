
<?php
header('Content-Type: text/plain; charset=utf-8');

echo "PHP works\n";

$path = __DIR__ . '/../../private/vendor/autoload.php';

echo "Directory: " . __DIR__ . "\n";
echo "Path: " . $path . "\n";

if (file_exists($path)) {
    echo "Autoload exists: YES\n";
} else {
    echo "Autoload exists: NO\n";
}

if (is_readable($path)) {
    echo "Autoload readable: YES\n";
} else {
    echo "Autoload readable: NO\n";
}

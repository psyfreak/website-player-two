
<?php
header('Content-Type: text/plain; charset=utf-8');

echo "Step 1: PHP works\n";

try {
    $autoload = __DIR__ . '/../../private/vendor/autoload.php';

    require_once $autoload;
    echo "Step 2: Composer loaded successfully\n";

    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        echo "Step 3: PHPMailer class found\n";

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        echo "Step 4: PHPMailer instantiated successfully\n";

        echo "\nSUCCESS: PHPMailer is ready!\n";
    } else {
        echo "ERROR: PHPMailer class not found\n";
    }

} catch (\Throwable $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
}

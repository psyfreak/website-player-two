<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function reply(int $status, string $message): never {
    http_response_code($status);
    echo json_encode(['message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    reply(405, 'Methode nicht erlaubt.');
}
if (!empty($_POST['website'])) reply(200, 'Vielen Dank für deine Nachricht.');

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$company = trim((string) ($_POST['company'] ?? ''));
$topic = trim((string) ($_POST['topic'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

$topics = ['', 'create', 'improve', 'empower', 'evolve',
    'produktidee-prototyp', 'prozesse-automatisieren', 'software-modernisieren',
    'team-verstaerken', 'systeme-verbinden', 'daten-nutzen',
    'technologie-strategie', 'skalierung', 'projekt-retten'];
// Keep the topic list synchronized with src/data.ts; the website uses slug values.
if (!isset($_POST['privacy']) || $name === '' || $message === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($name) > 240 || strlen($email) > 200 || strlen($company) > 200 ||
    strlen($topic) > 120 || strlen($message) > 10000 ||
    preg_match('/[\r\n]/', $name . $email . $company . $topic) ||
    !in_array($topic, $topics, true)) {
    reply(422, 'Bitte prüfe deine Eingaben und versuche es erneut.');
}

// Configuration and Composer vendor/ MUST be outside the public document root.
$configFile = dirname(__DIR__, 2) . '/private/mail-config-p2.php';
$autoload = dirname(__DIR__, 2) . '/private/vendor/autoload.php';
try {
    if (!is_readable($configFile) || !is_readable($autoload)) {
        throw new RuntimeException('Mail configuration or PHPMailer not installed');
    }
    $config = require $configFile;
    if (!is_array($config)) throw new RuntimeException('Invalid mail config');
    $username = (string) ($config['smtp_username'] ?? '');
    $password = (string) ($config['smtp_password'] ?? '');
    if (!filter_var($username, FILTER_VALIDATE_EMAIL) || $password === '') {
        throw new RuntimeException('SMTP credentials missing');
    }
    require_once $autoload;
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.ionos.de';
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->Timeout = 15;
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(false);
    $mail->setFrom($username, 'Player Two');
    $mail->addAddress('contact@player-two.de');
    $mail->addReplyTo($email, $name);
    $mail->Subject = 'Player Two: Neue Projektanfrage' . ($topic !== '' ? ' (' . $topic . ')' : '');
    $mail->Body = "Neue Projektanfrage über player-two.de\n\n"
        . "Name: {$name}\nE-Mail: {$email}\nUnternehmen: {$company}\nQuest: {$topic}\n\nNachricht:\n{$message}\n";
    $mail->send();
    reply(200, 'Vielen Dank! Deine Nachricht wurde versendet.');
} catch (\Throwable $error) {
    error_log('Player Two contact form: ' . $error->getMessage());
    reply(503, 'Nachricht konnte nicht gesendet werden. Bitte schreibe direkt an contact@player-two.de.');
}

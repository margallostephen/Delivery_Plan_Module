<?php
require 'vendor/autoload.php';
require_once __DIR__ . '/utils/php/fetchDataPhpScript.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mailHost = $_ENV['MAIL_HOST'];
$mailUsername = $_ENV['MAIL_USERNAME'];
$mailPassword = $_ENV['MAIL_PASSWORD'];
$smtpEncryption = $_ENV['MAIL_ENCRYPTION'];
$mailPort = $_ENV['MAIL_PORT'];

$mailFrom = $_ENV['MAIL_FROM'];
$mailFromName = $_ENV['MAIL_FROM_NAME'];

$mailTo = $_ENV['MAIL_TO'];
$mailToNames = $_ENV['MAIL_TO_NAMES'];

if (!$mailHost || !$mailUsername || !$mailPassword || !$smtpEncryption || !$mailPort || !$mailFrom || !$mailTo) {
    die("Missing required mail configuration in environment variables.");
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $mailHost;
    $mail->SMTPAuth = true;
    $mail->Username = $mailUsername;
    $mail->Password = $mailPassword;
    $mail->SMTPSecure = $smtpEncryption;
    $mail->Port = $mailPort;

    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    $mail->setFrom($mailFrom, $mailFromName);

    $emails = explode(',', $mailTo);
    $names = explode(',', $mailToNames);

    foreach ($emails as $index => $email) {
        $name = $names[$index] ?? '';
        $mail->addAddress(trim($email), trim($name));
    }

    $mail->Subject = 'Delivery Plan Report - ' . date('Y-m-d');
    $mail->isHTML(true);
    $mail->Body = '
        <b>This is an E-Mail sent via PTPI - SYSTEM auto-generated mail. Please do not reply.</b><br><br>
        Dear Mr./Ms.,<br>
        Please see attached file for Delivery Plan ' . date('Y-m-d') . '.<br><br>
        <b>--</b><br><br>
        Best Regards,<br><br>
        <b>PRIMA TECH PHILS., INC.</b><br>
        <b>FG Management System (FGMS)</b>
    ';

    $excelData = fetchDataFromDataPhp('export_excel.php', false);
    $filename = date('Ymd_His') . "_Delivery_Plan.xlsx";

    $mail->addStringAttachment(
        $excelData,
        $filename,
        'base64',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

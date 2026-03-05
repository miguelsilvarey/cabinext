<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

const SMTP_HOST   = 'c2631962.ferozo.com';       // Verifícalo en Donweb
const SMTP_PORT   = 465;                       // 587 (STARTTLS) o 465 (SSL)
const SMTP_USER   = 'noreply@cabinext.llc';
const SMTP_PASS   = 'AAg*8vP9oT';
const TO_EMAIL    = 'cabinextllc@gmail.com';
const TO_NAME     = 'Ismael Méndez';

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('error');
}

$firstName = trim($_POST['firstName'] ?? '');
$lastName  = trim($_POST['lastName'] ?? '');
$email     = trim($_POST['email'] ?? '');
$message   = trim($_POST['message'] ?? '');

if ($firstName === '' || $lastName === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  exit('error');
}

// includes manuales
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;
  $mail->SMTPAuth   = true;
  $mail->Username   = SMTP_USER;
  $mail->Password   = SMTP_PASS;

  if (SMTP_PORT == 587) {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  } else {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  }
  $mail->Port       = SMTP_PORT;

  $mail->CharSet    = 'UTF-8';
  $mail->Encoding   = 'base64';

  $mail->setFrom(SMTP_USER, 'CABINEXT Website');
  $mail->addAddress(TO_EMAIL, TO_NAME);
  $mail->addReplyTo($email, $firstName . ' ' . $lastName);

  $subject = "New Contact Form Submission from {$firstName} {$lastName}";
  $mail->Subject = $subject;

  $safeMsg = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
  $mail->isHTML(true);
  $mail->Body = "
    <h3>Website Contact</h3>
    <p><strong>Name:</strong> {$firstName} {$lastName}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Message:</strong><br>{$safeMsg}</p>
  ";
  $mail->AltBody = "Name: {$firstName} {$lastName}\nEmail: {$email}\n\nMessage:\n{$message}";

  $mail->Sender = SMTP_USER;

  $mail->send();
  echo 'success';
} catch (Exception $e) {
  // error_log('Mailer Error: ' . $mail->ErrorInfo);
  http_response_code(500);
  echo 'error';
}

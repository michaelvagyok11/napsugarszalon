<?php
// mailer.php

require_once __DIR__ . '/config.php';
// Feltételezzük, hogy PHPMailer telepítve van (pl. via Composer autoload)
/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
*/

function sendEmail($toEmail, $toName, $subject, $bodyHtml, $bodyPlain = '') {
    // Egyszerű PHP mail() verzió (ha nincs PHPMailer), de éles környezethez ajánlott SMTP / PHPMailer.
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";

    return mail($toEmail, $subject, $bodyHtml, $headers);
}

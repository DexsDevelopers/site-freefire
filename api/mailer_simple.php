<?php
function mail_build_from()
{
    $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
    $host = preg_replace('/:\d+$/', '', $host);
    if ($host === '') $host = 'localhost';
    return "no-reply@" . $host;
}

function mail_send_with_pdf($to, $subject, $htmlBody, $pdfBytes, $pdfFilename, $fromEmail = null)
{
    $to = trim((string)$to);
    if ($to === '') return false;

    $fromEmail = trim((string)($fromEmail ?: mail_build_from()));
    $subject = (string)$subject;
    $htmlBody = (string)$htmlBody;
    $pdfFilename = (string)($pdfFilename ?: 'comprovante.pdf');
    $boundary = 'bnd_' . bin2hex(random_bytes(12));

    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'From: ' . $fromEmail;
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

    $body = '';
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $htmlBody . "\r\n\r\n";

    $body .= "--$boundary\r\n";
    $body .= "Content-Type: application/pdf; name=\"" . addslashes($pdfFilename) . "\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"" . addslashes($pdfFilename) . "\"\r\n\r\n";
    $body .= chunk_split(base64_encode($pdfBytes)) . "\r\n";
    $body .= "--$boundary--\r\n";

    return mail($to, $subject, $body, implode("\r\n", $headers));
}

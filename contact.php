<?php
declare(strict_types=1);

$recipient = 'help@digitmorph.gr';
$redirectUrl = 'index.html#contact';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirectUrl);
    exit;
}

function clean_input(?string $value): string
{
    $value = trim((string) $value);
    $value = str_replace(["\r", "\n"], ' ', $value);

    return $value;
}

$name = clean_input($_POST['name'] ?? '');
$company = clean_input($_POST['company'] ?? '');
$phone = clean_input($_POST['phone'] ?? '');
$subject = clean_input($_POST['_subject'] ?? 'Νέο μήνυμα από DIGITMORPH');
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $message === '') {
    header('Location: ' . $redirectUrl . '?status=error');
    exit;
}

$lines = [
    'Νέο αίτημα από τη φόρμα επικοινωνίας της DIGITMORPH',
    '',
    'Ονοματεπώνυμο: ' . $name,
    'Εταιρεία: ' . ($company !== '' ? $company : '-'),
    'Τηλέφωνο: ' . ($phone !== '' ? $phone : '-'),
    '',
    'Μήνυμα:',
    $message,
];

$body = implode(PHP_EOL, $lines);

$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: DIGITMORPH Website <no-reply@digitmorph.gr>',
    'Reply-To: ' . $recipient,
    'X-Mailer: PHP/' . phpversion(),
];

$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
$sent = mail($recipient, $encodedSubject, $body, implode("\r\n", $headers));

header('Location: ' . $redirectUrl . ($sent ? '?status=success' : '?status=error'));
exit;

<?php

if ($_SERVER['REMOTE_ADDR'] !== '##SOURCE_HOST##') {
    die();
}
if ($_SERVER['REQUEST_METHOD'] !== 'LOG') {
    die();
}
$headers = apache_request_headers();
if (!isset($headers['ANYTHINGGOES'])) {
    die();
}
if (!isset($headers['LOGGEDUSER'])) {
    die();
}
if ($headers['ANYTHINGGOES'] !== '##SOURCE_KEY##') {
    die();
}
$file = __DIR__.'/../remote_bash.'.preg_replace('/[^a-z0-9_-]+/i', '', $headers['LOGGEDUSER']).'.log';
$pdo = new PDO('##DATABASE_CONNECTION##');
$pdo->query("CREATE TABLE IF NOT EXISTS remote_logs {name TEXT,command TEXT,created TEXT}");
$data = explode("\n", file_get_contents('php://input'));
if ('##TARGET_FILTER##' === 'true') {
    $data = array_unique($data);
}
$stmt = $pdo->prepare('INSERT INTO remote_logs VALUES (:name, :command, :created)');
foreach ($data as $line) {
    $stmt->closeCursor();
    $stmt->execute([
        ':name' => $user,
        ':command' => trim($line),
        ':created' => date('Y-m-d H:i:s')
    ]);
}
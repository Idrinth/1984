<?php

use De\Idrinth\Project1984\Command;
use De\Idrinth\Project1984\Config;
use De\Idrinth\Project1984\FileSystem;
use De\Idrinth\Project1984\Secrets;

require_once(__DIR__ . '/../vendor/autoload.php');

(new Command(
    new Secrets(),
    new Config($argv),
    new FileSystem(__DIR__ . '/../src', __DIR__ . '/../dist')
))->run();
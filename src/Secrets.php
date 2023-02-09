<?php

namespace De\Idrinth\Project1984;

final class Secrets
{
    private $pass;
    private $ivPhp;
    private $ivSh;
    private $killKey;
    private $killName;
    private $api;
    private $logger;
    private $key;

    public function __construct()
    {
        $this->pass = randomAlphaNumericString(256);
        $this->ivPhp = randomAlphaNumericString(8);
        $this->ivSh = randomAlphaNumericString(8);
        $this->killKey = randomAlphaNumericString(512);
        $this->killName = 'KILL_ME_' . randomAlphaNumericString(12);
        $this->api = randomAlphaNumericString(12);
        $this->logger = randomAlphaNumericString(3);
        $this->key = randomAlphaNumericString(512);
    }
    public function getPass(): string
    {
        return $this->pass;
    }
    public function getIvPhp(): string
    {
        return $this->ivPhp;
    }
    public function getIvSh(): string
    {
        return $this->ivSh;
    }
    public function getKillKey(): string
    {
        return $this->killKey;
    }
    public function getKillName(): string
    {
        return $this->killName;
    }
    public function getApi(): string
    {
        return $this->api;
    }
    public function getLogger(): string
    {
        return $this->logger;
    }
    public function getKey(): string
    {
        return $this->key;
    }
}

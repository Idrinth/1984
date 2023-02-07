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
        //unlink(__DIR__ . '/api.php');
    }
    /**
     * @test
     */
    public function succeedsWithDuplicateRemoval(): void
    {
        $key = randomAlphaNumericString(9);
        $sqlite = __DIR__ . '/db.sqlite';
        file_put_contents(
            __DIR__ . '/api.php',
            str_replace(
                ['##SOURCE_HOST##', '##SOURCE_KEY##', '##DATABASE_CONNECTION##', '##TARGET_FILTER##', "'LOG'"],
                ['127.0.0.1', $key, "sqlite:$sqlite", 'true', "'POST'"],
                file_get_contents(dirname(__DIR__) . '/src/api.php')
            )
        );
        $user = randomAlphaNumericString(7);
        $api = Process::fromShellCommandline('php -S 127.0.0.1:8912 ' . __DIR__ . '/api.php');
        $api->start();
        $data = random_bytes(rand(100, 200));
        $c = curl_init();
        curl_setopt_array($c, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_URL => 'http://127.0.0.1:8912/api.php',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'CONTENT-TYPE: text/plain',
                "ANYTHINGGOES: $key",
                "LOGGEDUSER: $user"
            )
        ]);
        curl_exec($c);
        curl_close($c);
        $api->stop();
        self::assertEquals('', $api->getOutput());
        self::assertEquals('', $api->getErrorOutput());
        self::assertFileExists($sqlite);
        unlink($sqlite);
    }
}

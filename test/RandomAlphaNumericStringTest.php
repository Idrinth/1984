<?php

namespace De\Idrinth\Project1984;

use PHPUnit\Framework\TestCase;

final class RandomAlphaNumericStringTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once __DIR__ . '/../src/randomAlphaNumericString.php';
    }
    public static function provideLengths(): array
    {
        return [
            [0],
            [1],
            [rand(0,100)],
            [rand(100,200)],
        ];
    }
    /**
     * @test
     * @dataProvider provideLengths
     */
    public function generatesAlphaNumnericStringOfDesiredLength(int $length): void
    {
        $string = randomAlphaNumericString($length);
        self::assertMatchesRegularExpression('/^[a-zA-Z0-9]*$/', $string);
        self::assertEquals($length, strlen($string));
    }
}

<?php

declare(strict_types=1);

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class UnifySectionCommentFixerTest extends AbstractFixerTestCase
{
    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array<array{
     *   0: string,
     *   1?: ?string
     * }>
     */
    public static function provideFixCases(): array
    {
        return [
            [
                '<?php
// -- Arrange',
                '<?php
// Arrange',
            ],
        ];
    }
}

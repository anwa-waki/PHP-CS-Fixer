<?php

declare(strict_types=1);

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class UnifySectionCommentFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure(['abc']);
    }

    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * The first value is the expected output.
     * The second value is the input. If it is null, the fixer will expect it to be equal to the expected output.
     * So this is for testing that nothing was changed.
     * The third value is the configuration for the fixer.
     *
     * @return array<array{
     *   0: string,
     *   1?: ?string,
     *   2: array<string, array<string, string>>
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
                ['section_names' => ['Arrange']],
            ],
            [
                '<?php
// -- Act & Assert',
                '<?php
// Act & Assert',
                ['section_names' => ['Arrange', 'Act & Assert']],
            ],
            [
                '<?php
// -- Arrange',
                null,
                ['section_names' => ['Arrange']],
            ],
            [
                '<?php
// Hurz',
                null,
                ['section_names' => ['Arrange']],
            ],
            [
                '<?php
/* Arrange */',
                null,
                ['section_names' => ['Arrange']],
            ],
        ];
    }
}

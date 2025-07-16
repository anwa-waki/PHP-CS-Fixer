<?php

declare(strict_types=1);

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @psalm-suppress InternalClass
 *
 * @psalm-type InputConfig array{
 *  section_names?: list<string>,
 * }
 * @psalm-type ComputedConfig array{
 *  section_names: list<string>,
 * }
 *
 * @template-implements ConfigurableFixerInterface<InputConfig, ComputedConfig>
 */
final class UnifySectionCommentFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /** @use ConfigurableFixerTrait<InputConfig, ComputedConfig> */
    use ConfigurableFixerTrait;

    /** @var list<string> */
    private array $sectionNamesToFix;

    #[\Override]
    public function getName(): string
    {
        return 'UnifySectionCommentFixer/unify_section_comments';
    }

    #[\Override]
    public function getDefinition(): FixerDefinition
    {
        return new FixerDefinition(
            'Changes section comments to a unified format.',
            [
                new CodeSample(
                    "<?php
// Section\n",
                ),
                new CodeSample(
                    "<?php
// Arrange\n",
                    ['section_names' => ['Arrange']],
                ),
                new CodeSample(
                    "<?php
// Act & Assert\n",
                    ['section_names' => ['Arrange', 'Act & Assert']],
                ),
            ],
        );
    }

    #[\Override]
    public function getPriority(): int
    {
        // Should be run extremely late, after all other comment fixers
        return -999;
    }

    #[\Override]
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    protected function configurePostNormalisation(): void
    {
        $this->sectionNamesToFix = $this->configuration['section_names'] ?? [];
    }

    #[\Override]
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        /**
         * @psalm-var int $index
         * @psalm-var Token $token
         */
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            $content = $token->getContent();

            if (!$this->isCommentAFixableSectionName($content)) {
                continue;
            }

            $newContent = $this->insertDashes($content);
            $tokens[$index] = new Token([T_COMMENT, $newContent]);
        }
    }

    private function isCommentAFixableSectionName(string $content): bool
    {
        $commentPart = mb_substr($content, 0, 3);
        $sectionName = mb_substr($content, 3); // The comment without the leading '// '

        return $commentPart === '// '
               && in_array($sectionName, $this->sectionNamesToFix, true);
    }

    private function insertDashes(string $content): string
    {
        $sectionName = mb_substr($content, 3); // The comment without the leading '// '

        return sprintf('// -- %s', $sectionName);
    }

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('section_names', 'List of section names to fix. i.e. Arrange, Act, Validate etc.'))
                ->setAllowedTypes(['string[]'])
                ->setDefault(['Section']) // PHP-CS-Fixer requires you to have a default to pass the tests. And that default must be the first CodeSample and that CodeSample must lead to a fix... So I have to use some nonsense default value to satisfy that requirement.
                ->getOption(),
        ]);
    }
}

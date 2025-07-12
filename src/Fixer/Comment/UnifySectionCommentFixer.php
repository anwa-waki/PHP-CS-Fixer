<?php

declare(strict_types=1);

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class UnifySectionCommentFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinition
    {
        return new FixerDefinition(
            'Changes section comments to a unified format.',
            [
                new CodeSample(
                    "<?php
// Arrange\n"
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            $content = $token->getContent();
            if ($this->doesContentAlreadyMatchSectionCommentFormat($content)) {
                continue;
            }

            $newContent = $this->insertDashes($content);
            $tokens[$index] = new Token([T_COMMENT, $newContent]);
        }
    }

    private function doesContentAlreadyMatchSectionCommentFormat(string $content): bool
    {
        return Preg::match('/^\/\/\s--\s.+/', $content);
    }

    private function insertDashes(string $content): string
    {
        $sectionName = substr($content, 3); // The comment without the leading '// '

        return sprintf('// -- %s', $sectionName);
    }
}

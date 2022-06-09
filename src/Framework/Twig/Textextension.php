<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Rajoute les extensions liées au textes
 */
class TextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'getExcerpt'])
        ];
    }

    public function getExcerpt($content, ?int $maxlength = 100): string
    {
        if (mb_strlen($content) > $maxlength) {
            $excerpt = mb_substr($content, 0, $maxlength);
            $lastSpace = strrpos($excerpt, ' ');
            return  mb_substr($excerpt, 0, $lastSpace) . '...';
        }

        return $content;
    }
}

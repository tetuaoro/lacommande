<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('regex_replace', [$this, 'regex']),
            new TwigFilter('truncate', [$this, 'text_truncate']),
            new TwigFilter('uniqueG', [$this, 'unique_group']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_env', [$this, 'getEnv']),
        ];
    }

    public function regex($subject, $pattern, $replace)
    {
        return preg_replace($pattern, $replace, $subject);
    }

    public function getEnv($envName)
    {
        return $_ENV[$envName];
    }

    public function text_truncate($subject, $max = 26, $ellipsis = '...')
    {
        $match = trim($subject);

        return substr($match, 0, $max).$ellipsis;
    }

    public function unique_group($subject)
    {
        return array_unique($subject->getItems(), SORT_REGULAR);
    }
}

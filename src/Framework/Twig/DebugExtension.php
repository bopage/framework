<?php

namespace Framework\Twig;

use Symfony\Component\VarDumper\VarDumper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DebugExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('debug', [$this, 'debug'])
        ];
    }

    public function debug(...$vars)
    {
        $result = VarDumper::dump($vars);
        return $result;
    }
}

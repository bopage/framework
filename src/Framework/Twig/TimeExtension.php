<?php

namespace Framework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(DateTime $date, string $format = 'Y/d/m H:i'):string
    {
        return "<span class='timeago' datetime='" . $date->format(Datetime::ISO8601) . "' >" .
                $date->format($format) . "</span>";
    }
}

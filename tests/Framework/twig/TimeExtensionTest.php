<?php

namespace Tests\Framework\Twig;

use DateTime;
use Framework\Twig\TimeExtension;
use PHPUnit\Framework\TestCase;

class TimeExtensionTest extends TestCase
{
    /**
     * extension
     *
     * @var TimeExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new TimeExtension;
    }

    public function testDateFormat()
    {
        $date = new DateTime();
        $format = 'Y/d/m H:i';
        $result = "<span class='timeago' datetime='" . $date->format(Datetime::ISO8601) . "' >" .
            $date->format($format) . "</span>";
        $this->assertEquals($result, $this->extension->ago($date));
    }
}

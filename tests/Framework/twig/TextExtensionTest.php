<?php

namespace Tests\Framework\Twig;

use Framework\Twig\TextExtension;
use PHPUnit\Framework\TestCase;

class TextExtensionTest extends TestCase
{

    
    /**
     * extension
     *
     * @var TextExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new TextExtension;
    }

    public function testExcerptWithShortText()
    {
        $text = 'salut';
        $this->assertEquals($text, $this->extension->getExcerpt($text, 10));
    }

    public function testExcerptWithLongText()
    {
        $text = 'salut les gens';
        $this->assertEquals('salut...', $this->extension->getExcerpt($text, 7));
        $this->assertEquals('salut les...', $this->extension->getExcerpt($text, 12));
    }
}

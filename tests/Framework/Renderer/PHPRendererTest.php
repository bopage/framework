<?php

namespace Tests\Framework\Renderer;

use Framework\Renderer\PHPRenderer;
use PHPUnit\Framework\TestCase;

class PHPRendererTest extends TestCase
{

    
    /**
     * renderer
     *
     * @var PHPRenderer
     */
    private $renderer;

    protected function setUp(): void
    {
        $this->renderer = new PHPRenderer(__DIR__ . '/views');
    }

    public function testRenderTheRightPath()
    {
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Salut les gens', $content);
    }

    public function testRenderTheDefaultPath()
    {
        $content = $this->renderer->render('demo');
        $this->assertEquals('Salut les gens', $content);
    }
    
    public function testRenderWithParams()
    {
        $content = $this->renderer->render('demoParams', ['prenom' => 'marc']);
        $this->assertEquals('Salut marc', $content);
    }

    public function testaddGlobal()
    {
        $this->renderer->addGlobal('prenom', 'marc');
        $content = $this->renderer->render('demoParams');
        $this->assertEquals('Salut marc', $content);
    }
}

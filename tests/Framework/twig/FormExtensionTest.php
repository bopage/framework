<?php

namespace Tests\Framework\Twig;

use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

class FormExtensionTest extends TestCase
{


    /**
     * formExtension
     *
     * @var FormExtension
     */
    private $formExtension;

    protected function setUp(): void
    {
        $this->formExtension = new FormExtension;
    }

    public function trim(string $string)
    {
        $lines = explode(PHP_EOL, $string);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }

    public function assertSimilar(string $expected, string $actual)
    {
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }

    public function testFieldWithError()
    {
        $context = ['errors' => ['name' => 'erreur']];
        $html = $this->formExtension->field($context, 'name', 'Demo', 'Titre');
        $this->assertSimilar("<div class='mb-3'>
            <label for='name' class='form-label'>Titre</label>
            <input type='text' class='form-control is-invalid' name='name' id='name' value='Demo'>
            <div class='invalid-feedback'>erreur</div></div>", $html);
    }

    public function testField()
    {
        $html = $this->formExtension->field([], 'name', 'Demo', 'Titre');
        $this->assertSimilar("<div class='mb-3'>
            <label for='name' class='form-label'>Titre</label>
            <input type='text' class='form-control' name='name' id='name' value='Demo'>
            </div>", $html);
    }

    public function testTextarea()
    {
        $html = $this->formExtension->field([], 'name', 'Demo', 'Titre', ['type' => 'textarea']);
        $this->assertSimilar("<div class='mb-3'>
            <label for='name' class='form-label'>Titre</label>
            <textarea type='text' class='form-control' name='name' id='name' rows='5'>Demo</textarea>
            </div>", $html);
    }

    public function testFieldWithClass()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            'Demo',
            'Titre',
            ['class' => 'demo']
        );
        $this->assertSimilar("<div class='mb-3'>
            <label for='name' class='form-label'>Titre</label>
            <input type='text' class='form-control demo' name='name' id='name' value='Demo'>
            </div>", $html);
    }

    public function testSelect()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            2,
            'Titre',
            ['options' => ['1' => 'demo1', '2' => 'demo2']]
        );
        $this->assertSimilar("<div class='mb-3'>
            <label for='name' class='form-label'>Titre</label>
            <select class='form-control' name='name' id='name'>
            <option value='1'>demo1</option><option value='2' selected>demo2</option>
            </select>
            </div>", $html);
    }
}

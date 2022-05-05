<?php

namespace App\Admin;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminTwigExtension extends AbstractExtension
{
    private $widgets;

    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_menu', [$this, 'renderMenu'], ['is_safe' => ['html']])
        ];
    }
    
    /**
     * Permet de render le code html du menu de l'administration
     *
     * @return string
     */
    public function renderMenu(): string
    {
        return array_reduce($this->widgets, function ($html, AdminWidgetInterface $widgets) {
            return $html . $widgets->renderMenu();
        }, '');
    }
}

<?php

namespace App\Admin;

use Framework\Renderer\RendererInterface;

class DashboardAction
{
    private $renderer;

    private $widgets;

    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    public function __invoke()
    {
        $widgets = array_reduce($this->widgets, function ($html, AdminWidgetInterface $widgets) {
            return $html . $widgets->render();
        }, '');
        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }
}

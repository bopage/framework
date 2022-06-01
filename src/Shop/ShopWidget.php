<?php
namespace App\Shop;

use App\Admin\AdminWidgetInterface;
use App\shop\Table\ProductTable;
use Framework\Renderer\RendererInterface;

class ShopWidget implements AdminWidgetInterface
{
    
    private $renderer;

    private $Table;

    public function __construct(RendererInterface $renderer, ProductTable $Table)
    {
        $this->renderer = $renderer;
        $this->Table = $Table;
    }

    public function render(): string
    {
        $count = $this->Table->count();
        return $this->renderer->render('@shop/admin/widget', compact('count'));
    }
    
    public function renderMenu(): string
    {
        return $this->renderer->render('@shop/admin/menu');
    }
}

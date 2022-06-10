<?php
namespace App\Shop;

use App\Admin\AdminWidgetInterface;
use App\shop\Table\ProductTable;
use App\shop\Table\PurchaseTable;
use Framework\Renderer\RendererInterface;

class ShopWidget implements AdminWidgetInterface
{
    
    private $renderer;

    private $Table;
    private $purchaseTable;

    public function __construct(RendererInterface $renderer, ProductTable $Table, PurchaseTable $purchaseTable)
    {
        $this->renderer = $renderer;
        $this->Table = $Table;
        $this->purchaseTable = $purchaseTable;
    }

    public function render(): string
    {
        $count = $this->Table->count();
        $total = $this->purchaseTable->getMonthRevenue();
        return $this->renderer->render('@shop/admin/widget', compact('count', 'total'));
    }
    
    public function renderMenu(): string
    {
        return $this->renderer->render('@shop/admin/menu');
    }
}

<?php
namespace App\Shop\Action;

use App\Shop\Entity\Product;
use App\Shop\Exception\AlreadyPurcharsedException;
use App\Shop\PurchaseProduct;
use App\shop\Table\ProductTable;
use Framework\Action\RouterAwareAction;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class PurchaseProcessAction
{
    private $productTable;
    private $purchaseProduct;
    private $auth;
    private $router;
    private $flashService;

    use RouterAwareAction;

    public function __construct(
        ProductTable $productTable,
        PurchaseProduct $purchaseProduct,
        Auth $auth,
        Router $router,
        FlashService $flashService
    ) {
        $this->productTable = $productTable;
        $this->purchaseProduct = $purchaseProduct;
        $this->auth = $auth;
        $this->router = $router;
        $this->flashService = $flashService;
    }
}

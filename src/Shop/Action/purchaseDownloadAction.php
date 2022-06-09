<?php
namespace App\Shop\Action;

use App\Shop\Entity\Product;
use App\shop\Table\ProductTable;
use App\shop\Table\PurchaseTable;
use Framework\Auth;
use Framework\Auth\ForbiddenException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class PurchaseDownloadAction
{
    private $productTable;
    private $purchaseTable;
    private $auth;

    public function __construct(ProductTable $productTable, PurchaseTable $purchaseTable, Auth $auth)
    {
        $this->productTable = $productTable;
        $this->purchaseTable = $purchaseTable;
        $this->auth = $auth;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        /** @var Product */
        $product = $this->productTable->find((int)$request->getAttribute('id'));
        $user = $this->auth->getUser();
        if ($this->purchaseTable->findFor($product, $user)) {
            $source = fopen('download/' . $product->getPdf(), 'r');
            return new Response(200, ['content-type' => 'application/pdf'], $source);
        } else {
            throw new ForbiddenException();
        }
    }
}

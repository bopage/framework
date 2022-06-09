<?php
namespace App\shop\Table;

use App\Auth\User;
use App\Shop\Entity\Product;
use App\Shop\Entity\Purchase;
use Framework\Database\Table;

/**
 * PurchaseTable table achat
 */
class PurchaseTable extends Table
{
    protected $table = Purchase::class;
    protected $entity = 'purchases';
    
    /**
     * Récupère l'achat correspondant à un utilisateur
     *
     * @param  Product $product
     * @param  User $user
     * @return Purchase|null
     */
    public function findFor(Product $product, User $user): ?Purchase
    {
        return $this->makeQuery()
                ->where('product_id :product AND user_id :user')
                ->params(['user' => $user->getId(), 'product' => $product->getId()])
                ->fetch() ?: null;
    }
}

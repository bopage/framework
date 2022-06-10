<?php

namespace App\shop\Table;

use App\Auth\User;
use App\Shop\Entity\Product;
use App\Shop\Entity\Purchase;
use Framework\Database\QueryResult;
use Framework\Database\Table;

/**
 * PurchaseTable table achat
 */
class PurchaseTable extends Table
{
    protected $entity = Purchase::class;
    protected $table = 'purchases';

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
            ->where('product_id = :product AND user_id = :user')
            ->params(['user' => $user->getId(), 'product' => $product->getId()])
            ->fetch() ?: null;
    }

    /**
     * Récupère les achats correspondant à l'utilisateur
     *
     * @param  User $user
     * @return QueryResult
     */
    public function findForUser(User $user): QueryResult
    {
        return $this->makeQuery()
            ->select('p.*', 'pr.name as product_name')
            ->where('p.user_id = :user')
            ->join('products as pr', ' pr.id = p.product_id')
            ->params(['user' => $user->getId()])
            ->fetchAll();
    }

    /**
     * Récupère l'achat et le produit en question
     *
     * @param  int $purchaseId
     * @return Purchase
     */
    public function findWithProduct(int $purchaseId): Purchase
    {
        return $this->makeQuery()
            ->select('p.*', 'pr.name as product_name')
            ->where("p.id = $purchaseId")
            ->join('products as pr', ' pr.id = p.product_id')
            ->fetchOrFail();
    }
    
    /**
     * Récupère la somme total hors taxe par mois
     *
     */
    public function getMonthRevenue()
    {
        return $this->makeQuery()
            ->select('SUM(price)')
            ->where("p.created_at BETWEEN DATE_SUB(NOW(), INTERVAL 1 MONTH) AND NOW()")
            ->fetchColumn();
    }
}

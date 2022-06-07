<?php
namespace App\shop\Table;

use App\Shop\Entity\Product;
use Framework\Database\Table;

/**
 * ProductTable table des produits
 */
class ProductTable extends Table
{
    protected $entity = Product::class;
    protected $table = 'products';
}

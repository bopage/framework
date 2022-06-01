<?php
namespace App\shop\Table;

use App\Shop\Entity\Product;
use Framework\Database\Table;

class ProductTable extends Table
{
    protected $entity = Product::class;
    protected $table = 'products';
}

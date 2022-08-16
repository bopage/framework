<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrderTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $constraints = ['delete' => 'cascade'];
        $this->table('orders')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id')
            ->addColumn('price', 'float', ['precision' => 10, 'scale' => 2])
            ->addColumn('vat', 'float', ['precision' => 10, 'scale' => 2])
            ->addColumn('country', 'string')
            ->addColumn('created_at', 'datetime')
            ->addColumn('stripe_id', 'string')
            ->create();

        $this->table('orders_products')
            ->addColumn('order_id', 'integer')
            ->addColumn('product_id', 'integer')
            ->addColumn('price', 'float', ['precision' => 10, 'scale' => 2])
            ->addColumn('quantity', 'integer', ['default' => 1])
            ->addForeignKey('order_id', 'orders', 'id', $constraints)
            ->addForeignKey('product_id', 'products', 'id', $constraints)
            ->create();

      //  $this->table('purchases')->drop();
    }
}

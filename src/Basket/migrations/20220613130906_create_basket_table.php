<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBasketTable extends AbstractMigration
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
        $this->table('baskets')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id', $constraints)
            ->create();

        $this->table('baskets_products')
            ->addColumn('basket_id', 'integer')
            ->addColumn('product_id', 'integer')
            ->addColumn('quantity', 'integer', ['default' => 1])
            ->addForeignKey('basket_id', 'baskets', 'id', $constraints)
            ->addForeignKey('product_id', 'products', 'id', $constraints)
            ->create();
    }
}

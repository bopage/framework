<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class CreateProducts extends AbstractMigration
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
        $this->table('products')
            ->addColumn('name', 'string')
            ->addColumn('slug', 'string')
            ->addColumn('description', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('image', 'string')
            ->addColumn('price', 'float', ['precision' => 10, 'scale' => 2])
            ->addColumn('updated_at', 'datetime')
            ->addColumn('created_at', 'datetime')
            ->addIndex('slug', ['unique' => true])
            ->create();
    }
}

<?php


use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $this->table('users')
            ->insert([
                'username' => 'admin',
                'email' => 'admin@admin.fr',
                'firstname' => 'admin',
                'lastname' => 'admin',
                'password' => password_hash('admin', PASSWORD_DEFAULT),
                'password_reset' => password_hash('admin', PASSWORD_DEFAULT),
                'password_reset_at' => date('Y-m-d H:i:s')
            ])->save();
    }
}

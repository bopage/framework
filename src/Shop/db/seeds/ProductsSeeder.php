<?php

use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class ProductsSeeder extends AbstractSeed
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
        //Seeding des produits
        $data = [];
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $date = $faker->unixTime('now');
            $data[] = [
                'name' => $faker->CatchPhrase,
                'slug' => $faker->slug(),
                'description' => $faker->text(3000),
                'image' => $faker->imageUrl(640, 480, 'animals', true),
                'price' => $faker->randomNumber(4, true),
                'created_at' => date('Y-m-d H:i:s', $date),
                'updated_at' => date('Y-m-d H:i:s', $date),
            ];
        }
        $this->table('products')
            ->insert($data)
            ->save();
    }
}

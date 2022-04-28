<?php

namespace Tests\App\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Tests\DatabaseTest;

class PostTableTest extends DatabaseTest
{

    
    /**
     * PostTable
     *
     * @var PostTable
     */
    private $postTable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postTable = new PostTable($this->pdo);
    }

    public function testFind()
    {
        $this->seedDatabase();
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotRecord()
    {
        $post = $this->postTable->find(1);
        $this->assertNull($post);
    }

    public function testUpdate()
    {
        $this->seedDatabase();
        $this->postTable->update(1, ['name' => 'name', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('name', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testInsert()
    {
        $this->postTable->insert([
            'name' => 'name',
            'slug' => 'demo',
            'content' => 'demo',
            'updated_at' => '2000/12/12 20:05',
            'created_at' => '2000/12/12 20:05'
        ]);
        $post = $this->postTable->find(1);
        $this->assertEquals('name', $post->name);
        $this->assertEquals('demo', $post->slug);
        $this->assertEquals('demo', $post->content);
    }

    public function testDelete()
    {
        $this->postTable->insert([
            'name' => 'name',
            'slug' => 'demo',
            'content' => 'demo',
            'updated_at' => '2000/12/12 20:05',
            'created_at' => '2000/12/12 20:05'
        ]);
        $this->postTable->insert([
            'name' => 'name',
            'slug' => 'demo',
            'content' => 'demo',
            'updated_at' => '2000/12/12 20:05',
            'created_at' => '2000/12/12 20:05'
        ]);

        $count = $this->pdo->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, $count);

        $this->postTable->delete($this->pdo->lastInsertId());
        $count = $this->pdo->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, $count);
    }
}

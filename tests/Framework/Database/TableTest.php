<?php

namespace Tests\Framework\Database;

use Framework\Database\Table;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

class TableTest extends TestCase
{

    
    /**
     * table
     *
     * @var Table
     */
    private $table;

    protected function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);

        $pdo->exec('CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name varchar (255)
        )');

        $this->table = new Table($pdo);
        $reflection = new ReflectionClass($this->table);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($this->table, 'test');
    }

    public function testFind()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        $test = $this->table->find(1);
        $this->assertInstanceOf(stdClass::class, $test);
        $this->assertEquals('a1', $test->name);
    }

    public function testFindList()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        $this->assertEquals(['1' => 'a1', '2' => 'a2'], $this->table->findList());
    }

    public function testFindAll()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        $categories = $this->table->findAll()->fetchAll();
        $this->assertCount(2, $categories);
        $this->assertInstanceOf(stdClass::class, $categories[0]);
        $this->assertEquals('a1', $categories[0]->name);
        $this->assertEquals('a2', $categories[1]->name);
    }

    public function testFindBy()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a3')");
        $categorie = $this->table->findBy('name', 'a1');
        $this->assertInstanceOf(stdClass::class, $categorie);
        $this->assertEquals(1, $categorie->id);
    }

    public function testExist()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        $this->assertTrue($this->table->exist('1'));
        $this->assertTrue($this->table->exist('2'));
        $this->assertFalse($this->table->exist('3'));
    }

    public function testCount()
    {
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a1')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a2')");
        $this->table->getPdo()->exec("INSERT INTO test (name) VALUES ('a3')");
        $this->assertEquals(3, $this->table->count());
    }
}

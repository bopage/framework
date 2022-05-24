<?php

namespace Tests\Framework;

use Framework\Validator;
use Tests\DatabaseTest;

class ValidatorTest extends DatabaseTest
{
    private function makeValidator(array $params)
    {
        return new Validator($params);
    }

    public function testRequiredFail()
    {
        $errors = ($this->makeValidator([
            'name' => 'Joe'
        ]))
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testNotEmpty()
    {
        $errors = ($this->makeValidator([
            'name' => 'Joe',
            'content' => ""
        ]))
            ->notEmpty('content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testRequiredSuccess()
    {
        $errors = ($this->makeValidator([
            'name' => 'Joe'
        ]))
            ->required('name')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testSlugSuccess()
    {
        $errors = ($this->makeValidator([
            'slug' => 'zra-sazelug-7test',
            'slug1' => 'fzefqfezq'
        ]))
            ->slug('slug')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testSlugError()
    {
        $errors = ($this->makeValidator([
            'slug' => 'mon-slug-7tAAst',
            'slug1' => 'mon-slug_7test',
            'slug2' => 'mon-_slug-7test',
            'slug4' => 'mons-slug-detest-'
        ]))
            ->slug('slug')
            ->slug('slug1')
            ->slug('slug2')
            ->slug('slug4')
            ->getErrors();
        $this->assertEquals(['slug', 'slug1', 'slug2', 'slug4'], array_keys($errors));
    }

    public function testLength()
    {
        $params = ['slug' => '123456789'];
        $this->assertCount(
            0,
            $this->makeValidator($params)->length('slug', 3)->getErrors()
        );
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champ slug doit contenir plus de 12 caractères', (string)$errors['slug']);
        $this->assertCount(
            1,
            $this->makeValidator($params)->length('slug', null, 7)->getErrors()
        );
        $this->assertCount(
            0,
            $this->makeValidator($params)->length('slug', null, 12)->getErrors()
        );
        $this->assertCount(
            0,
            $this->makeValidator($params)->length('slug', 3, 15)->getErrors()
        );
        $this->assertCount(
            1,
            $this->makeValidator($params)->length('slug', 3, 6)->getErrors()
        );
    }

    public function testDatetime()
    {
        $this->assertCount(
            0,
            $this->makeValidator([
                'date' => '2012-12-12 12:04:04'
            ])->datetime('date')->getErrors()
        );
        $this->assertCount(
            1,
            $this->makeValidator([
                'date' => '2012-13-12 12:04:04'
            ])->datetime('date')->getErrors()
        );
        $this->assertCount(
            0,
            $this->makeValidator([
                'date' => '2012-12-12 00:00:00'
            ])->datetime('date')->getErrors()
        );
        $this->assertCount(
            1,
            $this->makeValidator([
                'date' => '2013-02-29 12:04:04'
            ])->datetime('date')->getErrors()
        );
    }

    public function testExist()
    {
        $pdo = $this->getPDO();
        $pdo->exec('CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name varchar (255)
        )');
        $pdo->exec("INSERT INTO test (name) VALUES ('a1')");
        $pdo->exec("INSERT INTO test (name) VALUES ('a2')");
        $this->assertTrue($this->makeValidator(['category' => '1'])->exist('category', 'test', $pdo)->isValid());
        $this->assertFalse($this->makeValidator(['category' => '12'])->exist('category', 'test', $pdo)->isValid());
    }


    public function testUnique()
    {
        $pdo = $this->getPDO();
        $pdo->exec('CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name varchar (255)
        )');
        $pdo->exec("INSERT INTO test (name) VALUES ('a1')");
        $pdo->exec("INSERT INTO test (name) VALUES ('a2')");
        $this->assertFalse($this->makeValidator(['name' => 'a2'])->unique('name', 'test', $pdo, 1)->isValid());
        $this->assertFalse($this->makeValidator(['name' => 'a1'])->unique('name', 'test', $pdo)->isValid());
        $this->assertTrue($this->makeValidator(['name' => 'a111'])->unique('name', 'test', $pdo)->isValid());
        $this->assertTrue($this->makeValidator(['name' => 'a1'])->unique('name', 'test', $pdo, 1)->isValid());
    }

    public function testEmail()
    {
        $this->assertTrue($this->makeValidator(['email' => 'demo@demo.com'])->email('email')->isValid());
        $this->assertFalse($this->makeValidator(['email' => 'a1'])->email('email')->isValid());
    }
}

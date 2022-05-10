<?php

use Core\Database\QueryBuilder;
use PHPUnit\Framework\TestCase;
class QueryBuilderTest extends TestCase {
    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->queryBuilder = new QueryBuilder();
    }

    public function test_select(){
        $query = $this->queryBuilder->select('*')->from('test')->get();
        $this->assertEquals("SELECT * FROM test WHERE 1+1 ", $query);
    }

    public function test_select_with_where(){
        $query = $this->queryBuilder->select('*')->from('test')->where('k', 'v')->like('k2', '%jgj%')->get();
        $this->assertStringContainsString("SELECT * FROM test WHERE 1+1 AND k=:", $query);
    }
    public function test_select_with_where_multiple(){
        $query = $this->queryBuilder->select('*')->from('test')->where(['n'=>'v'])->where('k2', 'v2')->whereNull('k3')->whereNotNull('k4')->get();
        $this->assertStringContainsString("SELECT * FROM test WHERE 1+1 AND n=:", $query);
    }
    public function test_select_with_or_where_multiple(){
        $query = $this->queryBuilder->select('*')->from('test')->where("n='v'")->orWhere('k2', 'v2')->where(['k3'=>"v3"])->andWhere(['k4'=>'v4'])->get();
        $this->assertStringContainsString("SELECT * FROM test WHERE 1+1 AND n='v'", $query);
    }

    public function test_update(){
        $query = $this->queryBuilder->update('test')->set(['k'=>'v'])->where('id=5')->get();
        $this->assertStringContainsString("UPDATE test SET k=:", $query);
    }

    public function test_get_array_key(){
        $key = ["1", '2', 'k'=>'v'];
        $keys = json_encode(array_keys($key));
        echo "{$keys}\n";
        $this->assertTrue(true);

    }

    public function test_insert_simple(){
        $query = $this->queryBuilder->insert(['1', '2', '3', '4'])->into('test')->get();
        $this->assertStringContainsString("INSERT INTO test VALUES (:", $query);
    }

    public function test_insert_key_values(){
        $query = $this->queryBuilder->insert(['id'=>'1','name'=> 'testvalueString','email'=> '3','password'=> '4'])->into('test')->save();
        $this->assertStringContainsString("INSERT INTO test (id , name , email , password) VALUES (:", $query);
    }
}
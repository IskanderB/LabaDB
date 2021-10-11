<?php

class DBaseTest extends \PHPUnit\Framework\TestCase
{
    private $DBase;

    protected function setUp(): void
    {
        $this->DBase = new \App\Models\DB();
    }

    protected function tearDown(): void
    {

    }

    public function testCreate(){
        $this->assertEquals("Hello Test!", $this->DBase->create('test'));
    }


}

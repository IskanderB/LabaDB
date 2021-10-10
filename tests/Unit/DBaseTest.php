<?php

class DBaseTest extends \PHPUnit\Framework\TestCase
{
    private $DBase;

    protected function setUp(): void
    {
        $this->DBase = new \App\Models\DBase();
    }

    protected function tearDown(): void
    {

    }

    public function testHello(){
        $this->assertEquals("Hello Test!", $this->DBase->test());
    }


}

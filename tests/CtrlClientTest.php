<?php

use ExpressPHP\models\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {

    private $Client;

    protected function setUp() {
        $this->Client = new Client(1, 'Test Client');
    }

    public function test__construct() {}

    public function testStaticConstruct() {

    }

    public function testGetClientId() {
        $this->assertTrue($this->Client->getClientId() == 1, 'it should work');
    }

    public function testSetClientId() {
        $this->expectException(InvalidArgumentException::class);
        $this->Client->setClientId(-1);
        $this->Client->setClientId('invalid');
    }

    public function testGetName() {

    }

    public function testSetName() {

    }

    public function testSave() {

    }

    public function testFindAll() {

    }

    public function testFindOne() {

    }

    public function testUpdateOne() {

    }

}

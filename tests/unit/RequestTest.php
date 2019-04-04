<?php

use ExpressPHP\core\Server;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

    private $request;

    /**
     * @BeforeClass
     */
    protected function setUp() {
        $server = new Server(
            'server.com/api/index.php/auth?name=pete',
            'GET',
            'auth',
            ['username' => 'pete@mail.com'],
            ['file' => 'Some file'],
            ['cookieKey' => 'Some cookie value'],
            [
                'host' => 'server.com',
                'connection' => 'keep-alive',
                'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                'accept-language' => 'en-US,en;q=0.9',
                'x-access-token' => 'asdasdefwefefsdasd'
            ],
            'HTTP'
        );
        $this->request = $server->getRequest();
    }

    public function testURL() {
        $this->assertEquals($this->request->getOriginalUrl(), 'server.com/api/index.php/auth?name=pete');
    }

    public function testMethod() {
        $this->assertEquals($this->request->getMethod(), 'GET');
    }

    public function testPath() {
        $this->assertEquals($this->request->getPath(), 'auth');
    }

    public function testQuery() {
        $this->assertEquals($this->request->getQuery(), ['name' => 'pete']);
    }

    public function testBody() {
        $this->assertEquals($this->request->getBody(), ['username' => 'pete@mail.com']);
    }

    public function testFiles() {
        $this->assertEquals($this->request->getFiles(), ['file' => 'Some file']);
    }

    public function testCookie() {
        $this->assertEquals($this->request->getCookie(), ['cookieKey' => 'Some cookie value']);
    }

    public function testHeaders() {
        $this->assertEquals($this->request->getHeaders(), [
            'host' => 'server.com',
            'connection' => 'keep-alive',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'accept-language' => 'en-US,en;q=0.9',
            'x-access-token' => 'asdasdefwefefsdasd'
        ]);
    }

    public function testProtocol() {
        $this->assertEquals($this->request->getProtocol(), 'HTTP');
    }

}
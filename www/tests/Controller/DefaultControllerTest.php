<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class DefaultControllerTest extends TestCase 
{
    public function test_if_i_get_json()
    {
        $adapter = new Client(['base_uri' => 'http://localhost:4444']);
        $request = new Request('GET', '/teste');
        try {
            $response = $adapter->sendRequest($request);
        } catch (\Psr\Http\Client\ClientExceptionInterface $e) {
            exit();
        }
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(in_array('application/json;charset=utf-8', $response->getHeader('Content-type')));
        $this->assertNotEquals('{"key":"value"}', $response->getBody()->getContents());
    }
}

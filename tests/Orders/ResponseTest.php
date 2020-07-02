<?php

namespace Tests\Omnipay\PayPal\Orders;

use Omnipay\Tests\TestCase;
use Omnipay\PayPal\Message\OrdersResponse;

class ResponseTest extends TestCase
{
    public function testCompletePurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('RestCompletePurchaseSuccess.txt');
        $data = json_decode($httpResponse->getBody()->getContents(), true);

        $response = new OrdersResponse($this->getMockRequest(), $data, $httpResponse->getStatusCode());

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('d9f80740-38f0-11e8-b467-0ed5f89f718b', $response->getTransactionReference());
        $this->assertEquals('COMPLETED', $response->getStatus());
    }

    public function testCompletePurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('RestCompletePurchaseFailure.txt');
        $data = json_decode($httpResponse->getBody()->getContents(), true);

        $response = new OrdersResponse($this->getMockRequest(), $data, $httpResponse->getStatusCode());

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertEquals('This request is invalid due to the current state of the payment', $response->getMessage());
    }

    public function testFetchOrderSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('RestFetchOrderSuccess.txt');
        $data = json_decode($httpResponse->getBody()->getContents(), true);

        $response = new OrdersResponse($this->getMockRequest(), $data, $httpResponse->getStatusCode());

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('0L3952582F3664834', $response->getData()['id']);
        $this->assertEquals('APPROVED', $response->getStatus());
    }

    public function testFetchOrderFailure()
    {
        $httpResponse = $this->getMockHttpResponse('RestFetchOrderFailure.txt');
        $data = json_decode($httpResponse->getBody()->getContents(), true);

        $response = new OrdersResponse($this->getMockRequest(), $data, $httpResponse->getStatusCode());

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('The specified resource does not exist.', $response->getMessage());
    }

    public function testTokenFailure()
    {
        $httpResponse = $this->getMockHttpResponse('RestTokenFailure.txt');
        $data = json_decode($httpResponse->getBody()->getContents(), true);

        $response = new OrdersResponse($this->getMockRequest(), $data, $httpResponse->getStatusCode());

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('Client secret does not match for this client', $response->getMessage());
    }
}

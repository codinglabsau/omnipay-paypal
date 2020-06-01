<?php

namespace Tests\Omnipay\PayPal\Orders;

use Omnipay\Tests\TestCase;
use Omnipay\PayPal\Message\AuthorizeRequest;

class AuthorizeRequestTest extends TestCase
{
    /**
     * @var AuthorizeRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();

        $this->request = new AuthorizeRequest($client, $request);

        $this->request->initialize([
            'orderId' => 'test',
            'payerId' => 'QYR5Z8XDVJNXQ',
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertEquals('test', $data['order_id'], $data['payer_id']);
    }

    public function testEndpoint()
    {
        $this->request->setOrderId('ABC-123');
        $this->assertStringEndsWith('/checkout/orders/ABC-123/authorize', $this->request->getEndpoint());
    }
}

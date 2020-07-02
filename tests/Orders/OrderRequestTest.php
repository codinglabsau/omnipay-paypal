<?php

namespace Tests\Omnipay\PayPal\Orders;

use Omnipay\Tests\TestCase;
use Omnipay\PayPal\Message\OrderRequest;

class OrderRequestTest extends TestCase
{
    /**
     * @var OrderRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $client = $this->getHttpClient();
        $request = $this->getHttpRequest();

        $this->request = new OrderRequest($client, $request);

        $this->request->initialize([
            'orderId' => 'test',
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertEquals('test', $data['order_id']);
    }

    public function testEndpoint()
    {
        $this->request->setOrderId('ABC-123');
        $this->assertStringEndsWith('/checkout/orders/ABC-123', $this->request->getEndpoint());
    }
}

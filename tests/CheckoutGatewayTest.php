<?php

namespace Tests\Omnipay\PayPal;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\PayPal\CheckoutGateway;

class CheckoutGatewayTest extends GatewayTestCase
{
    /**
     * @var CheckoutGateway
     */
    protected $gateway;

    /**
     * @var array
     */
    protected $params;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new CheckoutGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setToken('TEST-TOKEN-123');
        $this->gateway->setTokenExpires(time() + 600);

        $this->params = [
            'order_id' => 'test',
        ];
    }

    public function testBearerToken()
    {
        $this->gateway->setToken('');
        $this->setMockHttpResponse('RestTokenSuccess.txt');

        $this->assertFalse($this->gateway->hasToken());
        $this->assertEquals('A21AAEpcd8Cn35EV5og-5Pxp14EvaHZdjNk2Tj5k0JZqXSCIE7BpZb', $this->gateway->getToken()); // triggers request
        $this->assertEquals(time() + 31729, $this->gateway->getTokenExpires());
        $this->assertTrue($this->gateway->hasToken());
    }

    public function testBearerTokenReused()
    {
        $this->setMockHttpResponse('RestTokenSuccess.txt');
        $this->gateway->setToken('MYTOKEN');
        $this->gateway->setTokenExpires(time() + 60);

        $this->assertTrue($this->gateway->hasToken());
        $this->assertEquals('MYTOKEN', $this->gateway->getToken());
    }

    public function testBearerTokenExpires()
    {
        $this->setMockHttpResponse('RestTokenSuccess.txt');
        $this->gateway->setToken('MYTOKEN');
        $this->gateway->setTokenExpires(time() - 60);

        $this->assertFalse($this->gateway->hasToken());
        $this->assertEquals('A21AAEpcd8Cn35EV5og-5Pxp14EvaHZdjNk2Tj5k0JZqXSCIE7BpZb', $this->gateway->getToken());
    }

    public function testCompletePurchaseFailure()
    {
        $this->setMockHttpResponse('RestCompletePurchaseFailure.txt');

        $response = $this->gateway->completePurchase($this->params)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('This request is invalid due to the current state of the payment', $response->getMessage());
    }

    public function testCompletePurchaseSuccess()
    {
        $this->setMockHttpResponse('RestCompletePurchaseSuccess.txt');

        $response = $this->gateway->completePurchase($this->params)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('d9f80740-38f0-11e8-b467-0ed5f89f718b', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }
}

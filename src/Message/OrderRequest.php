<?php

namespace Omnipay\PayPal\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * <code>
 *   // Fetch an order.
 *   $transaction = $gateway->fetchOrder([
 *       'order_id' => '123',
 *   ]);
 * </code>
 *
 * @link https://developer.paypal.com/docs/api/orders/v2/#orders_get
 */
class OrderRequest extends AbstractRequest
{
    protected function getHttpMethod(): string
    {
        return 'GET';
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setOrderId($value): AbstractRequest
    {
        return $this->setParameter('orderId', $value);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('orderId');

        return [
            'order_id' => $this->getOrderId(),
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return parent::getEndpoint() . '/checkout/orders/' . $this->getOrderId();
    }

    /**
     * @param $data
     * @param $statusCode
     * @return OrdersResponse
     */
    protected function createResponse($data, $statusCode)
    {
        return $this->response = new OrdersResponse($this, $data, $statusCode);
    }
}

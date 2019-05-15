<?php

namespace Omnipay\PayPal\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * <code>
 *   // Once the transaction has been approved, we need to complete it.
 *   $transaction = $gateway->completePurchase([
 *       'order_id' => '123',
 *   ]);
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       // The customer has successfully paid.
 *   } else {
 *       // There was an error returned by completePurchase().  You should
 *       // check the error code and message from PayPal, which may be something
 *       // like "card declined", etc.
 *   }
 * </code>
 *
 * @link https://developer.paypal.com/docs/api/orders/v2/#orders_capture
 */
class CaptureRequest extends AbstractRequest
{
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
            'order_id' => $this->getOrderId()
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return parent::getEndpoint() . '/checkout/orders/' . $this->getOrderId() . '/capture';
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

<?php

namespace Omnipay\PayPal\Message;

/**
 * PayPal Orders Response
 */
class OrdersResponse extends BaseResponse
{
    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        if (isset($this->data['purchase_units'][0]['reference_id']) && $this->isSuccessful()) {
            return $this->data['purchase_units'][0]['reference_id'];
        }

        return parent::getTransactionReference();
    }
}

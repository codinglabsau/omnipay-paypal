<?php

namespace Omnipay\PayPal\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * PayPal Response
 */
class BaseResponse extends AbstractResponse
{
    protected $statusCode;

    /**
     * Response constructor.
     * @param RequestInterface $request
     * @param $data
     * @param int $statusCode
     */
    public function __construct(RequestInterface $request, $data, $statusCode = 200)
    {
        parent::__construct($request, $data);
        $this->statusCode = $statusCode;
    }

    /**
     * @return boolean
     */
    public function isSuccessful(): bool
    {
        return empty($this->data['error']) && $this->getCode() < 400;
    }

    /**
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * @return null|string A response status from the payment gateway
     */
    public function getStatus()
    {
        return $this->data['status'] ?? null;
    }

    /**
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->data['error_description'] ?? $this->data['message'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getInformationLink()
    {
        return $this->data['information_link'] ?? null;
    }

    /**
     * @return null|string A response code from the payment gateway
     */
    public function getCode()
    {
        return $this->statusCode;
    }
}

<?php

namespace Omnipay\PayPal\Message;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * PayPal Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const API_VERSION = 'v2';

    /**
     * Sandbox Endpoint URL
     *
     * The PayPal REST APIs are supported in two environments. Use the Sandbox environment
     * for testing purposes, then move to the live environment for production processing.
     * When testing, generate an access token with your test credentials to make calls to
     * the Sandbox URIs. When you’re set to go live, use the live credentials assigned to
     * your app to generate a new access token to be used with the live URIs.
     *
     * @var string URL
     */
    protected $testEndpoint = 'https://api.sandbox.paypal.com';

    /**
     * Live Endpoint URL
     *
     * When you’re set to go live, use the live credentials assigned to
     * your app to generate a new access token to be used with the live URIs.
     *
     * @var string URL
     */
    protected $liveEndpoint = 'https://api.paypal.com';

    /**
     * PayPal Payer ID
     *
     * @var string PayerID
     */
    protected $payerId = null;

    protected $referrerCode;

    /**
     * @return string
     */
    public function getReferrerCode()
    {
        return $this->referrerCode;
    }

    /**
     * @param string $referrerCode
     */
    public function setReferrerCode($referrerCode)
    {
        $this->referrerCode = $referrerCode;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->getParameter('clientId');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setClientId($value): AbstractRequest
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setSecret($value): AbstractRequest
    {
        return $this->setParameter('secret', $value);
    }

    /**
     * Get OAuth 2.0 access token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->getParameter('token') ?? $this->getClientId() . ':' . $this->getSecret();
    }

    /**
     * @return mixed
     */
    public function getPayerId()
    {
        return $this->getParameter('payerId');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setPayerId($value): AbstractRequest
    {
        return $this->setParameter('payerId', $value);
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    protected function getHttpMethod(): string
    {
        return 'POST';
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint . '/' . self::API_VERSION;
    }

    /**
     * @param mixed $data
     * @return BaseResponse
     * @throws InvalidResponseException
     */
    public function sendData($data)
    {
        // Guzzle HTTP Client createRequest does funny things when a GET request
        // has attached data, so don't send the data if the method is GET.
        if ($this->getHttpMethod() === 'GET') {
            $requestUrl = $this->getEndpoint() . '?' . http_build_query($data);
            $body = null;
        } else {
            $body = json_encode($data);
            $requestUrl = $this->getEndpoint();
        }

        // Might be useful to have some debug code here, PayPal especially can be
        // a bit fussy about data formats and ordering.  Perhaps hook to whatever
        // logging engine is being used.
        // echo "Data == " . json_encode($data) . "\n";

        try {
            $httpResponse = $this->httpClient->request(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getToken(),
                    'Content-type' => 'application/json',
                    'PayPal-Partner-Attribution-Id' => $this->getReferrerCode(),
                ],
                $body
            );
            // Empty response body should be parsed also as and empty array
            $body = (string) $httpResponse->getBody()->getContents();
            $jsonToArrayResponse = !empty($body) ? json_decode($body, true) : [];
            return $this->response = $this->createResponse($jsonToArrayResponse, $httpResponse->getStatusCode());
        } catch (\Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * @param $data
     * @param $statusCode
     * @return BaseResponse
     */
    protected function createResponse($data, $statusCode)
    {
        return $this->response = new BaseResponse($this, $data, $statusCode);
    }
}

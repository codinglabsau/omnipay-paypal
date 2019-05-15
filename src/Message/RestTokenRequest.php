<?php

namespace Omnipay\PayPal\Message;

/**
 * PayPal REST Token Request
 *
 * With each API call, you’ll need to set request headers, including
 * an OAuth 2.0 access token. Get an access token by using the OAuth
 * 2.0 client_credentials token grant type with your clientId:secret
 * as your Basic Auth credentials.
 *
 * @link https://developer.paypal.com/docs/api/overview/#get-an-access-token
 */
class RestTokenRequest extends AbstractRequest
{
    /**
     * @return array
     */
    public function getData(): array
    {
        return [];
    }

    /**
     * v2 Oauth does not exist
     * @return string
     */
    protected function getEndpoint(): string
    {
        $base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
        return $base . '/v1' . '/oauth2/token';
    }

    /**
     * @param mixed $data
     * @return BaseResponse
     */
    public function sendData($data): BaseResponse
    {
        $body = $data ? http_build_query($data, '', '&') : null;
        $httpResponse = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode("{$this->getClientId()}:{$this->getSecret()}"),
            ],
            $body
        );
        // Empty response body should be parsed also as and empty array
        $body = (string) $httpResponse->getBody()->getContents();
        $jsonToArrayResponse = !empty($body) ? json_decode($body, true) : [];
        return $this->response = new BaseResponse($this, $jsonToArrayResponse, $httpResponse->getStatusCode());
    }
}

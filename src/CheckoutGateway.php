<?php

namespace Omnipay\PayPal;

use Omnipay\Common\AbstractGateway;
use Omnipay\PayPal\Message\OrderRequest;
use Omnipay\PayPal\Message\CaptureRequest;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\PayPal\Message\RestTokenRequest;

/**
 * PayPal Checkout Class
 *
 * @method \Omnipay\Common\Message\RequestInterface capture(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface void(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface purchase(array $options = [])
 *
 * ### Example
 * // Create a gateway for the PayPal CheckoutGateway
 * // (routes to GatewayFactory::create)
 *
 * $gateway = Omnipay::create('PayPal_Checkout');
 *
 * // Initialise the gateway
 * $gateway->initialize([
 *     'token' => 'MyPayPalToken',
 *     'clientId' => 'MyPayPalClientId',
 *     'secret'   => 'MyPayPalSecret',
 *     'testMode' => true, // Or false when you are ready for live transactions
 * ]);
 */
class CheckoutGateway extends AbstractGateway
{
    /**
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return [
            'token' => null,
            'clientId' => null,
            'secret' => null,
            'testMode' => false,
        ];
    }

    /**
     * Get OAuth 2.0 client ID for the access token.
     *
     * Get an access token by using the OAuth 2.0 client_credentials
     * token grant type with your clientId:secret as your Basic Auth
     * credentials.
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->getParameter('clientId');
    }

    /**
     * Set OAuth 2.0 client ID for the access token.
     *
     * Get an access token by using the OAuth 2.0 client_credentials
     * token grant type with your clientId:secret as your Basic Auth
     * credentials.
     *
     * @param string $value
     * @return CheckoutGateway
     */
    public function setClientId($value): CheckoutGateway
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * Get OAuth 2.0 secret for the access token.
     *
     * Get an access token by using the OAuth 2.0 client_credentials
     * token grant type with your clientId:secret as your Basic Auth
     * credentials.
     *
     * @return string
     */
    public function getSecret(): string
    {
        return $this->getParameter('secret');
    }

    /**
     * Set OAuth 2.0 secret for the access token.
     *
     * Get an access token by using the OAuth 2.0 client_credentials
     * token grant type with your clientId:secret as your Basic Auth
     * credentials.
     *
     * @param string $value
     * @return CheckoutGateway
     */
    public function setSecret($value): CheckoutGateway
    {
        return $this->setParameter('secret', $value);
    }

    /**
     * Set OAuth 2.0 access token.
     *
     * @param string $value
     * @return CheckoutGateway
     */
    public function setToken(string $value): CheckoutGateway
    {
        return $this->setParameter('token', $value);
    }

    /**
     * Get OAuth 2.0 access token.
     *
     * @param bool $createIfNeeded [optional] - If there is not an active token present, should we create one?
     * @return string|null
     */
    public function getToken($createIfNeeded = true)
    {
        if ($createIfNeeded && !$this->hasToken()) {
            $response = $this->createToken()->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                if (isset($data['access_token'])) {
                    $this->setToken($data['access_token']);
                    $this->setTokenExpires(time() + $data['expires_in']);
                }
            }
        }

        return $this->getParameter('token');
    }

    /**
     * Create OAuth 2.0 access token request.
     * @return AbstractRequest
     */
    public function createToken(): AbstractRequest
    {
        return $this->createRequest(RestTokenRequest::class, []);
    }

    /**
     * Get OAuth 2.0 access token expiry time.
     *
     * @return mixed
     */
    public function getTokenExpires()
    {
        return $this->getParameter('tokenExpires');
    }

    /**
     * Set OAuth 2.0 access token expiry time.
     *
     * @param integer $value
     * @return CheckoutGateway
     */
    public function setTokenExpires($value): CheckoutGateway
    {
        return $this->setParameter('tokenExpires', $value);
    }

    /**
     * Is there a bearer token and is it still valid?
     *
     * @return bool
     */
    public function hasToken(): bool
    {
        $token = $this->getParameter('token');

        $expires = $this->getTokenExpires();
        if (!empty($expires) && !is_numeric($expires)) {
            $expires = strtotime($expires);
        }

        return !empty($token) && time() < $expires;
    }

    /**
     * Create Request
     *
     * This overrides the parent createRequest function ensuring that the OAuth
     * 2.0 access token is passed along with the request data -- unless the
     * request is a RestTokenRequest in which case no token is needed.  If no
     * token is available then a new one is created (e.g. if there has been no
     * token request or the current token has expired).
     *
     * @param string $class
     * @param array $parameters
     * @return AbstractRequest
     */
    public function createRequest($class, array $parameters = [])
    {
        if ($class !== RestTokenRequest::class && !$this->hasToken()) {
            // This will set the internal token parameter which the parent
            // createRequest will find when it calls getParameters().
            $this->getToken(true);
        }

        return parent::createRequest($class, $parameters);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PayPal Checkout';
    }

    /**
     * @param array $parameters ['order_id']
     * @return AbstractRequest
     */
    public function fetchOrder(array $parameters = [])
    {
        return $this->createRequest(OrderRequest::class, $parameters);
    }

    /**
     * @param array $parameters ['order_id']
     * @return AbstractRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(CaptureRequest::class, $parameters);
    }
}

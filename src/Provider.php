<?php
/**
 * Official OAuth2 Client Provider for Preview Technologies
 *
 * @author Shaharia Azam <shaharia@previewtechs.com>
 * @url https://www.previewtechs.com
 */

namespace Previewtechs\Oauth2\Client;

use InvalidArgumentException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Provider
 * @package Previewtechs\Oauth2\Client
 */
class Provider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    private $urlAuthorize = "https://myaccount.previewtechs.com/oauth/v1/authorize";

    /**
     * @var string
     */
    private $urlAccessToken = "https://myaccount.previewtechs.com/oauth/v1/access_token";

    /**
     * @var string
     */
    private $urlResourceOwnerDetails = "https://myaccount.previewtechs.com/api/v1/identity/user-info";

    /**
     * @var string
     */
    private $accessTokenMethod = "POST";

    /**
     * @var string
     */
    private $accessTokenResourceOwnerId;

    /**
     * @var array|null
     */
    public $scopes = [];

    /**
     * @var array
     */
    private $defaultScopes = ['basic', 'email'];

    /**
     * @var string
     */
    private $scopeSeparator = ' ';

    /**
     * @var string
     */
    private $responseError = 'error';

    /**
     * @var string
     */
    private $responseCode;

    /**
     * @var string
     */
    private $responseResourceOwnerId = 'id';

    /**
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->assertRequiredOptions($options);

        $possible = $this->getConfigurableOptions();
        $configured = array_intersect_key($options, array_flip($possible));

        foreach ($configured as $key => $value) {
            $this->$key = $value;
        }

        // Remove all options that are only used locally
        $options = array_diff_key($options, $configured);

        parent::__construct($options, $collaborators);
    }

    /**
     * Returns all options that can be configured.
     *
     * @return array
     */
    protected function getConfigurableOptions()
    {
        return array_merge($this->getRequiredOptions(), [
            'accessTokenMethod',
            'accessTokenResourceOwnerId',
            'scopeSeparator',
            'responseError',
            'responseCode',
            'responseResourceOwnerId',
            'scopes',
        ]);
    }

    /**
     * Returns all options that are required.
     *
     * @return array
     */
    protected function getRequiredOptions()
    {
        return [
            'clientId',
            'clientSecret'
        ];
    }

    /**
     * Verifies that all required options have been passed.
     *
     * @param  array $options
     * @return void
     * @throws InvalidArgumentException
     */
    private function assertRequiredOptions(array $options)
    {
        $missing = array_diff_key(array_flip($this->getRequiredOptions()), $options);

        if (!empty($missing)) {
            throw new InvalidArgumentException(
                'Required options not defined: ' . implode(', ', array_keys($missing))
            );
        }
    }

    /**
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->urlAuthorize;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->urlAccessToken;
    }

    /**
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->urlResourceOwnerDetails;
    }

    /**
     * @return array
     */
    public function getDefaultScopes()
    {
        return array_merge($this->defaultScopes, $this->scopes);
    }

    /**
     * @return string
     */
    protected function getAccessTokenMethod()
    {
        return $this->accessTokenMethod;
    }

    /**
     * @return null|string
     */
    protected function getAccessTokenResourceOwnerId()
    {
        return $this->accessTokenResourceOwnerId ?: parent::getAccessTokenResourceOwnerId();
    }

    /**
     * @return string
     */
    protected function getScopeSeparator()
    {
        return $this->scopeSeparator;
    }

    /**
     * @param ResponseInterface $response
     * @param array|string $data
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data[$this->responseError])) {
            $error = $data[$this->responseError];
            $code = $this->responseCode ? $data[$this->responseCode] : 0;
            throw new IdentityProviderException($error, $code, $data);
        }
    }

    /**
     * @param array $response
     * @param AccessToken $token
     * @return ResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ResourceOwner($response, $this->responseResourceOwnerId);
    }
}
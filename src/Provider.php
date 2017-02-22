<?php
/**
 * Write something about the purpose of this file
 *
 * @author Shaharia Azam <shaharia@previewtechs.com>
 * @url https://www.previewtechs.com
 */

namespace Previewtechs\Oauth2\Client;


use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Provider extends AbstractProvider
{

    public $testMode = false;
    public $scopes = ['basic', 'email'];

    public $authorizeEndpoint;
    public $accessTokenEndpoint;
    public $resourceOwnerEndpoint;

    public $defaultScopes = null;

    /**
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->authorizeEndpoint = "https://oauth.previewtechs.com/ac/authorize";
    }

    /**
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->accessTokenEndpoint = "https://oauth.previewtechs.com/ac/access_token";
    }

    /**
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->resourceOwnerEndpoint = "https://oauth.previewtechs.com/api/users";
    }

    /**
     * @return null
     */
    protected function getDefaultScopes()
    {
        return $this->defaultScopes;
    }

    /**
     * @param ResponseInterface $response
     * @param array|string $data
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode > 400) {
            throw new IdentityProviderException(
                $data['message'] ?: $response->getReasonPhrase(),
                $statusCode,
                $response
            );
        }
    }

    /**
     * @param array $response
     * @param AccessToken $token
     * @return ResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ResourceOwner($response);
    }
}
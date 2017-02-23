<?php
namespace Previewtechs\Oauth2\Client\Test;

use Eloquent\Phony\Pho\Phony;
use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Token\AccessToken;
use Previewtechs\Oauth2\Client\Provider;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ProviderTests
 * @package Previewtechs\Oauth2\Client\Test
 */
class ProviderTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     *
     */
    protected function setUp()
    {
        $this->provider = new Provider([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'mock_redirect_uri',
        ]);
    }

    /**
     *
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @param $body
     * @return \Eloquent\Phony\Mock\Handle\InstanceHandle
     */
    protected function mockResponse($body)
    {
        $response = Phony::mock(ResponseInterface::class);
        $response->getHeader->with('content-type')->returns('application/json');
        $response->getBody->returns(json_encode($body));
        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @return \Eloquent\Phony\Mock\Handle\InstanceHandle
     */
    protected function mockClient(ResponseInterface $response)
    {
        $client = Phony::mock(ClientInterface::class);
        $client->send->returns($response);
        return $client;
    }

    /**
     *
     */
    public function testAuthorizationUrl()
    {
        // Run
        $url = $this->provider->getAuthorizationUrl();
        $path = \parse_url($url, PHP_URL_PATH);
        // Verify
        $this->assertSame('/ac/v1/authorize', $path);
    }

    /**
     *
     */
    public function testBaseAccessTokenUrl()
    {
        $params = [];
        // Run
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $path = \parse_url($url, PHP_URL_PATH);
        // Verify
        $this->assertSame('/ac/v1/access_token', $path);
    }

    /**
     *
     */
    public function testDefaultScopes()
    {
        $reflection = new \ReflectionClass(get_class($this->provider));
        $getDefaultScopesMethod = $reflection->getMethod('getDefaultScopes');
        $getDefaultScopesMethod->setAccessible(true);
        // Run
        $scope = $getDefaultScopesMethod->invoke($this->provider);
        // Verify
        $this->assertEquals([], $scope);
    }

    /**
     *
     */
    public function testGetAccessToken()
    {
        // https://github.com/hhru/api/blob/master/docs/authorization.md
        $body = [
            'access_token' => 'mock_access_token',
            'token_type' => 'bearer',
            'expires_in' => \time() * 3600,
            'refresh_token' => 'mock_refresh_token',
        ];
        $response = $this->mockResponse($body);
        $client = $this->mockClient($response->get());
        // Run
        $this->provider->setHttpClient($client->get());
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        // Verify
        $this->assertNull($token->getResourceOwnerId());
        $this->assertEquals($body['access_token'], $token->getToken());
        $this->assertEquals($body['refresh_token'], $token->getRefreshToken());
        $this->assertGreaterThanOrEqual($body['expires_in'], $token->getExpires());
    }

    /**
     *
     */
    public function testUserProperty()
    {
        $body = [
            'id' => 12345678,
            'last_name' => 'lst_name',
            'first_name' => 'first_name',
            'middle_name' => 'middle_name',
            'email' => 'email',
        ];
        $tokenOptions = [
            'access_token' => 'mock_access_token',
            'expires_in' => 3600,
        ];
        $token = new AccessToken($tokenOptions);
        $response = $this->mockResponse($body);
        $client = $this->mockClient($response->get());
        // Run
        $this->provider->setHttpClient($client->get());
        $user = $this->provider->getResourceOwner($token);
        // Verify
        $this->assertEquals($body['id'], $user->getId());
        $this->assertEquals($body['email'], $user->getEmail());
        $this->assertArrayHasKey('email', $user->toArray());
    }

    /**
     * @expectedException League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function testErrorResponses()
    {
        $code = 401;
        $body = [
            'error' => 'Foo error',
            'message' => 'Error Message',
        ];
        $response = $this->mockResponse($body);
        $response->getStatusCode->returns($code);
        $client = $this->mockClient($response->get());
        // Run
        $this->provider->setHttpClient($client->get());
        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }
}
<?php
namespace Previewtechs\Oauth2\Client\Test;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Mockery;
use Previewtechs\Oauth2\Client\Provider;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Provider
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Provider([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertArrayHasKey('client_id', $query);
    }
}
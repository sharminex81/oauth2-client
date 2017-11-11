# Previewtechs Provider for OAuth 2.0 Client

This package provides Preview Technologies Limited OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).


[![Latest Stable Version](https://poser.pugx.org/previewtechs/oauth2-client/v/stable)](https://packagist.org/packages/previewtechs/oauth2-client)
[![License](https://img.shields.io/packagist/l/previewtechs/oauth2-client.svg)](https://github.com/previewtechs/oauth2-client/blob/master/LICENSE)
[![Build Status](https://api.travis-ci.org/PreviewTechnologies/oauth2-client.svg?branch=master)](https://travis-ci.org/PreviewTechnologies/oauth2-client)
[![Code Coverage](https://scrutinizer-ci.com/g/PreviewTechnologies/oauth2-client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PreviewTechnologies/oauth2-client/?branch=master)
[![Code Quality](https://scrutinizer-ci.com/g/PreviewTechnologies/oauth2-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PreviewTechnologies/oauth2-client/?branch=master)

## Installation

To install, use composer:

```
composer require previewtechs/oauth2-client
```

## Usage

Usage is the same as The League's OAuth client, using `\Previewtechs\Oauth2\Client\Provider` as the provider.

### Authorization Code Flow

```php
$provider = new \Previewtechs\Oauth2\Client\Provider([
    'clientId' => '{previewtechs_client_id}',    // The client ID assigned to you by Preview Technologies
    'clientSecret' => '{previewtechs_client_secret}',   // The client secret assigned to you by Preview Technologies
    'redirectUri' => '{your_redirect_url}'
]);
```

For further usage of this package please refer to the [core package documentation on "Authorization Code Grant"](https://github.com/thephpleague/oauth2-client#usage).

To get a full list of scopes, contact with developers@previewtechs.com. Developer portal is pending development

### Refreshing a Token

```php
$provider = new \Previewtechs\Oauth2\Client\Provider([
    'clientId' => '{previewtechs_client_id}',    // The client ID assigned to you by Preview Technologies
    'clientSecret' => '{previewtechs_client_secret}',   // The client secret assigned to you by Preview Technologies
    'redirectUri' => '{your_redirect_url}'
]);

$existingAccessToken = getAccessTokenFromYourDataStore();

if ($existingAccessToken->hasExpired()) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $existingAccessToken->getRefreshToken()
    ]);
    
    //Remove old and save new access token in your database
}
```

For further usage of this package please refer to the [core package documentation on "Refreshing a Token"](https://github.com/thephpleague/oauth2-client#refreshing-a-token).


### Complete Usage
```php
<?php
require "vendor/autoload.php";

session_start();

$provider = new \Previewtechs\Oauth2\Client\Provider([
    'clientId' => '{previewtechs_client_id}',
    // The client ID assigned to you by Preview Technologies
    'clientSecret' => '{previewtechs_client_secret}',
    // The client password assigned to you by Preview Technologies
    'redirectUri' => '{your_redirect_url}'
]);

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo $accessToken->getToken() . "\n";
        echo $accessToken->getRefreshToken() . "\n";
        echo $accessToken->getExpires() . "\n";
        echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        var_export($resourceOwner->toArray());

        // We provides a way to get an authenticated API request for
        // the service, using the access token; it returns an object conforming
        // to Psr\Http\Message\RequestInterface.
        $request = $provider->getAuthenticatedRequest(
            'GET',
            'https://myaccount.previewtechs.com/api/v1/identity/user-info',
            $accessToken
        );

        $client = new \GuzzleHttp\Client();
        $result = $client->send($request);

        var_export(json_decode($result->getBody()->getContents(), true));

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }

}
```

## Contributing

Please see [CONTRIBUTING](https://github.com/previewtechnologies/oauth2-client) for details.


## Credits

- [Shaharia Azam](https://github.com/shahariaazam)
- [All Contributors](https://github.com/previewtechnologies/oauth2-client/contributors)


## License

The MIT License (MIT)


_Notes: heavily inspired from [The PHP League Third Party Provider's libraries](https://github.com/thephpleague/oauth2-client/blob/master/docs/providers/thirdparty.md)_
# Previewtechs Provider for OAuth 2.0 Client

This package provides Preview Technologies Limited OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

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

## Contributing

Please see [CONTRIBUTING](https://github.com/previewtechnologies/oauth2-client) for details.


## Credits

- [Shaharia Azam](https://github.com/shahariaazam)
- [All Contributors](https://github.com/previewtechnologies/oauth2-client/contributors)


## License

The MIT License (MIT)


_Notes: heavily inspired from [The PHP League Third Party Provider's libraries](https://github.com/thephpleague/oauth2-client/blob/master/docs/providers/thirdparty.md)_
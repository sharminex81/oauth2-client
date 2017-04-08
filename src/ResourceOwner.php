<?php
/**
 * Preview Technologies OAuth2 Resource Owner PHP class
 *
 * @author Shaharia Azam <shaharia@previewtechs.com>
 * @url https://www.previewtechs.com
 */

namespace Previewtechs\Oauth2\Client;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
/**
 * Class ResourceOwner
 * @package Previewtechs\Oauth2\Client
 */
class ResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;
    /**
     * @var array|mixed
     */
    protected $data = [];
    /**
     * ResourceOwner constructor.
     * @param array $response
     */
    public function __construct(array $response = array())
    {
        $this->response = $response;
        if (array_key_exists('data', $this->response)) {
            $this->data = $this->response['data'];
        }
    }
    /**
     * @return null
     */
    public function getId()
    {
        return $this->data['id'] ?: null;
    }
    /**
     * @return null
     */
    public function getEmail()
    {
        return $this->data['email_address'] ?: null;
    }
    /**
     * @return null
     */
    public function getFirstName()
    {
        return $this->data['profile']['first_name'] ?: null;
    }
    /**
     * @return null
     */
    public function getLastName()
    {
        return $this->data['profile']['last_name'] ?: null;
    }
    /**
     * @return null
     */
    public function getFullName()
    {
        return $this->data['profile']['full_name'] ?: null;
    }
    /**
     * @return null
     */
    public function getPicture()
    {
        return $this->data['profile']['picture'] ?: null;
    }
    /**
     * @return null
     */
    public function getGender()
    {
        return $this->data['profile']['gender'] ?: null;
    }
    /**
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
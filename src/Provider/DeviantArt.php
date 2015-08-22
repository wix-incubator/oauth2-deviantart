<?php

namespace DeviantArt\OAuth2\Client\Provider;

use DeviantArt\OAuth2\Client\Entity;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class DeviantArt extends AbstractProvider
{
    use BearerAuthorizationTrait;

    public $userExpandOptions = [];

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://www.deviantart.com/oauth2/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://www.deviantart.com/oauth2/token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $query = '';
        if (!empty($this->userExpandOptions)) {
            $query = http_build_query(['expand' => implode(',', $this->userExpandOptions)]);
        }

        return 'https://www.deviantart.com/api/v1/oauth2/user/whoami?' . $query;
    }

    protected function getDefaultScopes()
    {
        return ['user'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $data['message'] ?: $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $deviant = Entity\Deviant::factory($response);

        if (!empty($response['details'])) {
            $deviant->setUserDetails(Entity\Details::factory($response['details']));
        }

        if (!empty($response['geo'])) {
            $deviant->setUserGeo(Entity\Geo::factory($response['geo']));
        }

        if (!empty($response['stats'])) {
            $deviant->setUserStats(Entity\Stats::factory($response['stats']));
        }

        if (!empty($response['profile'])) {
            $deviant->setUserProfile(Entity\Profile::factory($response['profile']));
        }

        return $deviant;
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }
}

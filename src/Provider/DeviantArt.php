<?php

namespace DeviantArt\OAuth2\Client\Provider;

use DeviantArt\OAuth2\Client\Entity;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class DeviantArt extends AbstractProvider
{
    public $authorizationHeader = 'Bearer';
    public $scopeSeparator = ' ';
    public $userExpandOptions = [];

    public function urlAuthorize()
    {
        return 'https://www.deviantart.com/oauth2/authorize';
    }

    public function urlAccessToken()
    {
        return 'https://www.deviantart.com/oauth2/token';
    }

    public function urlUserDetails(AccessToken $token)
    {
        $query = '';
        if (!empty($this->userExpandOptions)) {
            $query = $this->httpBuildQuery(['expand' => implode(',', $this->userExpandOptions)]);
        }

        return 'https://www.deviantart.com/api/v1/oauth2/user/whoami?' . $query;
    }

    public function userDetails($response, AccessToken $token)
    {
        $deviant = Entity\Deviant::factory($response);

        if (!empty($response->details)) {
            $deviant->setUserDetails(Entity\Details::factory($response->details));
        }

        if (!empty($response->geo)) {
            $deviant->setUserGeo(Entity\Geo::factory($response->geo));
        }

        if (!empty($response->stats)) {
            $deviant->setUserStats(Entity\Stats::factory($response->stats));
        }

        if (!empty($response->profile)) {
            $deviant->setUserProfile(Entity\Profile::factory($response->profile));
        }

        return $deviant;
    }

    protected function fetchUserDetails(AccessToken $token)
    {
        $this->headers['Authorization'] = 'Bearer ' . $token->accessToken;

        return parent::fetchUserDetails($token);
    }
}

<?php

namespace DeviantArt\OAuth2\Client\Test\Provider;

use DeviantArt\OAuth2\Client\Provider\DeviantArt;
use League\OAuth2\Client\Token\AccessToken;
use Mockery as m;

class DeviantArtTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;

    protected function setUp()
    {
        $this->provider = new DeviantArt([
            'clientId'     => 'client_id',
            'clientSecret' => 'secret',
            'redirectUri'  => 'redirect_uri',
            'userExpandOptions' => ['user.details', 'user.geo', 'user.profile', 'user.stats'],
        ]);
    }

    public function testCanGenerateAuthorizationUri()
    {
        $url = $this->provider->getAuthorizationUrl([
            'state' => 'state',
            'scope' => 'scope1 scope2',
        ]);
        $parsed = parse_url($url);
        parse_str($parsed['query'], $query);

        $this->assertEquals('https', $parsed['scheme'], 'Should use https');
        $this->assertEquals('www.deviantart.com', $parsed['host'], 'Should use www.deviantart.com');
        $this->assertEquals('/oauth2/authorize', $parsed['path'], 'Should use /oauth2/authorize');
        $this->assertEquals('client_id', $query['client_id'], 'Should send client_id');
        $this->assertEquals('redirect_uri', $query['redirect_uri'], 'Should send redirect_uri');
        $this->assertEquals('state', $query['state'], 'Should send state');
        $this->assertEquals('scope1 scope2', $query['scope'], 'Should send scope');
    }

    public function testUsesCorrectTokenUri()
    {
        $url = $this->provider->getBaseAccessTokenUrl([]);
        $parsed = parse_url($url);
        $this->assertEquals('/oauth2/token', $parsed['path'], 'Should use /oauth2/token');
        $this->assertContains('deviantart.com', $parsed['host'], 'Should use da domain');
    }

    public function testUsesCorrectResourceOwnerDetailsUrl()
    {
        $token = new AccessToken(['access_token' => 'blah']);

        $url = $this->provider->getResourceOwnerDetailsUrl($token);
        $parsed = parse_url($url);

        $this->assertEquals('/api/v1/oauth2/user/whoami', $parsed['path'], 'Should use /api/v1/oauth2/user/whoami');
    }

    public function testCanGetResourceOwner()
    {
        $userinfo =
'{
    "userid": "C69E67CC-61A2-C16B-9C0A-D9989349AC0B",
    "username": "reactortest3",
    "usericon": "http://a.deviantart.net/avatars/r/e/reactortest3.jpg?1",
    "type": "regular",
    "details": {
        "sex": "f",
        "age": null,
        "joindate": "2012-06-19T02:54:19-0700"
    },
    "geo": {
        "country": "United Kingdom",
        "countryid": 223,
        "timezone": "America/Dawson_Creek"
    },
    "profile": {
        "user_is_artist": true,
        "artist_level": "Hobbyist",
        "artist_speciality": "Literature",
        "real_name": "realname",
        "tagline": "tagline",
        "website": "http://www.test.com",
        "profile_pic": {
            "deviationid": "FE7CB1E5-D4F2-6E76-3D87-6A9398B0BAE6",
            "printid": null,
            "url": "http://reactortest3.deviantart.com/art/Profile-picture-511477571",
            "title": "Profile picture",
            "category": "Uncategorized",
            "category_path": "",
            "is_favourited": false,
            "is_deleted": false,
            "author": {
                "userid": "C69E67CC-61A2-C16B-9C0A-D9989349AC0B",
                "username": "reactortest3",
                "usericon": "http://a.deviantart.net/avatars/r/e/reactortest3.jpg?1",
                "type": "regular"
            },
            "stats": {
                "comments": 0,
                "favourites": 0
            },
            "published_time": 1422966478,
            "allows_comments": true,
            "content": {
                "src": "http://orig10.deviantart.net/4a60/f/2015/034/5/e/profile_picture_by_reactortest3-d8giqmb.jpg",
                "height": 300,
                "width": 225,
                "transparency": false,
                "filesize": 9943
            },
            "thumbs": [
                {
                    "src": "http://t05.deviantart.net/V3O3LP6iaDzdFO4txQgtkzguvZE=/fit-in/150x150/filters:no_upscale():origin()/pre13/40cb/th/pre/f/2015/034/5/e/profile_picture_by_reactortest3-d8giqmb.jpg",
                    "height": 150,
                    "width": 113,
                    "transparency": false
                },
                {
                    "src": "http://t13.deviantart.net/JL6ulyiaO1gGsLuZuQ_4V36qQos=/300x200/filters:fixed_height(100,100):origin()/pre13/40cb/th/pre/f/2015/034/5/e/profile_picture_by_reactortest3-d8giqmb.jpg",
                    "height": 200,
                    "width": 150,
                    "transparency": false
                },
                {
                    "src": "http://orig10.deviantart.net/4a60/f/2015/034/5/e/profile_picture_by_reactortest3-d8giqmb.jpg",
                    "height": 300,
                    "width": 225,
                    "transparency": false
                }
            ],
            "is_mature": false,
            "is_downloadable": true
        }
    },
    "stats": {
        "watchers": 0,
        "friends": 0
    }
}';

        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token&expires=3600&refresh_token=mock_refresh_token}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
        $postResponse->shouldReceive('getStatusCode')->andReturn(200);
        $userResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $userResponse->shouldReceive('getBody')->andReturn($userinfo);
        $userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $userResponse->shouldReceive('getStatusCode')->andReturn(200);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(2)
            ->andReturn($postResponse, $userResponse);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $user = $this->provider->getResourceOwner($token);

        $this->assertInstanceOf('DeviantArt\OAuth2\Client\Entity\Deviant', $user, 'Should get instance of Deviant');
        $this->assertEquals('reactortest3', $user->username, 'Username should be set');
        $this->assertEquals('C69E67CC-61A2-C16B-9C0A-D9989349AC0B', $user->userid, 'Userid should be set');
        $this->assertNotEmpty($user->details, 'Should set details');
        $this->assertNotEmpty($user->geo, 'Should set geo');
        $this->assertNotEmpty($user->stats, 'Should set stats');
        $this->assertNotEmpty($user->profile, 'Should set profile');
    }
}

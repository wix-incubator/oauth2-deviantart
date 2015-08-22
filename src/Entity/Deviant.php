<?php

namespace DeviantArt\OAuth2\Client\Entity;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class Deviant extends Entity implements ResourceOwnerInterface
{
    protected $username;
    protected $userid;
    protected $usericon;
    protected $type;

    protected $details;
    protected $geo;
    protected $stats;
    protected $profile;

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->userid;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'userid' => $this->userid,
            'username' => $this->username,
            'usericon' => $this->usericon,
            'type' => $this->type,
            'details' => $this->details ? $this->details->toArray() : [],
            'geo' => $this->geo ? $this->geo->toArray() : [],
            'stats' => $this->stats ? $this->stats->toArray() : [],
            'profile' => $this->profile ? $this->profile->toArray() : [],
        ];
    }

    public static function factory(array $data)
    {
        return new Deviant(
            $data['userid'],
            $data['username'],
            $data['usericon'],
            $data['type']
        );
    }

    public function __construct($userid, $username, $usericon, $type)
    {
        $this->userid = $userid;
        $this->username = $username;
        $this->usericon = $usericon;
        $this->type = $type;
    }

    public function setUserDetails(Details $details) {
        $this->details = $details;
    }

    public function setUserProfile(Profile $profile) {
        $this->profile = $profile;
    }

    public function setUserGeo(Geo $geo) {
        $this->geo = $geo;
    }

    public function setUserStats(Stats $stats) {
        $this->stats = $stats;
    }
}

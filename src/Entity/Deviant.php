<?php

namespace DeviantArt\OAuth2\Client\Entity;

class Deviant extends Entity
{
    protected $username;
    protected $userid;
    protected $usericon;
    protected $type;

    protected $details;
    protected $geo;
    protected $stats;
    protected $profile;

    public static function factory(\stdClass $data)
    {
        return new Deviant(
            $data->userid,
            $data->username,
            $data->usericon,
            $data->type
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

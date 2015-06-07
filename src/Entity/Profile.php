<?php

namespace DeviantArt\OAuth2\Client\Entity;

class Profile extends Entity
{
    protected $userIsArtist;
    protected $artistLevel;
    protected $artistSpeciality;
    protected $realName;
    protected $tagline;
    protected $website;
    protected $profilePic;

    public static function factory(\stdClass $data)
    {
        return new Profile(
            $data->user_is_artist,
            $data->artist_level,
            $data->artist_speciality,
            $data->real_name,
            $data->tagline,
            $data->website,
            $data->profile_pic
        );
    }

    public function __construct($userIsArtist, $artistLevel, $artistSpeciality, $realName, $tagline, $website, $profilePic)
    {
        $this->userIsArtist = $userIsArtist;
        $this->artistLevel = $artistLevel;
        $this->artistSpeciality = $artistSpeciality;
        $this->realName = $realName;
        $this->tagline = $tagline;
        $this->website = $website;
        $this->profilePic = !empty($profileDeviation->content) ? $profileDeviation->content->src : null;
    }
}

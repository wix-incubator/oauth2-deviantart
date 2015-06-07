<?php

namespace DeviantArt\OAuth2\Client\Entity;

class Stats extends Entity
{
    protected $watchers;
    protected $friends;

    public static function factory(\stdClass $data)
    {
        return new Stats(
            $data->watchers,
            $data->friends
        );
    }

    public function __construct($watchers, $friends)
    {
        $this->watchers = $watchers;
        $this->friends = $friends;
    }
}

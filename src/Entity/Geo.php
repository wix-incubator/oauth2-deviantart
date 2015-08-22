<?php

namespace DeviantArt\OAuth2\Client\Entity;

class Geo extends Entity
{
    protected $country;
    protected $countryId;
    protected $timezone;

    public static function factory(array $data)
    {
        return new Geo(
            $data['country'],
            $data['countryid'],
            $data['timezone']
        );
    }

    public function toArray() {
        return [
            'country' => $this->country,
            'countryId' => $this->countryid,
            'timezone' => $this->timezone,
        ];
    }

    public function __construct($country, $countryId, $timezone)
    {
        $this->country = $country;
        $this->countryId = $countryId;
        $this->timezone = $timezone;
    }
}

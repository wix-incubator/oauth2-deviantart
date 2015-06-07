<?php

namespace DeviantArt\OAuth2\Client\Entity;

class Details extends Entity
{
    protected $sex;
    protected $age;
    protected $joinDate;

    public static function factory(\stdClass $data)
    {
        return new Details(
            $data->sex,
            $data->age,
            $data->joindate
        );
    }

    public function __construct($sex, $age, $joinDate)
    {
        $this->sex = $sex;
        $this->age = $age;
        $this->joinDate = \DateTime::createFromFormat(\DateTime::ISO8601, $joinDate);
    }
}

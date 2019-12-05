<?php

namespace AcfFill;

require_once 'vendor/autoload.php';

use Faker\Factory as Faker;

class AcfFill
{
    protected $faker;
    protected $fields;

    public function __construct($fields=[])
    {
        $this->faker = Faker::create();
        $this->fields = $fields;
    }

    public function fillText($maxlength=10, $default_value='', $prepend='', $append='')
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        if (!\is_numeric($maxlength)) {
            throw new \Error('maxLength must be an integer');
        }

        if (!ctype_alnum($prepend) || !ctype_alnum($append)) {
            throw new Error('Prepend and Append must be a string or number');
        }

        return $text = $this->faker->sentence($maxlength);;
    }

    public function fillNumber($default_value='', $min=0, $max=1000, $prepend='', $append='')
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        return $number = $this->faker->numberBetween($min, $max);
    }

    public function fillEmail($default_value='')
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        return $email = $this->faker->email();
    }

    public function fillUrl()
    {
        return $this->faker->url();
    }

    public function fillPassword()
    {
        return $this->faker->password();
    }

    public function fillImage($width=500, $height=500)
    {
       return $this->faker->imageUrl($width, $height);
    }
}
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

    public function fillFile($type='pdf')
    {
        $randomFile = $this->faker->url();
    }

    public function fillWYSIWYG()
    {
        return $this->faker->randomHtml(2,3);
    }

    public function fillOembed()
    {
        return 'https://www.youtube.com/embed/YlyUfIn7r8I';
    }

    public function fillGallery($min=null, $max=10)
    {
        $gallery = [];
        $num_of_images = ($min) ? intval($min) : intval($max);

        for ($i = 1; $i < $num_of_images; $i++) {
            $image = $this->fillImage();
            $gallery[] = $image;
        }

        return $gallery;
    }

    public function fillLink($target='_self')
    {
        return [
            'url' => $this->fillUrl(),
            'title' => $this->fillText(10),
            'target' => $target
        ];
    }

    public function fillPostObject($post_types=['post'], $num_of_posts=5, $taxonomy=[])
    {
        if (!\is_array($post_types) && !empty($post_types)) {
            $post_types = ['post'];
        }

        if (empty($post_types)) {
            $post_types = ['post'];
        }

        $args = [
            'post_type' => $post_types,
            'numberposts' => \intval($num_of_posts)
        ];

        if (!\is_array($taxonomy) && !empty($taxonomy)) {
            $args['taxono']
        }


    }
}
<?php

namespace AcfFill;

require_once __DIR__ . '../vendor/autoload.php';

use Faker\Factory as Faker;

class AcfFill
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function fillText($maxlength = 10, $default_value = '')
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        return $text = $this->faker->sentence($maxlength);
    }

    public function fillNumber($default_value = '', $min = 0, $max = 1000, $prepend = '', $append = '')
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        return $number = $this->faker->numberBetween($min, $max);
    }

    public function fillEmail($default_value = '')
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

    public function fillImage($width = 500, $height = 500)
    {
        return $this->faker->imageUrl($width, $height);
    }

    public function fillFile($type = 'pdf')
    {
        $randomFile = $this->faker->url();
    }

    public function fillWYSIWYG()
    {
        return $this->faker->randomHtml(2, 3);
    }

    public function fillOembed()
    {
        return 'https://www.youtube.com/embed/YlyUfIn7r8I';
    }

    public function fillGallery($min = null, $max = 10)
    {
        $gallery = [];
        $num_of_images = ($min) ? intval($min) : intval($max);

        for ($i = 1; $i < $num_of_images; $i++) {
            $image = $this->fillImage();
            $gallery[] = $image;
        }

        return $gallery;
    }

    public function fillLink($target = '_self')
    {
        return [
            'url' => $this->fillUrl(),
            'title' => $this->fillText(10),
            'target' => $target
        ];
    }

    public function fillPostObject($post_types = ['post'], $num_of_posts = 5, $taxonomy = [])
    {
        if (!\is_array($post_types) || empty($post_types)) {
            $post_types = ['post'];
        }

        $args = [
            'post_type' => $post_types,
            'numberposts' => \intval($num_of_posts)
        ];

        if (!\is_array($taxonomy) || !empty($taxonomy)) {
            $taxonomy_array = ['relationship' => 'AND'];

            foreach ($taxonomy as $tax) {
                $taxonomy_type = explode(':', $tax);

                $taxonomy_array[] = [
                    'taxonomy' => $taxonomy_type[0],
                    'field' => 'slug',
                    'terms' => [$taxonomy_type[2]]
                ];
            }
        }

        $posts = get_posts($args);
        $post_ids = [];

        if (!empty($posts)) {
            $post_ids = wp_list_pluck($posts, 'ID');
        }

        return $post_ids;
    }

    public function fillPageLink()
    {
        return $this->fillUrl();
    }

    public function fillRelationship($post_types = [], $taxonomies = [], $num_posts = 5)
    {
        if (!\is_array($post_types) || empty($post_types)) {
            $post_types = ['post'];
        }

        return $this->fillPostObject($post_types, $taxonomies, $num_posts);
    }

    public function fillTaxonomy($taxonomy = 'category')
    {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);


        return $terms;
    }

    #TODO Create fillUser
    public function fillUser()
    {
        return null;
    }

    public function fillGoogleMaps()
    {
        return [
            'address' => $this->faker->address()
        ];
    }

    public function fillDateField()
    {
        return $this->faker->date($format = 'Ymd', $max = 'now');
    }

    public function fillDateTimeField()
    {
        return $this->faker->date($format = 'Y-m-d', $max = 'now') . $this->faker->time($format = 'H:i:s', $max = 'now');
    }

    public function fillTimeField()
    {
        return $this->faker->time($format = 'H:i:s', $max = 'now');
    }

    public function fillColorField()
    {
        return $this->faker->hexColor();
    }
}
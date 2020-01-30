<?php

namespace AcfFill;

//require_once __DIR__ . '../vendor/autoload.php';
require_once(realpath(dirname(__FILE__)) . '../../vendor/autoload.php');
require_once( ABSPATH . 'wp-admin/includes/image.php' );
require_once( \ABSPATH . 'wp-includes/class-wp-query.php');

use Faker\Factory as Faker;

class AcfFill
{
    protected $faker;
    protected $wp_query;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function fillText($maxlength, $default_value = '')
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        if (empty($maxlength)) {
            $maxlength = 10;
        }

        return $this->faker->sentence($maxlength);
    }

    public function fillNumber($default_value = '', $min = 0, $max = 1000)
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        return $number = $this->faker->numberBetween(\intval($min), intval($max));
    }

    public function fillEmail($default_value = 'jdoe@aol.com')
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

    public function uploadImageFromUrl($url)
    {
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($url);
        $filename = basename($url);

        if (wp_mkdir_p( $upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        }
        else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name( $filename ),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $file );
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    public function checkForExistingImages()
    {
        $args = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => 1,
        ];

        $query_images = new \WP_Query($args);

        if (count($query_images->posts) > 0) {
            return $query_images[0]->ID;
        } else {
            return null;
        }

    }

    public function fillImage($width = 500, $height = 500)
    {
        $existing_images = $this->checkForExistingImages();
        if (count($existing_images) > 0) {
            return $existing_images;
        }
        
        $image_url = $this->faker->imageUrl($width, $height);

        return $this->uploadImageFromUrl($image_url);
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
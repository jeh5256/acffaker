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

        return $this->faker->text($maxlength);
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

        //File name is in the format of ?XXXX
        $filename = str_replace('?', '', $filename) . '.jpg';

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
            return [];
        } else {
            return null;
        }
    }

    public function fillImage($width = 500, $height = 500)
    {
//        $existing_images = $this->checkForExistingImages();
//        if (count($existing_images) > 0) {
//            return $existing_images;
//        }
        
        $image_url = $this->faker->imageUrl($width, $height);

        return $this->uploadImageFromUrl($image_url);
    }

    public function fillFile($type = 'pdf')
    {
        return $randomFile = $this->faker->url();
    }

    public function fillWYSIWYG()
    {
        $html = $this->faker->randomHtml(2, 3);
        preg_match('~<body[^>]*>(.*?)</body>~si', $html, $body);

        return $body ? $body[1] : '';
    }

    public function fillOembed()
    {
        return 'https://www.youtube.com/embed/YlyUfIn7r8I';
    }

    public function fillSelect($choices=[]) {
        if (!empty($choices) && \is_array($choices)) {
            return $choices[0];
        }

        return $this->fillText(10);
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

    /**
     *
     * @param string $target
     * @return array
     */
    public function fillLink($target = '_self')
    {
        return [
            'title' => $this->fillText(10),
            'url' => $this->fillUrl(),
            'target' => $target
        ];
    }

    public function fillPostObject($post_types = ['post'], $num_of_posts = 1, $taxonomy = [])
    {
        if (!\is_array($post_types) || empty($post_types)) {
            $post_types = ['post'];
        }

        $args = [
            'post_type' => $post_types,
            'numberposts' => \intval($num_of_posts)
        ];

        if (!\is_array($taxonomy) && !empty($taxonomy)) {
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

    public function fillPageLink($post_types = ['post'], $taxonomy = [])
    {
        return $this->fillPostObject($post_types, 1, $taxonomy);
    }

    public function fillRelationship($post_types = [], $taxonomies = [], $num_posts = 5)
    {
        if (!\is_array($post_types) || empty($post_types)) {
            $post_types = ['post'];
        }

        if (!empty($num_posts) || $num_posts > 1) {
            $num_posts = 1;
        }

        return $this->fillPostObject($post_types, $taxonomies, $num_posts);
    }

    public function fillTaxonomy($taxonomy = 'category')
    {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        if (!\is_wp_error($terms)) {
            return \wp_list_pluck($terms, 'term_id');
        }

        return [];
    }

    public function fillUser($roles = ['admin'])
    {
        $args = ['number' => 1];

        if (\is_array($roles) && !empty($roles)) {
            $args['role__in'] = $roles;
        }

        $user_query = new \WP_User_Query($args);
        $results = $user_query->get_results();

        if (count($results) > 0) {
            return $results[0]->data->ID;
        }

        return '';
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
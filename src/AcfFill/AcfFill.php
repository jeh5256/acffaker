<?php

namespace AcfFaker\AcfFill;

use Faker\Factory;
use JetBrains\PhpStorm\ArrayShape;

class AcfFill
{
    protected $faker;
    protected $wp_query;

    public function __construct()
    {
        $this->faker = Factory::create();
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

    public function fillTextArea($default_value = '')
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        return $this->faker->paragraph();
    }

    public function fillNumber($default_value = '', $min = 10, $max = 100)
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        $min = !empty($min) ? intval($min) : 10;
        $max = !empty($max) ? \intval($max) : 100;

        return $this->faker->numberBetween($min, $max);
    }

    public function fillEmail($default_value = 'jdoe@aol.com')
    {
        if (!empty($default_value)) {
            return $default_value;
        }

        return $this->faker->email();
    }

    public function fillUrl()
    {
        return $this->faker->url();
    }

    public function fillPassword()
    {
        return $this->faker->password();
    }

    public function uploadFileFromUrl($url)
    {
        try {
            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents($url);
            $filename = basename($url);

            if (wp_mkdir_p($upload_dir['path'])) {
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
        } catch (\Exception $e) {
            return null;
        }

        return $attach_id;
    }

    public function checkForExistingFiles($mime_type='')
    {
        $args = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => 1,
        ];

        if (!empty($mime_type) && (\is_string($mime_type) || \is_array($mime_type))) {
            $args['post_mime_type'] = $mime_type;
        }
        $query_images = new \WP_Query($args);
        
       return (count($query_images->posts) > 0) ? $query_images->posts[0]->ID : null;

    }

    public function fillImage($width = 500, $height = 500)
    {
        $existing_images = $this->checkForExistingFiles();

        if (!is_null($existing_images)) {
            return $existing_images;
        }
        
        $image_url = $this->faker->imageUrl($width, $height);

        return $this->uploadFileFromUrl($image_url);
    }

    public function fillFile($type = 'application/pdf')
    {
        if (!\is_string($type) || \is_array($type)) {
            $type = 'application/pdf';
        }

        $existing_file = $this->checkForExistingFiles($type);

        if ($existing_file) {
            return $existing_file;
        } else {
            return $this->uploadFileFromUrl(dirname(__FILE__) . '/../files/test.pdf');
        }
    }

    public function fillWYSIWYG()
    {
        $html = $this->faker->randomHtml(2, 3);
        preg_match('~<body[^>]*>(.*?)</body>~si', $html, $body);

        return $body ? $body[1] : '';
    }

    public function fillOembed()
    {
        return 'https://youtu.be/Ts71hfw4FFs';
    }

    public function fillSelect($choices=[]) {
        if (!empty($choices) && \is_array($choices)) {
            return $choices[0];
        }

        return $this->fillText(10);
    }

    public function fillGallery($min = null, $max = 10): array
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
    #[ArrayShape(['title' => "mixed", 'url' => "mixed", 'target' => "string"])]
    public function fillLink(string $target = '_self'): array
    {
        return [
            'title' => $this->fillText(10),
            'url' => $this->fillUrl(),
            'target' => $target
        ];
    }

    public function fillPostObject($post_types = ['post'], $num_of_posts = 1, $taxonomy = []): array
    {
        if (!is_array($post_types) || empty($post_types)) {
            $post_types = ['post'];
        }

        $args = [
            'post_type' => $post_types,
            'numberposts' => intval($num_of_posts)
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

    public function fillPageLink($post_types = ['post'], $taxonomy = []): array
    {
        return $this->fillPostObject($post_types, 1, $taxonomy);
    }

    public function fillRelationship($post_types = [], $taxonomies = [], $num_posts = 5): array
    {
        if (!\is_array($post_types) || empty($post_types)) {
            $post_types = ['post'];
        }

        if (!empty($num_posts) || $num_posts > 1) {
            $num_posts = 1;
        }

        return $this->fillPostObject($post_types, $taxonomies, $num_posts);
    }

    public function fillTaxonomy($taxonomy = 'category'): array
    {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        if (!is_wp_error($terms)) {
            return wp_list_pluck($terms, 'term_id');
        }

        return [];
    }

    public function fillUser($roles = ['admin']): string
    {
        $args = ['number' => 1];

        if (is_array($roles) && !empty($roles)) {
            $args['role__in'] = $roles;
        }

        $user_query = new \WP_User_Query($args);
        $results = $user_query->get_results();

        if (count($results) > 0) {
            return $results[0]->data->ID;
        }

        return '';
    }

    /**
     * Generate random address within the United States
     * @return array
     */
    #[ArrayShape(['address' => "mixed", 'lat' => "mixed", 'lng' => "mixed", 'zoom' => "int"])]
    public function fillGoogleMaps(): array
    {
        return [
            'address' => $this->faker->address(),
            'lat' => $this->faker->latitude(30, 50),
            'lng' => $this->faker->longitude(-125, -75),
            'zoom' => 10
        ];
    }

    public function fillDateField()
    {
        return $this->faker->date('Ymd');
    }

    public function fillDateTimeField(): string
    {
        return $this->faker->date() . $this->faker->time();
    }

    public function fillTimeField()
    {
        return $this->faker->time();
    }

    public function fillColorField($default = '')
    {
        if (!empty($default)) return $default;

        return $this->faker->hexColor();
    }
}
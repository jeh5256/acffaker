<?php

namespace AcfFaker;

use AcfFaker\QueryPosts\QueryPosts;
use AcfFaker\HandleFields\HandleFields;
use Exception;
use WP_CLI;

ini_set('max_execution_time', 120);

class ACFFaker {

    protected $acf_json_path;

    public function __construct($template_directory)
    {
        $this->acf_json_path = $template_directory . '/acf-json';
    }

    public function fillAll() {

        if ($handle = opendir($this->acf_json_path)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry !== "." && $entry !== "..") {

                    try {
                        $json = json_decode(file_get_contents($this->acf_json_path . '/' . $entry), true);

                        if (array_key_exists('location', $json)) {
                            foreach ($json['location'] as $location) {
                                $template = $location[0]['value'];

                                switch ($location[0]['param']) {
                                    case 'page_template':
                                        $post_ids = QueryPosts::getPostIdsByTemplate($template);

                                        break;
                                    case 'post_type':
                                        $post_ids = QueryPosts::getIdsByPostType($template);
                                        break;

                                    default:
                                }

                                if (!empty($post_ids)) {
                                    HandleFields::handle($post_ids, $json['fields']);
                                }
                            }
                        }
                    } catch(Exception $e) { echo $e; }
                }
            }

            closedir($handle);
        }
    }

    public function fillByIdOrType($ids=[], $types=[])
    {
        try {
            if (!is_array($ids) || !is_array($types)) return;

            /**
             * Can skip any querying here since types is empty and acf only needs ids
             **/

            if (!empty($types) && !empty($ids)) {
                $queried_ids = QueryPosts::getPostsByTypeAndId($ids, $types);

                if (!empty($queried_ids)) {
                    foreach ($queried_ids as $queried_id) {
                        $fields = \get_field_objects($queried_id);

                        if (!empty($fields)) {
                            HandleFields::handle($queried_ids, $fields);
                        }
                    }

                }
            }

            if (empty($types) && !empty($ids)) {
                foreach ($ids as $id) {
                    $fields = \get_field_objects($id);

                    if ($fields) {
                        HandleFields::handle($ids, $fields);
                    }
                }
            }



            if (!empty($types) && empty($ids)) {
                $queried_ids = QueryPosts::getPostsByType($types);

                if (!empty($queried_ids)) {
                    foreach ($queried_ids as $queried_id) {
                        $fields = \get_field_objects($queried_id);

                        if (!empty($fields)) {
                            HandleFields::handle($queried_ids, $fields);
                        }
                    }
                }
            }

        } catch(Exception $e) { echo $e; }
    }

    public function createPostTypes($type, $number=1): array
    {
        $ids = [];

        $number = is_numeric($number)  ? $number : 1;

        if (is_string($type)) {
            for ($i=0; $i < $number; $i++) {
                $post_id = wp_insert_post([
                    'post_status' => 'draft',
                    'post_type' => sanitize_title($type)
                ]);

                if (!is_wp_error($post_id)) {
                    WP_CLI::log("Created post of type {$type}: {$post_id}");
                    $ids[] = $post_id;
                }
            }
        } elseif (is_array($type)) {
            if (!empty($type)) {
                for ($j=0; $j < $number; $j++) {
                    foreach ($type as $post_type) {
                        $post_id = wp_insert_post([
                            'post_status' => 'draft',
                            'post_type' => sanitize_title($post_type)
                        ]);

                        if (!is_wp_error($post_id)) {
                            WP_CLI::log("Created post of type {$post_type}: {$post_id}");
                            $ids[] = $post_id;
                        }
                    }
                }
            }
        }

        return $ids;
    }
}

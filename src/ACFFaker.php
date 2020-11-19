<?php

namespace AcfFaker;

use AcfFaker\QueryPosts\QueryPosts;
use AcfFaker\HandleFields\HandleFields;
use Exception;

ini_set('max_execution_time', 120);

class ACFFaker {

    protected $acf_json_path;

    public function __construct()
    {
        $this->acf_json_path = get_template_directory() . '/acf-json';
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
                        $fields = get_field_objects($queried_id);

                        if (!empty($fields)) {
                            HandleFields::handle($queried_ids, $fields);
                        }
                    }

                }
            }

            if (empty($types) && !empty($ids)) {
                foreach ($ids as $id) {
                    $fields = get_field_objects($id);

                    if ($fields) {
                        HandleFields::handle($ids, $fields);
                    }
                }
            }



            if (!empty($types) && empty($ids)) {
                $queried_ids = QueryPosts::getPostsByType($types);

                if (!empty($queried_ids)) {
                    foreach ($queried_ids as $queried_id) {
                        $fields = get_field_objects($queried_id);

                        if (!empty($fields)) {
                            HandleFields::handle($queried_ids, $fields);
                        }
                    }
                }
            }

        } catch(Exception $e) { echo $e; }
    }
}

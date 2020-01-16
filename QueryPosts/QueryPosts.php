<?php

namespace QueryPosts;

//require_once realpath(dirname(__FILE__)) . '/../../wp-load.php';
//require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/acf-faker/QueryPosts/QueryPosts.php');

class QueryPosts
{
    private $wpdb;
    private $table_prefix;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $this->wpdb->prefix;
    }

    public function getPostIdsByTemplate($page_template)
    {
        $table_name = $this->wpdb->prefix . "postmeta";

        $sql = $this->wpdb->prepare(
            "SELECT post_id FROM {$table_name} WHERE meta_key LIKE '%_wp_page_template%' AND meta_value=%s",
            $page_template
        );

        $results = $this->wpdb->get_results($sql , ARRAY_A);

        return \wp_list_pluck($results, 'post_id');
    }

    public function getIdsByPostType($post_type)
    {
        $table_name = $this->wpdb->prefix . "posts";

        $sql = $this->wpdb->prepare(
            "SELECT id FROM {$table_name} WHERE post_type=%s",
            $post_type
        );

        return \wp_list_pluck($this->wpdb->get_results($sql , ARRAY_A), 'id');
    }
}
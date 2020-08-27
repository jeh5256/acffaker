<?php

namespace QueryPosts;

class QueryPosts
{
    public static function getPostIdsByTemplate($page_template)
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key LIKE '%_wp_page_template%' AND meta_value=%s",
            $page_template
        );

        $results = $wpdb->get_results($sql , ARRAY_A);

        return wp_list_pluck($results, 'post_id');
    }

    public static function getIdsByPostType($post_type)
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT id FROM {$wpdb->posts} WHERE post_type=%s",
            $post_type
        );

        return \wp_list_pluck($wpdb->get_results($sql , ARRAY_A), 'id');
    }

    public static function getPostsByType($types=[])
    {
        global $wpdb;

        if (!is_array($types)) return [];

        $types = self::convert_to_sql_ready_string($types);

        $sql = $wpdb->prepare(
            "SELECT id FROM {$wpdb->posts} WHERE post_type IN ( $types )", [
                $types
            ]
        );

        return \wp_list_pluck($wpdb->get_results($sql , ARRAY_A), 'id');
    }

    public static function getPostsByTypeAndId($ids=[], $types=[])
    {
        global $wpdb;

        if (!is_array($ids) && !is_array($types)) return [];

        $types = self::convert_to_sql_ready_string($types);
        $ids = self::convert_to_sql_ready_string($ids);


        $sql = $wpdb->prepare(
            "SELECT id FROM {$wpdb->posts} WHERE post_type IN ( $types ) AND ID in ( $ids )", [
                $types, $ids
            ]
        );

        return wp_list_pluck($wpdb->get_results($sql , ARRAY_A), 'id');
    }

    private static function convert_to_sql_ready_string( $arr ) {
        return '"' . implode( $arr, '","' ) . '"';
    }
}
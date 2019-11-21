<?php
require_once '../wp-load.php';
$acf_json_path = get_template_directory() . '/acf-json';
//var_dump($acf_json_path);
global $wpdb;
if ($handle = opendir($acf_json_path)) {

    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {

           try {
               $json = json_decode(file_get_contents($acf_json_path . '/' . $entry), true);
               echo '<pre>';
               var_dump($json['location']);
               echo '</pre>';

               if (array_key_exists('location', $json)) {
                   foreach ($json['location'] as $location) {

                       switch ($location[0]['param']) {
                           case 'page_template':
                               $page_template = $location[0]['value'];

                               print_r($page_template);
                               echo '<br>';


                               $table_name = $wpdb->prefix . "postmeta";

                               $sql = $wpdb->prepare(
                                   "SELECT post_id FROM {$table_name} WHERE meta_key LIKE '%_wp_page_template%' AND meta_value=%s",
                                    $page_template
                               );
                               $results = $wpdb->get_results( $sql , ARRAY_A );
                               //var_dump( $sql);
                               echo '<pre>';
                               var_dump($results);
                               echo '</pre>';

                               break;
                           case 'post_type':
                               $post_template = $location[0]['value'];
                               echo $post_template . '<br>';
                               $table_name = $wpdb->prefix . "posts";
                               $sql = $wpdb->prepare(
                                   "SELECT id FROM {$table_name} WHERE post_type=%s",
                                   $post_template
                               );
                               $results = $wpdb->get_results( $sql , ARRAY_A );
                               echo '<pre>';
                               var_dump($sql);
                               echo '</pre>';
                           default:
                               //print_r($location[0]);
                       }
                       //var_dump($location[0]);
                   }
               }
           } catch(Exception $e) { echo $e; }
        }
    }

    closedir($handle);
}
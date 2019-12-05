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
                var_dump($json);
                echo '</pre>';
               if (array_key_exists('location', $json)) {
                   foreach ($json['location'] as $location) {

                       switch ($location[0]['param']) {
                           case 'page_template':
                               $page_template = $location[0]['value'];

                               $table_name = $wpdb->prefix . "postmeta";

                               $sql = $wpdb->prepare(
                                   "SELECT post_id FROM {$table_name} WHERE meta_key LIKE '%_wp_page_template%' AND meta_value=%s",
                                    $page_template
                               );
                               $results = $wpdb->get_results( $sql , ARRAY_A );

                               break;
                           case 'post_type':
                               $post_template = $location[0]['value'];
                               //echo $post_template . '<br>';
                               $table_name = $wpdb->prefix . "posts";
                               $sql = $wpdb->prepare(
                                   "SELECT id FROM {$table_name} WHERE post_type=%s",
                                   $post_template
                               );
                               $results = $wpdb->get_results( $sql , ARRAY_A );
//                               echo '<pre>';
//                               var_dump($results);
//                               echo '</pre>';
                               break;
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
require_once 'AcfFill.php';
use AcfFill\AcfFill;

$a = new AcfFill();
//var_dump($a->fillText(20, '', 1111, 222));
//var_dump($a->fillNumber(null, 20, 21, '<li>', '</li>'));
//var_dump($a->fillEmail());
//var_dump($a->fillUrl());
//var_dump($a->fillPassword());
var_dump($a->fillImage());

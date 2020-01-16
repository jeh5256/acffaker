<?php

require_once '../wp-load.php';
require_once './QueryPosts/QueryPosts.php';
require_once './HandleFields/HandleFields.php';

use QueryPosts\QueryPosts;
use AcfFill\AcfFill;
use HandleFields\HandleFields;

$acf_json_path = get_template_directory() . '/acf-json';
var_dump(realpath(dirname(__FILE__)));

if ($handle = opendir($acf_json_path)) {

    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {

           try {
               $json = json_decode(file_get_contents($acf_json_path . '/' . $entry), true);
               $query_posts = new QueryPosts();
//                echo '<pre>';
//                var_dump($json['fields']);
//                echo '</pre>';
               if (array_key_exists('location', $json)) {
                   foreach ($json['location'] as $location) {
                       //var_dump($location[0]['param']);
                        //echo'========= <br/>';
//                        echo '<pre>';
//                        var_dump($location[0]);
//                        echo '</pre>';
                       switch ($location[0]['param']) {
                           case 'page_template':
                               $page_template = $location[0]['value'];

                               $page_ids = $query_posts->getPostIdsByTemplate($page_template);

//                               if (!empty($page_ids) && array_key_exists('fields', $json)) {
//                                   HandleFields::
//                               }
//                               echo '<pre>';
//                               var_dump($page_ids);
//                               echo '</pre>';

                               break;
                           case 'post_type':
                               $post_template = $location[0]['value'];
                               //echo $post_template . '<br>';


                               $post_ids = $query_posts->getIdsByPostType($post_template);

                               if (!empty($post_ids)) {
                                   HandleFields::handle($post_ids, $json['fields']);
                               }
//                               echo '<pre>';
//                               var_dump($post_ids);
//                               echo '<br/>';
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

$a = new AcfFill();
//var_dump($a->fillText(20, '', 1111, 222));
//var_dump($a->fillNumber(null, 20, 21, '<li>', '</li>'));
//var_dump($a->fillEmail());
//var_dump($a->fillUrl());
//var_dump($a->fillPassword());
//var_dump($a->fillImage());

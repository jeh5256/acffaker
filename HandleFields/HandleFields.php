<?php

namespace HandleFields;

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/acf-faker/QueryPosts/QueryPosts.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/acf-faker/AcfFill/AcfFill.php');

use AcfFill\AcfFill;

class HandleFields
{

    public static function handle($ids=[], $fields=[])
    {
        if (!\is_array($ids) || !\is_array($fields)) {
            throw new \Error('ids and fields parameters must be arrays');
        }

        $acf_fill = new AcfFill();

        foreach ($ids as $id) {
            foreach ($fields as $field) {
                switch ($field['type']) {
                    case 'text':
                        $content = $acf_fill->fillText($field['maxlength'], $field['default_value']);
                        break;

                    case 'number':
                        $content = $acf_fill->fillNumber($field['default_value'], $field['min'], $field['max']);
                        break;

                    case 'email':
                        $content = $acf_fill->fillEmail($field['default_value']);
                        break;

                    case 'url':
                        $content = $acf_fill->fillUrl();
                        break;

                    case 'password':
                        $content = $acf_fill->fillPassword();
                        break;

                    case 'image':
                        $content = $acf_fill->fillImage();
                        break;

                    case 'wysiwyg':
                        $content = $acf_fill->fillWYSIWYG();
                        break;

                    case 'oembed':
                        $content = $acf_fill->fillOembed();
                        break;

                    case 'select':
                        $content = $acf_fill->fillSelect($field['choices']);
                        break;

                    case 'link':
                        $content = $acf_fill->fillLink();
                        break;

                    case 'post_object':
                        $content = $acf_fill->fillPostObject($field['post_type']);
                        break;

                    case 'page_link':
                        $content = $acf_fill->fillPageLink($field['post_type'], $field['taxonomy']);
                        break;

                    case 'relationship':
                        $content = $acf_fill->fillRelationship($field['post_type'], $field['taxonomy'], $field['max']);
                        break;

                    case 'taxonomy':
                        $content = $acf_fill->fillTaxonomy($field['taxonomy']);
                        break;

                    case 'user':
                        $content = $acf_fill->fillUser($field['role']);
                        break;

                    case 'google_map':
                        $content = $acf_fill->fillGoogleMaps();
                        break;

                    case 'date_picker':
                        $content = $acf_fill->fillDateField();
                        break;

                    case 'date_time_picker':
                        $content = $acf_fill->fillDateTimeField();
                        break;

                    case 'time_picker':
                        $content = $acf_fill->fillTimeField();
                        break;

                    default:
                        $content = '';
                }

                if (!empty(\get_post($id))) {
                    \update_field($field['key'], $content, \intval($id));

                    if ($field['key'] === 'date_picker') {
                        \var_dump($content);
                    }
                }
            }
        }
    }
}
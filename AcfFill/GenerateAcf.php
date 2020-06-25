<?php


namespace AcfFill;


class GenerateAcf
{
    public static function generate(AcfFill $acf_fill, $field)
    {
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

            case 'color_picker':
                $content = $acf_fill->fillColorField();
                break;

            case 'repeater':
                $content = [];

                if (!empty($field['sub_fields'])) {
                    $min = !empty($min) ? intval($min) : 1;
                    $max = !empty($max) ? intval($max) : 5;

                    if ($min > $max) {
                        $max = $min + 1;
                    }

                    foreach (range($min, $max) as $row) {
                        foreach ($field['sub_fields'] as $sub_field) {
                            $content[$row][$sub_field['key']] = self::generate($acf_fill, $sub_field);
                        }
                    }
                }

                break;

            default:
                $content = '';
        }

        return $content;
    }
}
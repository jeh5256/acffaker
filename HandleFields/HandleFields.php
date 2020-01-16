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

        $faker = new AcfFill();

        foreach ($ids as $id) {
            foreach ($fields as $field) {
                switch ($field['type']) {
                    case 'text':
                        $content = $faker->fillText($field['maxlength'], $field['default_value']);

                        break;

                    case 'number':
                        $content = $faker->fillNumber($field['default_value'], $field['min'], $field['max']);

                        break;

                    default:
                        $content = '';
                }

                if (!empty(\get_post($id))) {
                    \var_dump($field['name']);
                    \var_dump($field['type'] === 'text');
                    \var_dump('content' . $content);
                    \var_dump('id' . \intval($id));
                    $a = \update_field($field['name'], $content, \intval($id));
                    \var_dump('status' . $a);
                }
            }
        }
    }
}
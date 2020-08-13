<?php

namespace HandleFields;

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/acf-faker/QueryPosts/QueryPosts.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/acf-faker/AcfFill/AcfFill.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/acf-faker/AcfFill/GenerateAcf.php');
use AcfFill\AcfFill;
use AcfFill\GenerateAcf;

class HandleFields
{

    public static function handle($ids=[], $fields=[])
    {
        if (!is_array($ids) || !is_array($fields)) {
            throw new \Error('ids and fields parameters must be arrays');
        }

        $acf_fill = new AcfFill();

        foreach ($ids as $id) {
            foreach ($fields as $field) {

                $content = GenerateAcf::generate($acf_fill, $field);

                if (!empty(get_post($id))) {
                    update_field($field['key'], $content, \intval($id));

                }
            }
        }
    }
}
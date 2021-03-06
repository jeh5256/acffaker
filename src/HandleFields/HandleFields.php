<?php

namespace AcfFaker\HandleFields;

use AcfFaker\AcfFill\AcfFill;
use AcfFaker\AcfFill\GenerateAcf;
use Exception;

class HandleFields
{

    public static function handle($ids=[], $fields=[])
    {
        if (!is_array($ids) || !is_array($fields)) {
            throw new Exception('ids and fields parameters must be arrays');
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
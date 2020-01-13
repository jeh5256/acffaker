<?php

namespace HandleFields;

//require_once '../AcfFill/AcfFill.php';

use AcfFill\AcfFill;

class HandleFields
{
    public static function handle($ids=[], $fields=[])
    {
        if (!\is_array($ids) || !\is_array($fields)) {
            throw new \Error('ids and fields parameters must be arrays');
        }

        $faker = new AcfFill();

        foreach ($fields as $field) {
            switch ($field) {
                case 'text';
                    $text = $faker->fillText($field['maxlength'], $field['default_value']);
                    update_field(' ')
            }
        }
    }
}
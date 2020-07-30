<?php

namespace AcfCommand;

class AcfFakerCommands
{
    /**
     * Fill all ACF Fields with Faker data from fzaninotto/faker
     *
     * ## EXAMPLES
     *
     *     wp acffake fillAll
     *
     * @when after_wp_load
     * @param $args
     * @param $assoc_args
     */
    public function fillAll($args, $assoc_args)
    {
        WP_CLI::success( 'fill all' );
    }

    /**
     * Fill all ACF Fields by post id with Faker data from fzaninotto/faker
     *
     * ## OPTIONS
     *
     * [--posts]
     * : Array of post ids to fill
     * ---
     * default: false
     * ---
     *
     * [--type=<type>]
     * : Array of post types to fill
     * ---
     * default: all
     * ---
     *
     * ## EXAMPLES
     *
     *     wp acffake --posts=1,2,3,4 --type=page,post,custom-post-type
     *
     * @when after_wp_load
     * @param $args
     * @param $assoc_args
     */
    public function fillPosts($args, $assoc_args)
    {
        $posts = explode(',', $assoc_args['posts']);

        foreach ($posts as $p) {
            WP_CLI::success(  $p );
        }
    }
}
<?php

namespace AcfFaker;

class AcfFakerCommands
{
    protected $acfFaker;

    public function __construct()
    {
        $this->acfFaker = new ACFFaker();
    }

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
        $this->acfFaker->fillAll();

        WP_CLI::success( 'Completed' );
    }

    /**
     * Fill all ACF Fields by post id with Faker data from fzaninotto/faker
     *
     * ## OPTIONS
     *
     * [--types[=<value>]]
     * : Array of post types to fill
     * ---
     * default: all
     * ---
     *
     * [--posts]
     * : Array of post ids to fill
     * ---
     * default: false
     * ---
     *
     * ## EXAMPLES
     *
     *     wp acffaker --posts=1,2,3,4 --type=page,post,custom-post-type
     *
     * @when after_wp_load
     * @param $args
     * @param $assoc_args
     */
    public function fillPosts($args, $assoc_args)
    {
        if (!empty($assoc_args['posts']) || !empty($assoc_args['types'])) {
            $this->acfFaker->fillByIdOrType($assoc_args['posts'], $assoc_args['types']);
        }

        $this->acfFaker->fillAll();

        WP_CLI::success( 'Completed' );
    }
}
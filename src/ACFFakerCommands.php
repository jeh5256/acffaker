<?php

namespace AcfFaker;

use WP_CLI;

class ACFFakerCommands
{
    protected $acfFaker;

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
        $this->acfFaker = new ACFFaker(get_template_directory());
        $this->acfFaker->fillAll();

        WP_CLI::success('Completed');
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
     *     wp acffake fillPosts --posts=1,2,3,4 --type=page,post,custom-post-type
     *
     * @when after_wp_load
     * @param $args
     * @param $assoc_args
     */
    public function fillPosts($args, $assoc_args)
    {
        $this->acfFaker = new ACFFaker(get_template_directory());
        if (!empty($assoc_args['posts']) || !empty($assoc_args['types'])) {
            $this->acfFaker->fillByIdOrType($assoc_args['posts'], $assoc_args['types']);
        }

        $this->acfFaker->fillAll();

        WP_CLI::success('Completed');
    }

    /**
     * Create a set number of posts
     *
     * ## OPTIONS
     *
     * [--types[=<value>]]
     * : Array of post types to create
     * ---
     * default: post
     * ---
     *
     * [--number]
     * : Number of posts to create for each post type
     * ---
     * default: 1
     * ---
     *
     * ## EXAMPLES
     *
     *     wp acffake createPost --posts=1,2,3,4 --type=page,post,custom-post-type
     *
     * @when after_wp_load
     * @param $args
     * @param $assoc_args
     */
    public function createPosts($args, $assoc_args)
    {
        \var_dump($assoc_args);
        \var_dump($args);

        WP_CLI::success('Completed');
    }
}
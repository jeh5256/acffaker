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
     * <types>...
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
     *     wp acffake createPost post page custom-post-type ... --number=10
     *
     * @when after_wp_load
     * @param $args
     * @param $assoc_args
     */
    public function createPosts($args, $assoc_args)
    {
        $number_of_posts = !empty($assoc_args['number']) ? $assoc_args['number'] : 1;
        $post_types = is_array($args) ? $args : [];
        $posts = $this->acfFaker->createPosts($post_types, $number_of_posts);
        $this->acfFaker->fillByIdOrType($posts);

        WP_CLI::success('Completed');
    }
}
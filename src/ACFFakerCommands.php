<?php

namespace AcfFaker;

use WP_CLI;

class ACFFakerCommands
{
    protected ACFFaker $acfFaker;

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
    public function fillAll($args, $assoc_args): void
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
    public function fillPosts($args, $assoc_args): void
    {
        $this->acfFaker = new ACFFaker(get_template_directory());

        $postArgs = $assoc_args['posts'] ?? [];
        $typeArgs = $assoc_args['types'] ?? [];

        if (!empty($assoc_args['posts']) || !empty($assoc_args['types'])) {
            $this->acfFaker->fillByIdOrType($postArgs, $typeArgs);
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
     * @param array $args
     * @param array $assoc_args
     */
    public function createPosts(array $args, array $assoc_args): void
    {
        $number_of_posts = !empty($assoc_args['number']) ? $assoc_args['number'] : 1;
        $this->acfFaker = new ACFFaker(get_template_directory());
        $posts = $this->acfFaker->createPostTypes($args, $number_of_posts);
        $this->acfFaker->fillByIdOrType($posts);

        WP_CLI::success('Completed');
    }
}
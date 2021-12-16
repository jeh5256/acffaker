jeh5256/acffaker
===========================

WP Command line tool to generate Advanced Custom Fields data based on ACF JSON. Package uses [fakerphp/faker](https://github.com/FakerPHP/Faker/)
to generate content for your Advanced Custom Fields. This package requires you to have an acf-json folder in your theme.


## Installing

Installing this package requires WP-CLI v2.4.0 or greater. Update to the latest stable release with `wp cli update`.

You can install this package with following command.

```bash
wp package install git@github.com:jeh5256/acffaker.git
```
You will also need to navigate to the ~/.wp-cli/packages/vendor/jeh5256/acffaker and run `composer install`.


## Usage

`wp acffake fillAll`

This will search through all ACF JSON files and generate faker data for every field


`wp acffake fillPosts`

This will fill posts by post id, post type, or by a post id with a post type


**Options**

    --posts
       Post ID
         
    --type=<type>
        Post type (ex page, post, custom post type)
        
## Warning
Running this tool will overwrite any current ACF data already entered. There are plans in the future for this not to be the case.
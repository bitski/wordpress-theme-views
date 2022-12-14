<?php
/**
 * Views functions and definitions
 *
 * @package wordpress-theme-views
 * @since 0.1.0
 */

if ( ! defined('VIEWS_VERSION')) {
    // Replace the version number of the theme on each release.
    define('VIEWS_VERSION', '0.1.0');
}

if ( ! function_exists('views_setup')) {
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     *
     * @return void
     * @since 0.1.0
     */
    function views_setup(): void
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Views, use a find and replace
         * to change 'views' to the name of your theme in all the template files.
         */
        load_theme_textdomain('views', get_template_directory() . '/languages');

        /*
         * Let WordPress manage the document title.
         * This theme does not use a hard-coded <title> tag in the document head,
         * WordPress will provide it for us.
         */
        add_theme_support('title-tag');

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support('post-thumbnails');
    }
}
add_action('after_setup_theme', 'views_setup');

/**
 * Hides the meta tag generator from document head and rss.
 *
 * @return string
 * @since 0.1.0
 */
function views_hide_generator(): string
{
    return '';
}

add_filter('the_generator', 'views_hide_generator');

/**
 * Disables the WordPress emoji functionality.
 *
 * @return void
 * @since 0.7.4
 */
function views_disable_wp_emojis(): void
{
    // Actions related to emojis
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');

    // Filters to remove TinyMCE emojis & to remove emojis from DNS prefetch
    add_filter('tiny_mce_plugins', 'views_disable_emojis_tinymce');
    add_filter(
        'wp_resource_hints',
        'views_disable_emojis_remove_dns_prefetch',
        10,
        2
    );
}

add_action('init', 'views_disable_wp_emojis');

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param  array  $plugins
 *
 * @return array
 * @since 0.7.4
 */
function views_disable_emojis_tinymce($plugins): array
{
    if (is_array($plugins)) {
        return array_diff($plugins, ['wpemoji']);
    }

    return [];
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param  array  $urls  URLs to print for resource hints.
 * @param  string  $relation_type  The relation type the URLs are printed for.
 *
 * @return array Difference between the two arrays.
 * @since 0.7.4
 */
function views_disable_emojis_remove_dns_prefetch(array $urls, $relation_type): array
{
    if ('dns-prefetch' == $relation_type) {
        /** This filter is documented in wp-includes/formatting.php */
        $emoji_svg_url = apply_filters(
            'emoji_svg_url',
            'https://s.w.org/images/core/emoji/2/svg/'
        );

        $urls = array_diff($urls, [$emoji_svg_url]);
    }

    return $urls;
}

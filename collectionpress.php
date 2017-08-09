<?php

/*
  Plugin Name: CollectionPress
  Author: KnowledgeArc
  Description: A plugin for displaying dspace information in Wordpress.
  Version: 1.0.0
  Copyright: 2017 KnowledgeArc Ltd
  License: GPLv3
  License URI: https://www.gnu.org/licenses/gpl.html
 */

if (!function_exists('add_shortcode')) {
    echo 'Do not call this plugin directly.';
    exit;
}


$dir = dirname(__FILE__);
define( 'CPR_PLUGIN_VERSION', '1.0.0' );
define( 'CPR_TEMPLATE_PATH', plugin_dir_path(__FILE__).'frontend/template' );
define( 'CPR_ROOT_URL', plugins_url('', __FILE__) );
define( 'CPR_BASENAME', plugin_basename(__FILE__) );

add_action('init','cpr_text_domain');
function cpr_text_domain(){
    load_plugin_textdomain('cpress', false, basename( dirname(__FILE__) ) . '/lang/' );
}

require_once($dir.'/includes/class-collectionpress.php');
require_once($dir.'/admin/settings/class-collectionpress-settings.php');
require_once($dir.'/includes/functions.php');
require_once($dir.'/includes/register-posts.php');
require_once($dir.'/frontend/class-collectionpress-shortcode.php');
$settings = new CollectionPress_Settings;
add_action('admin_init', array($settings, 'register'));

if (is_admin()) {
    require_once($dir.'/admin/class-collectionpress-admin.php');

    $admin = new CollectionPress_Admin;
    add_action('admin_menu', array($admin, 'get_menus'));
    $authorreg = new CPR_AuthorReg();
	add_action( 'wp_ajax_cpr_get_author_ajax', array($authorreg, 'get_author_by_api'));
} else {
    $shortcode = new CollectionPress_ShortCode();
    add_shortcode('collectionpress', array($shortcode, 'render'));
}

function cpr_rewrite_flush()
{
    $authorreg = new CPR_AuthorReg();
    $authorreg->cpr_register_post_author();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'cpr_rewrite_flush');

//Setting Page Link
function cpr_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=collectionpress">Settings</a>'; 
    array_unshift($links, $settings_link); 
    return $links; 
}
add_filter("plugin_action_links_".CPR_BASENAME, 'cpr_settings_link');

//Redirect to Setting page after Plugin Activation
function cpr_activation_redirect( $plugin )
{
    if ($plugin == CPR_BASENAME) {
        exit(wp_redirect(admin_url('options-general.php?page=collectionpress')));
    }
}
add_action( 'activated_plugin', 'cpr_activation_redirect' );

register_uninstall_hook(__FILE__, 'cpr_uninstall_options');

function cpr_uninstall_options(){
    if (! current_user_can('activate_plugins')) {
        return;
    }

    $option_name = 'collectionpress_settings_general';

    delete_option($option_name);

    $authors_posts = get_posts(array(
            'numberposts'   => -1,
            'post_type'     => 'cp_authors',
            'post_status'   => 'any' ));

    foreach ( $authors_posts as $post ) {
        delete_post_meta( $post->ID, 'show_items' );
        delete_post_meta( $post->ID, 'author_keyword' );
        delete_post_meta( $post->ID, 'show_posts' );
        delete_post_meta( $post->ID, 'cp_related_author' );
        wp_delete_post( $post->ID, true );
    }
}

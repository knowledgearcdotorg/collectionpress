<?php

/*
  Plugin Name: Collection Press
  Author: KnowledgeArc
  Description: A plugin for displaying dspace information in Wordpress.
  Version: 1.0.0-dev
 */

if (!function_exists('add_shortcode')) {
    echo 'Do not call this plugin directly.';
    exit;
}


$dir = dirname( __FILE__ );

define( 'CP_TEMPLATE_PATH', plugin_dir_path( __FILE__ ).'frontend/template' );

require_once($dir.'/includes/class-collectionpress.php');
require_once($dir.'/admin/settings/class-collectionpress-settings.php');
require_once($dir.'/includes/functions.php');
require_once($dir.'/includes/import-from-dspace.php');
require_once($dir.'/includes/register-posts.php');

$settings = new CollectionPress_Settings;
add_action('admin_init', array($settings, 'register'));

if (is_admin()) {
    require_once($dir.'/admin/class-collectionpress-admin.php');

    $admin = new CollectionPress_Admin;
    add_action('admin_menu', array($admin, 'get_menus'));
} else {
    require_once($dir.'/frontend/class-collectionpress-shortcode.php');

    $shortcode = new CollectionPress_Shortcode();
    add_shortcode('collectionpress', array($shortcode, 'render'));
}

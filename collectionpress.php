<?php

/*
  Plugin Name: CollectionPress
  Author: KnowledgeArc
  Description: A plugin for displaying dspace information in Wordpress.
  Version: 0.9.0
  Copyright: 2017 KnowledgeArc Ltd
  License: GPLv3
  License URI: https://www.gnu.org/licenses/gpl.html
 */

if (!function_exists('add_shortcode')) {
    echo 'Do not call this plugin directly.';
    exit;
}


$dir = dirname(__FILE__);

define( 'CP_TEMPLATE_PATH', plugin_dir_path(__FILE__).'frontend/template' );
define( 'CP_JS_PATH', plugin_dir_url(__FILE__).'assets/js/' );

add_action('init','plugin_text_domain');
function plugin_text_domain(){
    load_plugin_textdomain('cpress', false, basename( dirname(__FILE__) ) . '/lang/' );
}

require_once($dir.'/includes/class-collectionpress.php');
require_once($dir.'/admin/settings/class-collectionpress-settings.php');
require_once($dir.'/includes/functions.php');
require_once($dir.'/includes/import-from-dspace.php');
require_once($dir.'/includes/register-posts.php');
require_once($dir.'/frontend/class-collectionpress-shortcode.php');
$settings = new CollectionPress_Settings;
add_action('admin_init', array($settings, 'register'));

if (is_admin()) {
    require_once($dir.'/admin/class-collectionpress-admin.php');

    $admin = new CollectionPress_Admin;
    add_action('admin_menu', array($admin, 'get_menus'));
    $CP_Author = new CP_Author();
	add_action( 'wp_ajax_cp_get_author_ajax', array($CP_Author, 'cp_get_author_by_api'));
} else {
    $shortcode = new CollectionPress_Shortcode();
    add_shortcode('collectionpress', array($shortcode, 'render'));
}

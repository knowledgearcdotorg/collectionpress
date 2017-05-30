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

require_once($dir.'/includes/class-collectionpress.php' );

if (is_admin()) {

} else {
    require_once($dir.'/frontend/class-collectionpress-shortcode.php');
}

$shortcode = new CollectionPress_Shortcode();
add_shortcode('collectionpress', array($shortcode, 'render'));

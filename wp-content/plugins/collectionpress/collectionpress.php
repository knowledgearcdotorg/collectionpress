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

require_once(dirname(__FILE__).'/class.collectionpress.php' );

add_shortcode('collectionpress', array('CollectionPress', 'render'));

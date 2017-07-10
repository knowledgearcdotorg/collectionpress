<?php
if (!defined('WPINC')) {
    die;
}

/**
 * Provides admin functionality.
 */
class CollectionPress_Admin
{
    public function get_menus()
    {
        add_options_page(
            __('CollectionPress', 'cpress'),
            __('CollectionPress', 'cpress'),
            'manage_options',
            'collectionpress',
            array($this, 'display_settings')
        );
    }

    public function display_settings()
    {
        include_once dirname(__FILE__).'/views/display-settings.php';
    }
}

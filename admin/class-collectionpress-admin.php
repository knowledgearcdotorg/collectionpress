<?php
if (!defined('WPINC')) {
    die;
}

/**
 * Provides admin functionality.
 */
class CollectionPress_Admin
{
    public function cpr_get_menus()
    {
        add_options_page(
            __('CollectionPress', 'cpress'),
            __('CollectionPress', 'cpress'),
            'manage_options',
            'collectionpress',
            array($this, 'cpr_display_settings')
        );
    }

    public function cpr_display_settings()
    {
        include_once dirname(__FILE__).'/views/display-settings.php';
    }
}

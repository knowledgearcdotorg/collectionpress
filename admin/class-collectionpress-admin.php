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
        add_menu_page(
            __('CollectionPress', 'cpress'),
            __('CollectionPress', 'cpress'),
            'manage_options',
            'collectionpress',
            array($this, 'display_settings')
        );
        add_submenu_page(
            'collectionpress',
            __('CollectionPress Import Author', 'cpress'),
            __('CollectionPress Import Author', 'cpress'),
            'manage_options',
            'collectionpress-import',
            array($this, 'import_authors')
        );
        add_submenu_page(
            null, 
            __('CollectionPress Author Import by CSV', 'cpress'),
            __('CollectionPress Author Import by CSV', 'cpress'),
            'manage_options',
            'collectionpress-csv-import',
            array($this, 'import_authors_by_csv')
        );
    }

    public function display_settings()
    {
        include_once dirname(__FILE__).'/views/display-settings.php';
    }
    
    public function import_authors()
    {
        include_once dirname(__FILE__).'/views/import-settings.php';
    }
    
    public function import_authors_by_csv()
    {
        include_once dirname(__FILE__).'/views/import-authors-by-csv.php';
    }
}

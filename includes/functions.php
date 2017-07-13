<?php
if (!defined('WPINC')) {
    die;
}

function collectionpress_settings()
{
    $settings = new CollectionPress_Settings;
    return $settings->get_all();
}

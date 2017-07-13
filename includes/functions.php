<?php
if (!defined('WPINC')) {
    die;
}

function collectionpress_settings()
{
    $settings = new CollectionPress_Settings;
    return $settings->cpr_get_all();
}

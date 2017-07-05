<?php
if (!defined('WPINC')) {
    die;
}

class CollectionPress_Settings
{
    public function get_all() {
        $settings = get_option('collectionpress_settings_general', array());

        $options = wp_parse_args(
            $settings
       );

        return apply_filters('collectionpress_settings', $options);
    }

    public function get($key, $default = false)
    {
        $settings = $this->get_all();
        $value = ! empty($settings[$key]) ? $settings[$key] : $default;
        return apply_filters('collectionpress_setting_'.$key, $value);
    }

    public function register()
    {
        $settings = $this->get_all();

        /* Register Settings */
        register_setting(
            'collectionpress_settings_group',             // Options group
            'collectionpress_settings_general',      // Option name/database
            array($this, 'validate')
       );

        /* Create settings section */
        add_settings_section(
            'collectionpress_settings_general',                   // Section ID
            'CollectionPress General Settings',  // Section title
            array($this, "my_settings_section_description"), // Section callback function
            'collectionpress_settings'                          // Settings page slug
       );

        /* Create settings - rest url */
        add_settings_field(
            'rest_url',
            __('Rest Url', 'cpress' ),
            array($this, "text_callback"),
            'collectionpress_settings',
            'collectionpress_settings_general',
            array(
                'id'        =>"rest_url",
                'label_for' =>'',
                'class'     =>'',
                'desc'      =>'',
                'name'      =>"rest_url",
                'section'   =>"collectionpress_settings_general",
                'size'      =>null,
                'options'   =>isset($setting_config['options']) ? $setting_config['options'] : '',
                'value'     =>$settings["rest_url"]
           )
       );

        /* Create settings - rest url */
        add_settings_field(
            'item_url',
            __('Item Url', 'cpress' ),
            array($this, "text_callback"),
            'collectionpress_settings',
            'collectionpress_settings_general',
            array(
                'id'        =>"item_url",
                'label_for' =>'',
                'class'     =>'',
                'desc'      =>'',
                'name'      =>"item_url",
                'section'   =>"collectionpress_settings_general",
                'size'      =>null,
                'options'   =>isset($setting_config['options']) ? $setting_config['options'] : '',
                'value'     =>$settings["item_url"]
           )
       );
    }

    /* Sanitize Callback Function */
    public function validate($input)
    {
        return $input;
    }

    /* Setting Section Description */
    function my_settings_section_description()
    {
        echo wpautop( __("This aren't the Settings you're looking for. Move along.", 'cpress' ) );
    }

    public function text_callback($args)
    {
        $value = esc_attr(stripslashes($args['value']));
        $size = (isset($args['size']) && ! is_null($args['size'])) ? $args['size'] : 'regular';
        $html = <<<HTML
<label
    for="collectionpress_settings_general[{$args['id']}">{$args['desc']}
    <input
        type="text"
        class="{$size}-text"
        id="collectionpress_settings_general[{$args['id']}]"
        name="collectionpress_settings_general[{$args['id']}]"
        value="{$value}"/>
</label>
HTML;

        echo $html;
    }
}

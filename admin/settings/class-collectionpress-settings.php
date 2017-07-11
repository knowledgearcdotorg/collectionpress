<?php
if (!defined('WPINC')) {
    die;
}

class CollectionPress_Settings
{
    public function get_all()
    {
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
            __('Rest Url', 'cpress'),
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

        add_settings_field(
            'item_page',
            __('Item View Page', 'cpress'),
            array($this, "select_callback"),
            'collectionpress_settings',
            'collectionpress_settings_general',
            array(
                'id'        =>"item_page",
                'label_for' =>'',
                'class'     =>'',
                'desc'      =>__('Specifies the page used by CollectionPress for displaying an item directly from DSpace.', 'cpress'),
                'name'      =>"item_page",
                'section'   =>"collectionpress_settings_general",
                'size'      =>null,
                'options'   =>isset($setting_config['options']) ? $setting_config['options'] : '',
                'value'     =>$settings["item_page"]
           )
        );
       
        add_settings_field(
            'author_page',
            __('Author List Page', 'cpress'),
            array($this, "select_callback"),
            'collectionpress_settings',
            'collectionpress_settings_general',
            array(
                'id'        =>"author_page",
                'label_for' =>'',
                'class'     =>'',
                'desc'      =>__('Specifies the page used by CollectionPress for displaying a list of Author Pages.', 'cpress'),
                'name'      =>"author_page",
                'section'   =>"collectionpress_settings_general",
                'size'      =>null,
                'options'   =>isset($setting_config['options']) ? $setting_config['options'] : '',
                'value'     =>$settings["author_page"]
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
        echo wpautop(__("General settings for the CollectionPress plugin.", 'cpress'));
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
    
    public function select_callback($args)
    {
        $value = esc_attr(stripslashes($args['value']));
        $size = (isset($args['size']) && ! is_null($args['size'])) ? $args['size'] : 'regular';
        $page_ids=get_all_page_ids();
        $html = <<<HTML
<label
    for="collectionpress_settings_general[{$args['id']}">{$args['desc']}
    <select name="collectionpress_settings_general[{$args['id']}]" class="{$size}-text" >
HTML;
        $html .='<option value="">'.__('Select', 'cpress').'</option>';
        foreach ($page_ids as $page) {
            $selected_text='';
            if ($value == $page) {
                $selected_text = "selected='selected'";
            }
            $html .= '
            <option value="'.$page.'" '.$selected_text.' >
                    '.get_the_title($page).'</option>';
        }
        $html .= <<<HTML
    </select>
</label>
HTML;
        echo $html;
    }
}

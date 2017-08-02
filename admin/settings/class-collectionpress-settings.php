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
        $settings['rest_url']  = (isset($settings['rest_url']) ? $settings['rest_url']:'');
        $settings['handle_url']  = (isset($settings['handle_url']) ? $settings['handle_url']:'');
        $settings['item_page']  = (isset($settings['item_page']) ? $settings['item_page']:'');
        $settings['author_page']  = (isset($settings['author_page']) ? $settings['author_page']:'');
        $settings['display_item']  = (isset($settings['display_item']) ? $settings['display_item']:'');
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
            array($this, "settings_section_description"), // Section callback function
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
            'within_wp', 
            __( 'Display Items', 'cpress' ), 
            array($this, "field_within_wp_render"),
            'collectionpress_settings', 
            'collectionpress_settings_general'
        );

        add_settings_field(
            'item_page',
            __('Item Page', 'cpress'),
            array($this, "select_callback"),
            'collectionpress_settings',
            'collectionpress_settings_general',
            array(
                'id'        =>"item_page",
                'label_for' =>'',
                'class'     =>'',
                'desc'      =>__('Specifies the Wordpress page used for displaying a DSpace item.', 'cpress'),
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
    function settings_section_description()
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
    for="collectionpress_settings_general[{$args['id']}]">
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
        if (!empty($args['desc'])) {
            $html .= '<p class="description">'.$args['desc'].'</p>';
        }
        echo $html;
    }

    public function field_within_wp_render()
    {
        $page_ids=get_all_page_ids();
        $settings = $this->get_all();
        $settings['display_item']  = (isset($settings['display_item']) ? $settings['display_item']:'');
        $settings['handle_url']    = (isset($settings['handle_url']) ? $settings['handle_url']:'');
        ?>
        <label for='within_wp' class='td_lbl'>
            <input type='radio' id='within_wp' name='collectionpress_settings_general[display_item]'
                <?php checked( $settings['display_item'], 'within_wp' ); ?>
                value='within_wp'> <?php echo __('Within Wordpress', 'cpress'); ?>
        </label>
        <br>
        <label for='within_dspace' class='td_lbl'>
            <input type='radio' name='collectionpress_settings_general[display_item]' id='within_dspace'
                <?php checked( $settings['display_item'], 'within_dspace' ); ?>
                value='within_dspace'> <?php echo __('By linking to DSpace', 'cpress'); ?>
        </label>
        <br>
        <div class='display_item_wrap'>
            <label for='handle_url'><?php echo __('Handle Prefix', 'cpress'); ?></label>
            <input type='text' name='collectionpress_settings_general[handle_url]' id='handle_url' class="regular-text"
                placeholder="E.g. http://domain.tld/handle"
                <?php if ($settings['display_item']!= 'within_dspace') {
                    echo "disabled='disabled'";
                } ?>
                value='<?php echo $settings['handle_url'] ?>' >
            <br>
            <span class="howto"><?php echo __('Specifies the page used by CollectionPress for displaying an item directly from DSpace.', 'cpress'); ?></span>
        </div>
        <?php
    }
}

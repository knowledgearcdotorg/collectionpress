<?php
if (!defined('WPINC')) {
    die;
}

/**
 * Provides a short code renderer.
 */
class CollectionPress_ShortCode
{
    public function render($atts)
    {
        if (isset($atts["author"])) {
            $this->get_items($atts["author"]);
        }
    }

    public function include_template_file($fileName, $response)
    {
        extract($response);

        if (file_exists(locate_template('collectionpress/'.$fileName))) {
            include(locate_template('collectionpress/'.$fileName));
	    } else {
            include(plugin_dir_path(__FILE__).'template/'.$fileName);
        }
	}

    public function get_items($author)
    {
        $options = collectionpress_settings();

        $args = array(
            'timeout'=>30,
            'user-agent'=>'CollectionPress; '.home_url()
        );

        $response = wp_remote_get($this->get_url('discover.json?q=author:"'.$author.'"'), $args);

        $response = json_decode(wp_remote_retrieve_body($response));

        $this->include_template_file("item_display.php",$response);
    }

    public function get_url($endpoint)
    {
        $options = collectionpress_settings();

        $url = $options['rest_url'];

        return $url."/".$endpoint;
    }
}

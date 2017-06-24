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

    public function get_items($author)
    {
        $options = collectionpress_settings();

        $args = array(
            'timeout'=>30,
            'user-agent'=>'CollectionPress; '.home_url()
        );

        $response = wp_remote_get($this->get_url('discover.json?q=author:"'.$author.'"'), $args);

        $response = json_decode(wp_remote_retrieve_body($response));

        echo "<ul>";

        foreach ($response->response->docs as $doc) {
            $parts = array();

            echo "<li>";

            if (is_array($title = $doc->title)) {
                $title = array_shift($title);

                if ($handle = $doc->handle) {
                    $url = $options['item_url']."/".$handle;

                    $title = sprintf('<a href="%s" target="_blank">%s</a>', $url, $title);
                }

                $parts[] = $title;
            }

            if (is_array($publisher = $doc->{"dc.publisher"})) {
                $parts[] = array_shift($publisher);
            }

            if (is_array($dateIssued = $doc->dateIssued)) {
                $parts[] = array_shift($dateIssued);
            }

            echo implode(", ", $parts);

            echo "</li>";
        }

        echo "</ul>";
    }

    public function get_url($endpoint)
    {
        $options = collectionpress_settings();

        $url = $options['rest_url'];

        return $url."/".$endpoint;
    }
}

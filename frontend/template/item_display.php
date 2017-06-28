<?php
/**
 * Template file of Collection Press To Author Display
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/item_display.php"
 * */

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

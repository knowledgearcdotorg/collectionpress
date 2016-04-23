<?php
class CollectionPress
{

    //self::retrieve($type = 'items', $id = '497');
    public static function render($atts)
    {
        if (isset($_REQUEST['id']) && $_REQUEST['id'] != '') {

            $id = $_REQUEST['id'];
        }
        if (isset($_REQUEST['type']) && $_REQUEST['type'] != '') {
            $type = $_REQUEST['type'];
        } else {
            $type = "items";
            $id = '497';
        }
        $result = array();

        $result = self::retrieve($type, $id);

        if (!empty($result)) {
            if (!isset($result['message'])) {
                switch ($type) {
                    case "items":
                        $metadata_element = array_column($result['metadata'], 'element');
                        $metadata_qualifier = array_column($result['metadata'], 'qualifier');
                        $metadata_value = array_column($result['metadata'], 'value');
                        $i = 0;
                        foreach ($metadata_element as $ele) {
                            //echo  $ele.'<br>';
                            // echo $metadata_qualifier[$i].'<br>';
                            // echo $metadata_value[$i];
                            if ($ele == "description" && $metadata_qualifier[$i] == "abstract") {
                                echo "<h1>Title,Abstract & Author</h1>";
                                echo "<h3>Title:</h3>" . $result['name'] . "<br>";
                                echo "<h3>Abstract:</h3> " . $metadata_value[$i];
                            }
                            $i++;
                        }
                        $metadata_element = array_column($result['metadata'], 'element');
                        $metadata_qualifier = array_column($result['metadata'], 'qualifier');
                        $metadata_value = array_column($result['metadata'], 'value');
                        $i = 0;
                        foreach ($metadata_element as $ele) {
                            if ($ele == "contributor" && $metadata_qualifier[$i] == "author") {
                                echo "<h3>Author:</h3> " . $metadata_value[$i];
                            }
                            $i++;
                        }

                        break;
                    case "collections":

                        echo '<pre>';
                        print_r($result);
                        echo '</pre>';
                        break;

                    case "communities":
                        echo '<pre>';
                        print_r($result);
                        echo '</pre>';

                        break;
                }
            } else {
                echo '<pre>';
                print_r($result);
                echo '</pre>';
            }
        }
    }

    public static function retrieve($type = '', $id = '')
    {
        $error = array();
        $headers = array(
            "user:joomla@bora.hib.local",
            "pass:J7URkJQ9R9Rp",
            "Content-Type: text/json"
        );
        $html_brand = "https://archive.bora.hib.no/rest/" . $type;
        $html_brand.= ($id != '') ? "/" . $id . ".json" : ".json";


        $curlHandle = curl_init();
        $options = array(
            CURLOPT_URL => $html_brand,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        );
        curl_setopt_array($curlHandle, $options);
    // Execute
        $jsonEncodedApiResponse = curl_exec($curlHandle);

        // Ensure HTTP call was successful
        try {
            try {
                if ($jsonEncodedApiResponse === false) {
                    throw new \RuntimeException(
                    'API call failed with cURL error: ' . curl_error($curlHandle)
                    );
                }
            } catch (RuntimeException $e) {
                throw $e;
            }
        } catch (Exception $e) {
            $error['message'] = 'Message: ' . $e->getMessage();
        }
        // Clean up the resource now that we're done with cURL
        curl_close($curlHandle);

        // Decode the response from a JSON string to a PHP associative array
        $apiResponse = json_decode($jsonEncodedApiResponse, true);

        // Make sure we got back a well-formed JSON string and that there were no
        // errors when decoding it
        $jsonErrorCode = json_last_error();
        try {
            try {
                if ($jsonErrorCode !== JSON_ERROR_NONE) {
                    throw new \RuntimeException(
                    'API response not well-formed (json error code: ' . $jsonErrorCode . ')'
                    );
                }
            } catch (RuntimeException $e) {
                throw $e;
            }
        } catch (Exception $e) {
            $error['message'] = 'Message: ' . $e->getMessage();
        }
        if (empty($error)) {
            return $apiResponse;
        } else {
            return $error;
        }
    }
}

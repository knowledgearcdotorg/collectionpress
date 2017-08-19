<?php
/**
 * Import Authors by CSV
 */

$i=0;
$paged =1;
if (isset($_GET['paged']) && $_GET['paged']!='') {
    $paged = $_GET['paged'];
}
$upload_dir = wp_upload_dir();
$path = $upload_dir['baseurl'];
$basedir = $upload_dir['basedir'];

?>
<p>
    <a href="<?php echo admin_url('admin.php?page=collectionpress-import')?>" class="button button-primary" >
        <?php echo __("Back to Import Page", 'cpress'); ?>
    </a>
</p>
<?php
//Processing Post Values
 
if (isset($_POST['import_author_nonce']) && wp_verify_nonce($_POST['import_author_nonce'], 'import_author_nonce')) {  
    //~ unset($_POST['import_author_nonce']);
    $uploadedfile = $_FILES['import_file'];
    $skip_lines = $_POST['skip_lines'];
    $names_per_page = $_POST['names_per_page'];
    
    if ($uploadedfile) {
        if (! function_exists('wp_handle_upload')) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        $extension_array = explode(".", $_FILES["import_file"]["name"]);
        $extension = end($extension_array);
        if ($extension=="csv") {
            if (!file_exists("{$basedir}/import")) {
                mkdir("{$basedir}/import", 0755, true);
            }
            $new_name = 'csv-'.time().".csv";
            $movefile = move_uploaded_file($_FILES["import_file"]["tmp_name"],
                "{$basedir}/import/" .$new_name );
            if ($movefile) {
                $url  = '&filename='.$new_name;
                $url .= '&skip_lines='.$skip_lines;
                $url .= '&names_per_page='.$names_per_page;
                $redirect_url = admin_url('admin.php?page=collectionpress-csv-import'.$url);
                ?>
                <script>
                    window.location = "<?= $redirect_url ?>"; 
                </script>
                <?php
            }
        } else {
            echo '<div class="wrap">
                    <div class="updated notice notice-success is-dismissible" id="message"><p>'.__('Use CSV File only', 'cpress').'.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">'.__('Dismiss this notice', 'cpress').'.</span></button></div>
                </div>';
        }
    } else {
        echo '<div class="wrap">
                <div class="updated notice notice-success is-dismissible" id="message"><p>'.__('You have to upload the file', 'cpress').'.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">'.__('Dismiss this notice', 'cpress').'.</span></button></div>
            </div>';
    }
}
// Get variables
$new_name = $_GET['filename'];
$skip_lines = $_GET['skip_lines'];
$names_per_page = $_GET['names_per_page'];

if ($paged>1) {
    $skip_authors = (($paged-1)*$names_per_page)+ $skip_lines;
} else {
    $skip_authors = $skip_lines;
}

if (isset($_POST['checkbox_nonce']) && wp_verify_nonce($_POST['checkbox_nonce'], 'checkbox_nonce')) {
    $author_name = $_POST['author_name'];
    $file = fopen("{$basedir}/import/{$new_name}", 'r');
    $post_count =0;
    while (!feof($file)) {
        $fcsv = fgetcsv($file);  // Store every line in an array
        $data[] = $fcsv;
        if (in_array($post_count, $author_name)) {
            if (!empty($fcsv[0])) {
                $lastname= $fcsv[0];
                $firstname= $fcsv[1];
                $author_keyword = strtolower($lastname).', '.strtolower($firstname);
                $my_post['post_title']    = $lastname.', '.$firstname;		
                $my_post['post_status']   = 'publish';
                $my_post['post_type']     = 'cp_authors';
                $my_post['post_author']   = get_current_user_id();
                // Insert the post into the database
                $post_id = wp_insert_post( $my_post );
                if ($post_id) {
                    update_post_meta($post_id, "author_keyword", $author_keyword);
                    update_post_meta($post_id, "show_items", "yes");
                    update_post_meta($post_id, "show_posts", "no");
                }
            }           
        }
        $post_count++;        
    }
    fclose($file);
    echo '<div class="wrap">
                <div class="updated notice notice-success is-dismissible" id="message"><p>'.__('Successfully Imported', 'cpress').'.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">'.__('Dismiss this notice', 'cpress').'.</span></button></div>
            </div>';
}

$file = fopen("{$basedir}/import/{$new_name}", 'r');
$file_content = file("{$basedir}/import/{$new_name}", FILE_SKIP_EMPTY_LINES);
$found_result = count($file_content);
$total_pages  = ceil($found_result/$names_per_page);

//Showing names with checkbox in form
?>
<h3><?php echo __("Author", 'cpress') ?></h3>
<p class="h6"><?php echo __("Select which author to publish", 'cpress') ?></p>
<p><label><input type="checkbox" name="select_all" value="all" id="chkselectall"><?php echo __('Select All', 'cpress'); ?></label></p>
<form action="" method="POST">
    <?php  wp_nonce_field('checkbox_nonce', 'checkbox_nonce'); ?>
    <table>
        <?php $counter=0; ?>
        <?php while (!feof($file)) : ?>
            <?php $fcsv = fgetcsv($file);  // Store every line in an array
            $data[] = $fcsv;
            if ($i>=$skip_authors) {
                if (!empty($fcsv[0])) {
                    $lastname= $fcsv[0];
                    $firstname= $fcsv[1];
                    $name = $lastname.', '.$firstname;
                    ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" class="cpr_chkbox" name="author_name[<?= $i ?>]" value="<?= $i ?>">
                                <?php echo $name ?>
                            </label>
                        </td>
                    </tr>
                <?php
                }
                $counter++;
                if ($names_per_page==$counter) {
                    break;
                }                
            }
            $i++;
        endwhile;
        fclose($file); 
        ?>
    </table>
    <?php if ($total_pages>1) : ?>
        <div class="pagination">
            <?php
                $big = 999999999; // need an unlikely integer               
                echo paginate_links(array(
                    //'base'      =>str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format'    => '?paged=%#%', 
                    'prev_text' =>__('&laquo;'),
                    'next_text' =>__('&raquo;'),
                    'current'   => max(1, $paged),
                    'total'     =>$total_pages
                ));
            ?>
        </div>
    <?php endif; ?>
    <?php submit_button(); ?>                 
</form>
<script type="text/javascript">
jQuery(document).ready(function($){
    $("#chkselectall").change(function(){
        $(".cpr_chkbox").prop('checked', $(this).prop("checked")); 
    });

    $('.cpr_chkbox').change(function(){
        if(false == $(this).prop("checked")){
            $("#chkselectall").prop('checked', false);
        }
        //check "select all" if all checkbox items are checked
        if ($('.cpr_chkbox:checked').length == $('.cpr_chkbox').length ){
            $("#chkselectall").prop('checked', true);
        }
    });
});
</script>

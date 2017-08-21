<?php
/**
 * Import Authors by CSV
 */

$i=0;
$paged =1;
if (isset($_GET['paged']) && $_GET['paged']!='') {
    $paged = $_GET['paged'];
}

?>
<p>
    <a href="<?php echo admin_url('admin.php?page=collectionpress-import')?>" class="button button-primary" >
        <?php echo __("Back to Import Page", 'cpress'); ?>
    </a>
</p>
<style>
.pagination {
    border-top: 1px solid #eee;
    font-size: 0.875rem;
    font-weight: 800;
    padding: 2em 0 3em;
    text-align: center;
}
.page-numbers.current {
    font-size: 0.9375rem;
}
.page-numbers.current {
    color: #767676;
    display: inline-block;
}
.page-numbers {
    padding: 0.5em 0.75em;
}
.page-numbers {
    display: inline-block;
}
.prev.page-numbers, .next.page-numbers {
    transition: background-color 0.2s ease-in-out 0s, border-color 0.2s ease-in-out 0s, color 0.3s ease-in-out 0s;
}
.prev.page-numbers, .next.page-numbers {
    background-color: #ddd;
    border-radius: 2px;
    display: inline-block;
    font-size: 1.5rem;
    line-height: 1;
    padding: 0.25em 0.5em 0.4em;
}
</style>
<?php
//Processing Post Values
 
if (isset($_POST['import_author_nonce']) && wp_verify_nonce($_POST['import_author_nonce'], 'import_author_nonce')) {  
    //~ unset($_POST['import_author_nonce']);
    $rest_url = $_POST['rest_url'];
    $skip_lines = $_POST['skip_lines'];
    $names_per_page = $_POST['names_per_page'];
    
    if ($rest_url) {
        $url  = '&rest_url='.$rest_url;
        $url .= '&skip_lines='.$skip_lines;
        $url .= '&names_per_page='.$names_per_page;
        $redirect_url = admin_url('admin.php?page=collectionpress-solr-import'.$url);
        ?>
        <script>
            window.location = "<?= $redirect_url ?>"; 
        </script>
        <?php
    } else {
        echo '<div class="wrap">
                <div class="updated notice notice-success is-dismissible" id="message"><p>'.__('REST URL is important for  SOLR Query to work', 'cpress').'.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">'.__('Dismiss this notice', 'cpress').'.</span></button></div>
            </div>';
    }
    
}
// Get variables
$rest_url = $_GET['rest_url'];
$skip_lines = $_GET['skip_lines'];
$names_per_page = $_GET['names_per_page'];

if (!isset($_GET['rest_url']) || !isset($_GET['skip_lines']) || !isset($_GET['names_per_page'])) {
    wp_die('<div class="wrap">
                <div class="updated notice notice-success is-dismissible" id="message"><p>'.__('Cheating ahhhh...', 'cpress').'.</p></div>
            </div>');
}

if ($paged>1) {
    $skip_authors = (($paged-1)*$names_per_page)+ $skip_lines;
} else {
    $skip_authors = $skip_lines;
}

if (isset($_POST['checkbox_nonce']) && wp_verify_nonce($_POST['checkbox_nonce'], 'checkbox_nonce')) {
    $author_name = $_POST['author_name'];
    $post_count =0;
    $args = array(
                'timeout'=>30,
                'user-agent'=>'CollectionPress; '.home_url()
            );
    $response = wp_remote_get($rest_url.'/discover.json?q=*.*&rows=0&facet=true&facet.field=author_filter&facet.limit='.$names_per_page.'&facet.offset='.$skip_authors, $args);
    $response = json_decode(wp_remote_retrieve_body($response));
    $found_result = count($response->facet_counts->facet_fields->author_filter);
    if ($found_result) {
        $author_keyword = array_filter($response->facet_counts->facet_fields->author_filter);        
        foreach ($author_keyword as $author) {
            if ($author!=0 || !empty($author)) {
                if (in_array($post_count, $author_name)) {
                    $author_array   = explode("\n|||\n", $author);
                    $author_keyword = $author_array[0];
                    $author_n = $author_array[1];
                    $my_post['post_title']    = $author_n;
                    $my_post['post_status']   = 'publish';
                    $my_post['post_type']     = 'cp_authors';
                    $my_post['post_author']   = get_current_user_id();
                    //Check if author existed in cp_authors-post type
                    if (post_exists($my_post['post_title'])==0) {
                        // Insert the post into the database if auhtor not exists.
                        $post_id = wp_insert_post( $my_post );
                        if ($post_id) {
                            update_post_meta($post_id, "author_keyword", $author_keyword);
                            update_post_meta($post_id, "show_items", "yes");
                            update_post_meta($post_id, "show_posts", "no");
                        }
                    }
                }
            }
            $post_count++; 
        }
    }    
    echo '<div class="wrap">
                <div class="updated notice notice-success is-dismissible" id="message"><p>'.__('Successfully Imported', 'cpress').'.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">'.__('Dismiss this notice', 'cpress').'.</span></button></div>
            </div>';
}



//Showing names with checkbox in form
?>
<h3><?php echo __("Author", 'cpress') ?></h3>
<p class="h6"><?php echo __("Select which author to publish", 'cpress') ?></p>
<p><label><input type="checkbox" name="select_all" value="all" id="chkselectall"><?php echo __('Select All', 'cpress'); ?></label></p>
<form action="" method="POST">
    <?php  wp_nonce_field('checkbox_nonce', 'checkbox_nonce'); ?>
    <table>
        <?php
            $counter=0;
            $args = array(
                'timeout'=>30,
                'user-agent'=>'CollectionPress; '.home_url()
            );
            $total_response = wp_remote_get($rest_url.'/discover.json?q=*.*&rows=0&facet=true&facet.field=author_filter&facet.offset='.$skip_lines, $args);
            $total_response = json_decode(wp_remote_retrieve_body($total_response));
            $total_found_result = count($total_response->facet_counts->facet_fields->author_filter);
            $found_result = ceil($total_found_result/2);
            $total_pages  = ceil($found_result/$names_per_page);
            $response = wp_remote_get($rest_url.'/discover.json?q=*.*&rows=0&facet=true&facet.field=author_filter&facet.limit='.$names_per_page.'&facet.offset='.$skip_authors, $args);
            $response = json_decode(wp_remote_retrieve_body($response));
            $found_result = count($response->facet_counts->facet_fields->author_filter);
            
            if ($found_result) {
                $author_keyword = array_filter($response->facet_counts->facet_fields->author_filter);
                
                foreach ($author_keyword as $author) {
                    if ($author!=0 || !empty($author)) {
                        $author_array = explode("\n|||\n", $author);
                        $author_keyword = $author_array[0];
                        $author_name = $author_array[1];
                        ?>
                        <tr>
                            <td>
                                <label>
                                    <input type="checkbox" class="cpr_chkbox" name="author_name[<?= $counter ?>]" value="<?= $counter ?>">
                                    <?php echo $author_name ?>
                                </label>
                            </td>
                        </tr>
                        <?php
                    }
                    $counter++;
                }
            } else {

            }
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

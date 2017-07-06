<?php
/**
 * Register Custom Post Type for Authors and Author's items
 * @author Avinash
 **/
if (!defined('WPINC')) {
    die;
}


class CP_Author
{

    private $nonce = 'cp_author_nonce';
    
    
    public function __construct()
    {
        global $item_response;
        add_action( 'init', array( $this, 'register_post_author' ) );
        add_filter( 'template_include', array( $this,'cp_custom_single' ) );
        add_filter( 'enter_title_here', array( $this,'cp_custom_title' ) );
        add_filter( 'page_template', array( $this,'cp_author_template' ) );
        add_action( 'add_meta_boxes', array( $this,'cp_author_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_author_data' ) );
        //~ add_action( 'show_item_details', array( $this, 'cp_get_item_by_id' ), 10 ,1 );
        add_action( 'init', array( $this, 'cp_rewrite_rule' ),10,0 );
        add_action( 'init', array( $this, 'cp_rewrite_link' ),10,0 );
       
    }

    public function cp_rewrite_rule()
    {
        $page = get_page_by_path( 'items' );
        
        add_rewrite_rule( '^items/([0-9]+)?',
            'index.php?page_id='.$page->ID.'&item_id=$matches[1]',
            'top' );
      
    }
    
    public function cp_rewrite_link()
    {
        add_rewrite_tag( 'aposts', '([0-9]+)' );
        add_rewrite_tag( '%item_id%', '([^&]+)' );
    }
    
    public function register_post_author()
    {
        $labels = array(
            'name'               => __( 'Authors' ),
            'singular_name'      => __( 'Author'),
            'menu_name'          => __( 'Authors'),
            'name_admin_bar'     => __( 'Authors' ),
            'add_new'            => __( 'Add' ),
            'add_new_item'       => __( 'Add New' ),
            'new_item'           => __( 'New' ),
            'edit_item'          => __( 'Edit' ),
            'view_item'          => __( 'View' ),
            'all_items'          => __( 'All' ),
            'search_items'       => __( 'Search' ),
            'parent_item'        => __( 'Parent' ),
            'parent_item_colon'  => __( 'Parent:' ),
            'not_found'          => __( 'Nothing found.' ),
            'not_found_in_trash' => __( 'Nothing found in Trash.' )
            );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'cp-author' ),
            'taxonomies' => array(''), 
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor',  'thumbnail' ),
            );
        register_post_type( 'cp_authors', $args );
        
    }

    public function cp_custom_single( $template )
    {
        $post_types = array('cp_authors');
        
        if (is_singular($post_types)) {
            if (!file_exists(locate_template('single-cp_authors.php'))) {
                
                $template = CP_TEMPLATE_PATH.'/single-cp_authors.php';
            }
        }

        return $template;
    }

    public function cp_custom_title( $title )
    {
        $screen = get_current_screen();

        if ( 'cp_authors' == $screen->post_type ){
            $title = __('Enter Author Name here', 'cpress' );
        }
        
        return $title;
    }

    public function cp_author_template($page_template)
    {
        if ( is_page( 'author-list' ) ) {
            $templatefilename = 'cp_author_list.php';
            $page_template = CP_TEMPLATE_PATH.'/collectionpress/'.$templatefilename;
        }
        if ( is_page( 'items' ) ) {
            $templatefilename = 'cp_item.php';
            $page_template = CP_TEMPLATE_PATH.'/collectionpress/'.$templatefilename;
        }
        return $page_template;
    
    }

    public function cp_author_meta_box()
    {
        add_meta_box( 'author-info', __('Author Info','cpress' ),  array( $this, 'cp_author_info_box'), "cp_authors", 'normal', 'high');	
    }
    
    public function cp_author_info_box($post)
    {
        global $pagenow;
        global $typenow;
        wp_enqueue_script("jquery-ui-autocomplete");
        $show_items = get_post_meta($post->ID,"show_items",true);
        $author_keyword = get_post_meta($post->ID,"author_keyword",true);
        $show_posts = get_post_meta($post->ID,"show_posts",true);
        $cp_related_author = get_post_meta($post->ID,"cp_related_author",true);
        wp_nonce_field( 'author_meta_nonce', 'author_meta_nonce' );
        $authorusers = get_users( 'orderby=nicename&role=author' );
        
        ?>
        <style>
        .ui-front{z-index:9999!important;}
        .inside p label{ font-weight:bold;}
        </style>

        <div class='inside'>
            <p>
                <label for='show_items'>
                    <?php echo __('Show items for this Author','cpress') ?>:
                    <input type='checkbox' name='show_items' id='show_items' value='yes'
                        <?php if( $show_items=='yes' || $show_items=='' ) echo 'checked="checked"' ?> />
                        <?php echo __('Yes','cpress') ?>
                </label>
            </p>
            <p>
                <label for='show_posts'>
                    <?php echo __('Show posts for this Author','cpress') ?>:
                    <input type='checkbox' name='show_posts' id='show_posts' value='yes'
                        <?php if( $show_items=='yes' || $show_items=='' ) echo 'checked="checked"' ?> />
                        <?php echo __('Yes','cpress') ?>
                </label>
            </p>
            <p>
                <label for='cp_related_author'>
                    <?php echo __('Select Author','cpress') ?>:
                </label>
                <select name="cp_related_author" id="cp_related_author" >
                    <option value="" ><?php echo  __( 'Select', 'cpress' )?> </option>
                    <?php foreach($authorusers as $buser): ?>
                        <option value="<?= $buser->ID ?>"
                            <?php if(isset($buser->ID)) if($cp_related_author==$buser->ID) echo "selected='selected'";?>>
                            <?= $buser->user_login; ?>
                        </option>
                    <?php endforeach; ?>
                </select>               
            </p>
            <input type='hidden' name='author_keyword' id='author_keyword' value='<?php echo $author_keyword ?>' />
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($){
                var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' );?>";
                 var title = $("#title");
                title.addClass('search_authors')
                title.keypress(function(event){
                    if (event.keyCode == 10 || event.keyCode == 13)
                        event.preventDefault();
                });

                title.keyup(function(){
                   title.autocomplete({
                        source:ajaxurl+"?action=cp_get_author_ajax&process=true&nextNonce=SearchAuthor",
                        minLength:3,
                        select: function( event, ui ) {
                            event.preventDefault();
                            var com_label=ui.item.label;
                            title.val(com_label);
                            $("#author_keyword").val(com_label);
                        },
                    });			
                });
            });
        </script>
            
        <?php 
    }

    public function save_author_data($post_id)
    {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
            return $post_id;
        }
        if ( !current_user_can( 'edit_post', $post_id ) ){
            return $post_id;
        }
        
        $post = get_post($post_id);

        if(isset( $_POST['author_meta_nonce']) && wp_verify_nonce( $_POST['author_meta_nonce'], 'author_meta_nonce' )){
            $show_items = (isset($_POST['show_items'])? ($_POST['show_items']): "no");
            update_post_meta($post_id,'show_items',$show_items);	
            $show_posts = (isset($_POST['show_posts'])? ($_POST['show_posts']): "no");
            update_post_meta($post_id,'show_posts',$show_posts);	
            $author_keyword = (isset($_POST['author_keyword'])? ($_POST['author_keyword']): "");
            update_post_meta($post_id,'author_keyword',$author_keyword);	
            $cp_related_author = (isset($_POST['cp_related_author'])? ($_POST['cp_related_author']): "");
            update_post_meta($post_id,'cp_related_author',$cp_related_author);

            update_user_meta($cp_related_author,'show_posts',$show_posts);	
        }
        return $post_id;
    }

    public function cp_get_author_by_api()
    {
        if(isset($_REQUEST) && isset($_REQUEST['process']) && $_REQUEST['process']==true
            && isset($_REQUEST['nextNonce']) && $_REQUEST['nextNonce']=='SearchAuthor')
        {
            $search_term = strtolower($_REQUEST['term']);  
            $result=[];
            $collection_shortcode = new CollectionPress_ShortCode();
            $options = collectionpress_settings();

            $args = array(
                'timeout'=>30,
                'user-agent'=>'CollectionPress; '.home_url()
            );

            $response = wp_remote_get($collection_shortcode->get_url('discover.json?q:&rows=0&facet=true&facet.field=author_filter&facet.prefix='.$search_term), $args);
               
            $response = json_decode(wp_remote_retrieve_body($response));
            if(count($response->response->numFound)){
                $author_keyword = array_filter($response->facet_counts->facet_fields->author_filter);
                
                foreach($author_keyword as $author){
                    $author_name = end(explode("\n|||\n", $author) );
                    $result[] = array(
                            'label'=> html_entity_decode($author_name),
                            );
                }
                echo json_encode($result);
            }else{
                $result[] = array(
						'label'=> __("Oops our bad ! No match available.",'cpress'),
						);
				echo json_encode($result);
            }
        }
        wp_die();
    }

    public function cp_get_item_by_id($id)
    {
        $collection_shortcode = new CollectionPress_ShortCode();
        $options = collectionpress_settings();

        $args = array(
            'timeout'=>30,
            'user-agent'=>'CollectionPress; '.home_url()
        );

        $response = wp_remote_get($collection_shortcode->get_url('items/'.$id.'.json'), $args);
           
        $response = json_decode(wp_remote_retrieve_body($response));
        return $response;
    }
}

return new CP_Author();


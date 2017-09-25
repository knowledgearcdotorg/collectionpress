<?php
/**
 * Register Custom Post Type for Authors and Author's items
 * @author Avinash
 **/
if (!defined('WPINC')) {
    die;
}


class CPR_AuthorReg
{
    private $nonce = 'cp_author_nonce';
    
    public function __construct()
    {
        global $item_response;
        add_action('init', array($this, 'cpr_register_post_author'));
        add_filter('enter_title_here', array($this, 'cpr_custom_title'));
        add_action('add_meta_boxes', array($this, 'cpr_author_meta_box'));
        add_action('save_post', array($this, 'cpr_save_author_data'));
        add_action('init', array($this, 'cpr_rewrite_rule'), 10, 0);
        add_action('init', array($this, 'cpr_rewrite_link'), 10, 0);
        add_action('the_content', array($this, 'cpr_custom_content'), 10, 1);
        // Load frontend JS & CSS
        add_action('wp_enqueue_scripts', array($this, 'cp_register_styles'), 700);
        add_action('cpr_styles', array($this, 'cp_enqueue_styles'));
        add_action('wp_head', array($this, 'cpr_add_styles_head'));
        //Adding Custom classes in body
        add_filter('body_class', array($this, 'cpr_body_classes'));
    }

    public function cpr_rewrite_rule()
    {
        $options = collectionpress_settings();
        if (!empty($options) && isset($options['item_page']) && $options['item_page']) {
            $page_id = $options['item_page'];
        } else {
            $page = get_page_by_path('items');
            $page_id = $page->ID;
        }
        add_rewrite_rule('^items/([0-9]+)?',
            'index.php?page_id='.$page_id.'&item_id=$matches[1]',
            'top');      
    }
    
    public function cpr_rewrite_link()
    {
        add_rewrite_tag('aposts', '([0-9]+)');
        add_rewrite_tag('citem', '([0-9]+)');
        add_rewrite_tag('cauthors', '([0-9]+)');
        add_rewrite_tag('clists', '([0-9]+)');
        add_rewrite_tag( '%item_id%', '([^&]+)');
    }
    
    public function cpr_register_post_author()
    {
        $labels = array(
            'name'               => __('Authors', 'cpress'),
            'singular_name'      => __('Author', 'cpress'),
            'menu_name'          => __('Authors', 'cpress'),
            'name_admin_bar'     => __('Authors' , 'cpress'),
            'add_new'            => __('Add'),
            'add_new_item'       => __('Add New'),
            'new_item'           => __('New'),
            'edit_item'          => __('Edit'),
            'view_item'          => __('View'),
            'all_items'          => __('All'),
            'search_items'       => __('Search'),
            'parent_item'        => __('Parent'),
            'parent_item_colon'  => __('Parent:'),
            'not_found'          => __('Nothing found.'),
            'not_found_in_trash' => __('Nothing found in Trash.')
            );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'cp-author'),
            'taxonomies'         => array(''),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor',  'thumbnail'),
            );
        register_post_type('cp_authors', $args);        
    }

    public function cpr_custom_title($title)
    {
        $screen = get_current_screen();
        if ('cp_authors' == $screen->post_type){
            $title = __('Enter Author Name here', 'cpress');
        }        
        return $title;
    }
    public function cpr_add_styles_head()
    {
        $cpr_author_page='';
        $cpr_item_page='';
        $options = collectionpress_settings();
        if (!empty($options) && isset($options['author_page']) && $options['author_page']) {
            $cpr_author_page = $options['author_page'];
        }
        if (!empty($options) && isset($options['item_page']) && $options['item_page']) {
            $cpr_item_page = $options['item_page'];
        }
               
        global $post;
		if (is_page('author-list') || is_page($cpr_author_page) || get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_author_list.php") {
            do_action("cpr_styles"); 
        }
        if (is_page('items') || is_page($cpr_item_page) || get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_item.php") {
            do_action("cpr_styles"); 
        }

        if (is_singular('cp_authors')) {
            do_action("cpr_styles"); 
        }
    }

    public function cpr_custom_content($content)
    {
        global $post;
        $post_content = $content;
        //Author Single Page Content Customization
        if (is_singular('cp_authors')) {
            if (locate_template('template/collectionpress/single_cp_authors-content.php')) {
                include(locate_template('template/collectionpress/single_cp_authors-content.php'));
            }else {
                include(CPR_TEMPLATE_PATH.'/collectionpress/single_cp_authors-content.php');
            }
        }
        //Author and Item List Page Content Customization
        $cpr_author_page='';
        $cpr_item_page='';
        $options = collectionpress_settings();
        if (!empty($options) && isset($options['author_page']) && $options['author_page']) {
            $cpr_author_page = $options['author_page'];
        }
        if (!empty($options) && isset($options['item_page']) && $options['item_page']) {
            $cpr_item_page = $options['item_page'];
        }
        if (is_page('author-list') || is_page($cpr_author_page)) {
            //~ ob_start();
            if (is_page('author-list') || is_page($cpr_author_page)) {
                if (locate_template('template/collectionpress/cp_author_list-content.php')) {
                    get_template_part('template/collectionpress/cp_author_list','content'); 
                }else {
                    include(CPR_TEMPLATE_PATH.'/collectionpress/cp_author_list-content.php');
                }
            }
            //~ $post_content .= ob_get_clean();
        }
        if (is_page('items') || is_page($cpr_item_page)) {
            if (is_page('items') || is_page($cpr_item_page)) {
                if (locate_template('template/collectionpress/cp_item-content.php')) {
                    get_template_part('template/collectionpress/cp_item','content');
                } else {
                    include(CPR_TEMPLATE_PATH.'/collectionpress/cp_item-content.php');
                }
            }
        }
        return $post_content ;
    }

    public function cpr_author_meta_box()
    {
        add_meta_box('author-info', __('Author Info','cpress'),  array($this, 'cpr_author_info_box'),
            "cp_authors", 'normal', 'high');	
    }
    
    public function cpr_author_info_box($post)
    {
        global $pagenow;
        global $typenow;
        wp_enqueue_script("jquery-ui-autocomplete");
        $show_items = get_post_meta($post->ID, "show_items", true);
        $author_keyword = get_post_meta($post->ID,"author_keyword", true);
        $show_posts = get_post_meta($post->ID,"show_posts", true);
        $cp_related_author = get_post_meta($post->ID, "cp_related_author", true);
        $author_title = get_post_meta($post->ID, "author_title", true);
        $author_department = get_post_meta($post->ID, "author_department", true);
        $author_location = get_post_meta($post->ID, "author_location", true);
        $author_phone = get_post_meta($post->ID, "author_phone", true);
        $author_email = get_post_meta($post->ID, "author_email", true);
        $author_research_ints = get_post_meta($post->ID, "author_research_ints", true);
        $author_teaching_ints = get_post_meta($post->ID, "author_teaching_ints", true);
        $author_orcid = get_post_meta($post->ID, "author_orcid", true);
        wp_nonce_field('author_meta_nonce', 'author_meta_nonce');
        $authorusers = get_users('orderby=nicename&role=author');
        ?>
        <style>
        .ui-front{z-index:9999!important;}
        .inside p label{ font-weight:bold;}
        </style>
        <div class='inside'>
            <p>
                <label for='show_items'>
                    <?php echo __('Show items for this Author', 'cpress') ?>:
                    <input type='checkbox' name='show_items' id='show_items' value='yes'
                        <?php if ($show_items=='yes' || $show_items=='') :
                            echo 'checked="checked" ';
                        endif; ?> />
                        <?php echo __('Yes', 'cpress') ?>
                </label>
            </p>
            <p>
                <label for='show_posts'>
                    <?php echo __('Show posts for this Author', 'cpress') ?>:
                    <input type='checkbox' name='show_posts' id='show_posts' value='yes'
                        <?php if ($show_posts=='yes' || $show_posts=='') :
                            echo 'checked="checked" ';
                        endif; ?> />
                        <?php echo __('Yes', 'cpress') ?>
                </label>
            </p>
            <p>
                <label for='cp_related_author'>
                    <?php echo __('Select Author', 'cpress') ?>:
                </label>
                <select name="cp_related_author" id="cp_related_author" >
                    <option value="" ><?php echo  __( 'Select', 'cpress' )?> </option>
                    <?php foreach ($authorusers as $buser) : ?>
                        <option value="<?php echo  $buser->ID ?>"
                            <?php if (isset($buser->ID)) :
                                if ($cp_related_author==$buser->ID) :
                                    echo "selected='selected' ";
                                endif;
                            endif; ?> >
                            <?php echo  $buser->user_login; ?>
                        </option>
                    <?php endforeach; ?>
                </select>               
            </p>
            <p>
                <label for='author_title'>
                    <?php echo __('Title', 'cpress') ?>:
                </label>
                <input type='text' name='author_title' id='author_title' value='<?php echo  $author_title ?>'
                         class="input-text regular-text" />
            </p>
            <p>
                <label for='author_department'>
                    <?php echo __('Department', 'cpress') ?>:
                </label>
                <input type='text' name='author_department' id='author_department' value='<?php echo  $author_department ?>'
                         class="input-text regular-text" />
            </p>
            <p>
                <label for='author_location'>
                    <?php echo __('Location', 'cpress') ?>:
                </label>
                <input type='text' name='author_location' id='author_location' value='<?php echo  $author_location ?>'
                         class="input-text regular-text" />
            </p>
            <p>
                <label for='author_phone'>
                    <?php echo __('Phone Number', 'cpress') ?>:
                </label>
                <input type='text' name='author_phone' id='author_phone' value='<?php echo  $author_phone ?>'
                         class="input-text regular-text" />
            </p>
            <p>
                <label for='author_email'>
                    <?php echo __('Email', 'cpress') ?>:
                </label>
                <input type='email' name='author_email' id='author_email' value='<?php echo  $author_email ?>'
                         class="input-text regular-text" />
            </p>
            <p>
                <label for='author_research_ints'>
                    <?php echo __('Research Interests', 'cpress') ?>:
                </label>
                <input type='text' name='author_research_ints' id='author_research_ints' value='<?php echo  $author_research_ints ?>'
                         class="input-text regular-text" />
            </p>
            <p>
                <label for='author_teaching_ints'>
                    <?php echo __('Teaching Interests', 'cpress') ?>:
                </label>
                <input type='text' name='author_teaching_ints' id='author_teaching_ints' value='<?php echo  $author_teaching_ints ?>'
                         class="input-text regular-text" />
            </p>
            <p>
                <label for='author_orcid'>
                    <?php echo __('ORCID ID', 'cpress') ?>:
                </label>
                <input type='text' name='author_orcid' id='author_orcid' value='<?php echo  $author_orcid ?>'
                     class="input-text regular-text" />
            </p>
            <input type='hidden' name='author_keyword' id='author_keyword' value='<?php echo $author_keyword ?>' />
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($){
                var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' );?>";
                var title = $("#title");
                title.addClass('search_authors')
                title.keypress(function(event){
                    if (event.keyCode == 10 || event.keyCode == 13){
                        event.preventDefault();
                    }
                });

                title.keyup(function(){
                   title.autocomplete({
                        source:ajaxurl+"?action=cpr_get_author_ajax&process=true&nextNonce=SearchAuthor",
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

    public function cpr_save_author_data($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (! current_user_can( 'edit_post', $post_id)) {
            return $post_id;
        }
        
        $post = get_post($post_id);

        if (isset($_POST['author_meta_nonce']) && wp_verify_nonce($_POST['author_meta_nonce'], 'author_meta_nonce')) {
            unset($_POST['author_meta_nonce']);
            $show_items = (isset($_POST['show_items']) ? "yes" : "no");
            update_post_meta($post_id, 'show_items', $show_items);	

            $show_posts = (isset($_POST['show_posts']) ? "yes" : "no");
            update_post_meta($post_id, 'show_posts', $show_posts);	

            $author_keyword = (isset($_POST['author_keyword']) ? (sanitize_text_field($_POST['author_keyword'])) : "");
            update_post_meta($post_id, 'author_keyword', $author_keyword);

            $author_title = (isset($_POST['author_title']) ? (sanitize_text_field($_POST['author_title'])) : "");
            update_post_meta($post_id, 'author_title', $author_title);

            $author_department = (isset($_POST['author_department']) ? (sanitize_text_field($_POST['author_department'])) : "");
            update_post_meta($post_id, 'author_department', $author_department);

            $author_location = (isset($_POST['author_location']) ? (sanitize_text_field($_POST['author_location'])) : "");
            update_post_meta($post_id, 'author_location', $author_location);

            $author_phone = (isset($_POST['author_phone']) ? (sanitize_text_field($_POST['author_phone'])) : "");
            update_post_meta($post_id, 'author_phone', $author_phone);

            $author_email = (isset($_POST['author_email']) ? (sanitize_text_field($_POST['author_email'])) : "");
            update_post_meta($post_id, 'author_email', $author_email);

            $author_research_ints = (isset($_POST['author_research_ints']) ? (sanitize_text_field($_POST['author_research_ints'])) : "");
            update_post_meta($post_id, 'author_research_ints', $author_research_ints);

            $author_teaching_ints = (isset($_POST['author_teaching_ints']) ? (sanitize_text_field($_POST['author_teaching_ints'])) : "");
            update_post_meta($post_id, 'author_teaching_ints', $author_teaching_ints);

            $author_orcid = (isset($_POST['author_orcid']) ? (sanitize_text_field($_POST['author_orcid'])) : "");
            update_post_meta($post_id, 'author_orcid', $author_orcid);

            $authorusers = get_users('orderby=nicename&role=author');
            $all_user_ids = [];

            foreach ($authorusers as $buser) {
                $all_user_ids[] = $buser->ID;
            }
            
            $cp_related_author = (isset($_POST['cp_related_author']) ? (sanitize_text_field($_POST['cp_related_author'])): "");
            if ($cp_related_author) {
                if (in_array( $cp_related_author, $all_user_ids)) {
                    update_post_meta($post_id, 'cp_related_author', $cp_related_author);
                    update_user_meta($cp_related_author, 'show_posts', $show_posts);
                } else {
                    wp_die(__('Invalid User, go back and try again.','cpress'));
                }
            }
            $new_slug = sanitize_title($post->post_title);
            wp_update_post(
                array (
                    'ID'        => $post_id,
                    'post_name' => $new_slug
                )
            );
        }
        return $post_id;
    }

    public function get_author_by_api()
    {
        if (isset($_REQUEST) && isset($_REQUEST['process']) && $_REQUEST['process']==true
            && isset($_REQUEST['nextNonce']) && $_REQUEST['nextNonce']=='SearchAuthor') {
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
            if (count($response->response->numFound)) {
                $author_keyword = array_filter($response->facet_counts->facet_fields->author_filter);
                
                foreach ($author_keyword as $author) {
                    $author_name = end(explode("\n|||\n", $author) );
                    $result[] = array(
                            'label'=> html_entity_decode($author_name),
                            );
                }
                echo json_encode($result);
            } else {
                $result[] = array(
						'label'=> __("Oops our bad ! No match available.", 'cpress'),
						);
				echo json_encode($result);
            }
        }
        wp_die();
    }

    public function get_item_by_id($id)
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

    public function cp_register_styles()
    {
        wp_register_style('cpr-frontend', CPR_ROOT_URL . '/assets/css/cpr_frontend.css',
            array(), CPR_PLUGIN_VERSION);
    }
    
    public function cp_enqueue_styles()
    {
        $theme_name_array = explode(' ',strtolower(wp_get_theme()));
        $theme_name = $theme_name_array[0];
        if ($theme_name == "twenty") {
            if (wp_style_is('cpr-frontend', 'enqueued')) {
                return;
            } else {
                wp_enqueue_style('cpr-frontend');
            }
        }
    }
   
    public function cpr_body_classes($classes) {
        $options = collectionpress_settings();
        if (!empty($options) && isset($options['author_page']) && $options['author_page']) {
            $cpr_author_page = $options['author_page'];
        }
        if (!empty($options) && isset($options['item_page']) && $options['item_page']) {
            $cpr_item_page = $options['item_page'];
        }
               
        global $post;
		if (is_page('author-list') || is_page($cpr_author_page)) {
            $classes[] = " cp_author-list";
        }
        if (is_page('items') || is_page($cpr_item_page)) {
            $classes[] = " cp_item-list";
        }
        return $classes;
    }
}

return new CPR_AuthorReg();

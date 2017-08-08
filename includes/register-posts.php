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
        //~ add_filter('template_include', array($this, 'cpr_custom_single'));
        add_filter('enter_title_here', array($this, 'cpr_custom_title'));
        add_filter('page_template', array($this, 'cpr_author_template'));
        add_filter('frontpage_template', array($this, 'cpr_frontpage_template'));
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

    public function cpr_custom_single($template)
    {
        $post_types = array('cp_authors');
        
        if (is_singular($post_types)) {
            if (!file_exists(locate_template('single-cp_authors.php'))) {
                $template = CPR_TEMPLATE_PATH.'/single-cp_authors.php';
            }
        }

        return $template;
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
        if (is_singular('cp_authors')) {
            $post_content = $content;
            ob_start();
            ?>
            <div class="author-items-wrap">
                <?php
                $show_items = get_post_meta(get_the_ID(), "show_items", true);
                $author_keyword = get_post_meta(get_the_ID(), "author_keyword", true);
                if ( $show_items=="yes" ){
                    if ( $author_keyword=='' ){
                        $author_keyword = get_the_title();
                    }
                    echo do_shortcode('[collectionpress author="'.$author_keyword.'"]');
                }
                ?>
            </div>
            <?php
            $show_posts = get_post_meta(get_the_ID(), "show_posts", true);
            $cp_related_author = get_post_meta(get_the_ID(), "cp_related_author", true);
            if ($show_posts=="yes" && $cp_related_author!='') : ?>
                <div class="author-posts-wrap">
                    <h3><?php echo __("Author's Blog Posts", 'cpress') ?></h3>
                    <?php
                    $aposts=1;
                    if (isset($_GET) && isset($_GET['aposts'])) {
                        if ($_GET['aposts']!=''){
                            $aposts = $_GET['aposts'];
                        }
                    }
                    $author_posts = new WP_Query(array(
                            "author" 	 	 =>$cp_related_author,
                            "post_type"      =>"post",
                            "post_status"    =>"publish",
                            "orderby"        =>"modified",
                            "order"          =>"DESC",
                            "posts_per_page" =>get_option('posts_per_page'),
                            "cache_results"  => false,
                            "paged"          => $aposts));
                    $found_posts =$author_posts->found_posts;
                    $total_pages =$author_posts->max_num_pages;
                    if ($author_posts->have_posts()) :
                        while ($author_posts->have_posts()) :
                            $author_posts->the_post();

                            if (file_exists(locate_template('collectionpress/author_display_posts.php'))) {
                                include(locate_template('collectionpress/author_display_posts.php'));
                            } else {
                                include(CPR_TEMPLATE_PATH.'/collectionpress/author_display_posts.php');
                            }

                        endwhile; ?>
                        <div class="pagination">
                        <?php
                            $big = 999999999; // need an unlikely integer
                            echo paginate_links(array(
                                //~ 'base'      =>str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                'format'    =>'?aposts=%#%',
                                'prev_text' =>__('&laquo;'),
                                'next_text' =>__('&raquo;'),
                                'current'   =>max(1, $aposts),
                                'total'     =>$total_pages
                                ));
                            wp_reset_postdata();
                        ?>
                        </div>
                    <?php else: ?>
                        <p><?php echo __('No blog posts currently available.', 'cpress'); ?></p>
                    <?php endif; ?>                
                </div>
            <?php
            endif;
            $post_content .= ob_get_clean();
            return $post_content;
        }
    }
    
    public function cpr_author_template($page_template)
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
            if (get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_author_list.php") {
                $page_template = locate_template('template/collectionpress/cp_author_list.php');
            } else if (is_page('author-list') || is_page($cpr_author_page)) {
                if (locate_template('template/collectionpress/cp_author_list.php')) {
                    $page_template = locate_template('template/collectionpress/cp_author_list.php');
                }else {
                    $page_template = CPR_TEMPLATE_PATH.'/collectionpress/cp_author_list.php';
                }
            }
        }
        if (is_page('items') || is_page($cpr_item_page) || get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_item.php") {
            if (get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_item.php") {
                $page_template = locate_template('template/collectionpress/cp_item.php');
            } else if (is_page('items') || is_page($cpr_item_page)) {
                if (locate_template('template/collectionpress/cp_item.php')) {
                    $page_template = locate_template('template/collectionpress/cp_item.php');
                } else {
                    $page_template = CPR_TEMPLATE_PATH.'/collectionpress/cp_item.php';
                }
            }
        }
        return $page_template;
    }

    public function cpr_frontpage_template($page_template)
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
            if (get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_author_list.php") {
                $page_template = locate_template('template/collectionpress/cp_author_list.php');
            } else if (is_page('author-list') || is_page($cpr_author_page)) {
                if (locate_template('template/collectionpress/cp_author_list.php')) {
                    $page_template = locate_template('template/collectionpress/cp_author_list.php');
                }else {
                    $page_template = CPR_TEMPLATE_PATH.'/collectionpress/cp_author_list.php';
                }
            }
        }
        if (is_page('items') || is_page($cpr_item_page) || get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_item.php") {
            if (get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_item.php") {
                $page_template = locate_template('template/collectionpress/cp_item.php');
            } else if (is_page('items') || is_page($cpr_item_page)) {
                if (locate_template('template/collectionpress/cp_item.php')) {
                    $page_template = locate_template('template/collectionpress/cp_item.php');
                } else {
                    $page_template = CPR_TEMPLATE_PATH.'/collectionpress/cp_item.php';
                }
            }
        }
        return $page_template;
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
                        <option value="<?= $buser->ID ?>"
                            <?php if (isset($buser->ID)) :
                                if ($cp_related_author==$buser->ID) :
                                    echo "selected='selected' ";
                                endif;
                            endif; ?> >
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
		if (is_page('author-list') || is_page($cpr_author_page) || get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_author_list.php") {
            $classes[] = " cp_author-list";
        }
        if (is_page('items') || is_page($cpr_item_page) || get_post_meta($post->ID, "_wp_page_template", true)=="template/collectionpress/cp_item.php") {
            $classes[] = " cp_item-list";
        }
        return $classes;
    }
}

return new CPR_AuthorReg();

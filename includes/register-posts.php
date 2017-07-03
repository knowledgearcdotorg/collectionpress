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
        add_action( 'init', array( $this, 'register_post_author' ) );
        add_filter('template_include', array( $this,'cp_custom_single' ) );
        add_filter( 'enter_title_here', array( $this,'cp_custom_title' ) );
        add_filter( 'page_template', array( $this,'cp_author_template' ) );
        add_shortcode( 'AuthorList', array( $this,'cp_author_list'));
        add_action( 'add_meta_boxes', array( $this,'cp_author_meta_box' ) );
        
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
            $title = 'Enter Author Name here';
        }
        
        return $title;
    }

    public function cp_author_template($page_template)
    {
        if ( is_page( 'author-list' ) ) {
            $templatefilename = 'cp_author_list.php';
            $page_template = CP_TEMPLATE_PATH.'/'.$templatefilename;
        }
        return $page_template;
    
    }

    public function cp_author_list($atts)
    {
        ob_start();
        if(isset($atts['posts_per_page']))
            $posts_per_page = $atts['posts_per_page'];
        else
            $posts_per_page = get_option('posts_per_page');

        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
        $author_results = new WP_Query(array(
                        "post_type"      =>"cp_authors",
                        "post_status"    =>"publish",
                        "orderby"        =>"modified",
                        "order"          =>"DESC",
                        "posts_per_page" =>$posts_per_page,
                        "cache_results"  => false,
                        "paged"          => $paged,
                        )	);
        $found_posts =$author_results->found_posts;
        $total_pages =$author_results->max_num_pages;
        if($author_results->have_posts()):
            while ( $author_results->have_posts() ) : $author_results->the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <?php get_the_title(); ?>                   

                    <?php
                        $author_image='';
                        if(has_post_thumbnail()){
                            $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail');
                            $author_image = $image_url[0];
                        }
                    ?>
                    <?php if($author_image):?>
                    <div class="post-thumbnail">
                        <img src="<?php echo $author_image?>" />
                    </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div><!-- .entry-content -->

                </article><!-- #post-## -->
                
            <?php endwhile; ?>
            <div class="pagination">
                <?php               
                    $big = 999999999; // need an unlikely integer
                    echo paginate_links( array(
                        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                        'format' => '?paged=%#%',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'current' => max( 1, get_query_var('paged') ),
                        'total' => $total_pages
                    ) );
                ?>
            </div>
        <?php endif; ?>        
        <?php
        return ob_get_clean();
    }

    public function cp_author_meta_box(){
        add_meta_box( 'author-info', __('Author Info' ),  array( $this, 'cp_author_info_box'), "cp_authors", 'normal', 'high');	
    }
    
    public function cp_author_info_box(){
        global $pagenow;
        global $typenow;
        wp_enqueue_script("jquery-ui-autocomplete");
        //~ cp_authorspost-new.phpSs
        ?>
        <style>
        .ui-front{z-index:9999!important;}
        </style>
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
                        },
                    });			
                });
            });
        </script>
            
        <?php 
    }

    public function cp_get_author_by_api(){
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

            $response = wp_remote_get($collection_shortcode->get_url('discover.json?q=author:"'.$author.'"&rows=0&facet=true&facet.field=author_keyword'), $args);

            $response = json_decode(wp_remote_retrieve_body($response));
            if(count($response->facet_counts->facet_fields)){
                $author_keyword = array_filter($response->facet_counts->facet_fields->author_keyword);
                foreach($author_keyword as $author){
                    $result[] = array(
                            'label'=> html_entity_decode($author),
                            );
                }
                echo json_encode($result);
            }else{
                $result[] = array(
						'label'=> "Oops our bad ! No match available.",
						);
				echo json_encode($result);
            }
        }
        else  echo 1;
        wp_die();
    }
}

return new CP_Author();


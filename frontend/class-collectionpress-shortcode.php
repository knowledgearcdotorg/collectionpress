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

        if (isset($atts["limit"])) 
            $this->limit= $atts["limit"];
        else
            $this->limit = get_option('posts_per_page');

        if(isset($atts['list']) && $atts['list']=="authors"){
            $this->get_authors($this->limit);
        }
    }

    public function include_template_file($fileName, $response)
    {
       
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

    public function get_authors($limit){
        ob_start();
        $posts_per_page = $limit;

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

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>
                    <?php
					$thumb = '';

					$width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

					$height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
					$classtext = 'et_pb_post_main_image';
					$titletext = get_the_title();
					$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];

					et_divi_post_format_content();
                    ?>
                    <h2 class="entry-title"><a href="<?php echo  get_the_permalink(); ?>"><?php echo $titletext; ?></a></h2>

                    <?php if($thumb):?>
                        <div class="post-thumbnail">
                            <img src="<?php echo $thumb?>" class="<?php echo $classtext?>" title="<?php echo $titletext?>" />
                        </div>
                    <?php endif; ?>


                    <div class="entry-content">
                        <?php
                        
                            the_content();
                            
                        ?>                   
                    </div>
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
        //~ return ob_get_clean();
    }
    
    public function get_url($endpoint)
    {
        $options = collectionpress_settings();

        $url = $options['rest_url'];

        return $url."/".$endpoint;
    }
}

<?php
/**
 * Template Name: CP Author List
 * Template file of Collection Press To Author Display
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/cp_author_list.php"
 * */

get_header();
$posts_per_page = get_option("post_per_page");
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$author_results = new WP_Query(array(
				"post_type"      =>"cp_authors",
                "post_status"    =>"publish",
                "orderby"        =>"modified",
                "order"          =>"DESC",
                "posts_per_page" =>$posts_per_page,
                "cache_results"  => false,
                "paged"          => $paged));
$found_posts =$author_results->found_posts;
$total_pages =$author_results->max_num_pages;
?>

<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">
                <?php if($author_results->have_posts()): ?>
                    <?php while ( $author_results->have_posts() ) : $author_results->the_post(); ?>
                        <?php $post_format = et_pb_post_format(); ?>
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

                                if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) {
                                    if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) :
                                        printf(
                                            '<div class="et_main_video_container">
                                                %1$s
                                            </div>',
                                            $first_video
                                        );
                                    elseif ( ! in_array( $post_format, array( 'gallery' ) ) && 'on' === et_get_option( 'divi_thumbnails_index', 'on' ) && '' !== $thumb ) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height ); ?>
                                        </a>
                                <?php
                                    elseif ( 'gallery' === $post_format ) :
                                        et_pb_gallery_images();
                                    endif;
                                } ?>

                            <?php if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) : ?>
                                <?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) ) : ?>
                                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <?php endif; ?>

                                <?php
                                    et_divi_post_meta();

                                    if ( 'on' !== et_get_option( 'divi_blog_style', 'false' ) || ( is_search() && ( 'on' === get_post_meta( get_the_ID(), '_et_pb_use_builder', true ) ) ) ) {
                                        truncate_post( 270 );
                                    } else {
                                        the_content();
                                    }
                                ?>
                            <?php endif; ?>
                        </article> <!-- .et_pb_post -->
                
                    <?php endwhile; ?>
                    <div class="pagination">
                        <?php               
                            $big = 999999999; // need an unlikely integer
                            echo paginate_links( array(
                                'base'      =>str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                'format'    =>'?paged=%#%',
                                'prev_text' =>__('&laquo;'),
                                'next_text' =>__('&raquo;'),
                                'current'   =>max(1, get_query_var('paged')),
                                'total'     =>$total_pages
                            ) );
                        ?>
                    </div>
                <?php endif; ?>
            </div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>

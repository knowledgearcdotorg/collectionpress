<?php
/**
 * The template for displaying single CollectionPress authors
 * You can add this file to theme folder and paste this file  there.
 * Path will be "<theme_name>/single-cp_authors.php"
 * @author Avinash
 */
get_header();
?>

<div id="main-content">
    <div class="container">
        <div id="content-area" class="clearfix">
            <div class="content-wrap">
                <?php while (have_posts()) : ?>
                    <?php the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                        <div class="post_meta_wrapper">
                            <h1 class="entry-title"><?php the_title(); ?></h1>

                            <?php if (! post_password_required()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) :
                                        the_post_thumbnail();
                                    endif; ?>
                                </a>
                            <?php endif; ?>
                        </div> <!-- .et_post_meta_wrapper -->
                    
                        <div class="entry-content">
                        <?php
                       
                            the_content();

                            wp_link_pages(array('before' => '<div class="page-links">' . esc_html__('Pages:', 'cpress'), 'after' => '</div>'));
                        ?>
                        </div> <!-- .entry-content -->

                        <div class="author-items-wrap">
                            <h3><?php echo __('Author Items', 'cpress') ?></h3>

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
                                <h3><?php echo __('Author Blog Posts', 'cpress') ?></h3>
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
                                            include(CP_TEMPLATE_PATH.'/collectionpress/author_display_posts.php');
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
                                            'current'   =>max(1, get_query_var('aposts')),
                                            'total'     =>$total_pages
                                            ));
                                        wp_reset_postdata();
                                    ?>
                                    </div>
                                <?php endif; ?>                    
                            </div>
                        <?php endif; ?>
                        <div class="cp_post_meta_wrapper">
                        <?php
                            if ((comments_open() || get_comments_number())
                                && 'on' == et_get_option('divi_show_postcomments', 'on')) {
                                comments_template('', true);
                            }
                        ?>
                        </div> <!-- .et_post_meta_wrapper -->
                    </article> <!-- .et_pb_post -->

                <?php endwhile; ?>
            </div> <!-- #content -->

            <?php get_sidebar(); ?>
        </div> <!-- #content-area -->
    </div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>

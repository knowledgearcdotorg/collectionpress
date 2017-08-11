<?php
/**
 * The template for displaying single CollectionPress authors
 * You can add this file to theme folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/single_cp_authors-content.php"
 * @author Avinash
 */
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
            if ($_GET['aposts']!='') {
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

                if (file_exists(locate_template('template/collectionpress/author_display_posts.php'))) {
                    get_template_part('template/collectionpress/author_display_posts');
                } else {
                    include(CPR_TEMPLATE_PATH.'/template/collectionpress/author_display_posts.php');
                }

            endwhile; ?>
            <?php if ($total_pages>1) : ?>
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
            <?php endif; ?>
        <?php else: ?>
            <p><?php echo __('No blog posts currently available.', 'cpress'); ?></p>
        <?php endif; ?>
    </div>
<?php
endif;

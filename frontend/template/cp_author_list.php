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
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$author_results = new WP_Query(array(
                    "post_type"=>"cp_authors",
                    "post_status"=>"publish",
                    "orderby"=>"modified",
                    "order"=>"DESC",
                    'posts_per_page' =>$posts_per_page,
                    'cache_results'  => false,
                    'paged' => $paged));
$found_posts =$author_results->found_posts;
$total_pages =$author_results->max_num_pages;
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <header class="entry-header">
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
        </header><!-- .entry-header -->
        <?php if ($author_results->have_posts()) : ?>
            <?php while ($author_results->have_posts()) : $author_results->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <?php the_title('<h4 class="entry-title">', '</h4>'); ?>

                    <?php
                        $author_image='';
                        if (has_post_thumbnail()) {
                            $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail');
                            $author_image = $image_url[0];
                        }
                    ?>
                    <?php if ($author_image) :?>
                    <div class="post-thumbnail">
                        <img src="<?php echo $author_image?>" />
                    </div>
                    <?php endif; ?>

                    <div class="entry-content">
                    <?php the_content(); ?>
                    </div><!-- .entry-content -->

                </article><!-- #post-## -->

            <?php endwhile; ?>
        <?php endif; ?>
        <div class="pagination">
            <?php
                $big = 999999999; // need an unlikely integer
                echo paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'current' => max(1, get_query_var('paged')),
                    'total' => $total_pages));
            ?>
        </div>
    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>


<?php
/**
 * Template file of Collection Press To Author Display
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/cp_author_list.php"
 * */
$posts_per_page = get_option("posts_per_page");
$clist =1;
if (isset($_GET) && isset($_GET['clist'])) {
	if ($_GET['clist']!=''){
		$clist = $_GET['clist'];
	}
}
$author_results = new WP_Query(array(
                "post_type"      =>"cp_authors",
                "post_status"    =>"publish",
                "orderby"        =>"modified",
                "order"          =>"DESC",
                "posts_per_page" =>$posts_per_page,
                "cache_results"  => false,
                "paged"          => $clist));
$found_posts =$author_results->found_posts;
$total_pages =$author_results->max_num_pages;
?>

<?php if ($author_results->have_posts() ): ?>
    <ul class="author-names-list">
        <?php while ($author_results->have_posts()) : ?>
            <?php $author_results->the_post(); ?>
<!--
       <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <a href="<?php the_permalink(); ?>">
                <?php if (has_post_thumbnail()) :
                    the_post_thumbnail();
                endif; ?>
            </a>
-->
        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php //the_content(); ?>
<!--
        </article>
-->

        <?php endwhile; ?>
    </ul>
    <?php if ($total_pages>1) : ?>
        <div class="pagination">
            <?php
            $big = 999999999; // need an unlikely integer
            echo paginate_links(array(
            //~ 'base'      =>str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format'    =>'?clist=%#%',
            'prev_text' =>__('&laquo;'),
            'next_text' =>__('&raquo;'),
            'current'   =>max(1, $clist),
            'total'     =>$total_pages
            ));
            ?>
        </div>
    <?php endif; ?>
<?php else : ?>
    <p><?php echo __('No results found.', 'cpress'); ?></p>
<?php endif; ?>            

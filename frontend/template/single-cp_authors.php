<?php
/**
 * The template for displaying single CollectionPress authors
 * You can add this file to theme folder and paste this file  there.
 * Path will be "<theme_name>/single-cp_authors.php"
 * @author Avinash
 */
get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header><!-- .entry-header -->

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

                <footer class="entry-footer">
                    <?php
                    edit_post_link(
                        sprintf(
                        /* translators: %s: Name of current post */
                            __( 'Edit<span class="screen-reader-text"> "%s"</span>' ),
                            get_the_title()
                        ),
                        '<span class="edit-link">',
                        '</span>'
                    );
                    ?>
                </footer><!-- .entry-footer -->
            </article><!-- #post-## -->
            <?php
            if ( is_singular( 'cp_authors' ) ) {
                // Previous/next post navigation.
                the_post_navigation( array(
                    'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next') . '</span> ' .
                    '<span class="screen-reader-text">' . __( 'Next post:') . '</span> ' .
                    '<span class="post-title">%title</span>',
                    'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous') . '</span> ' .
                    '<span class="screen-reader-text">' . __( 'Previous post:') . '</span> ' .
                    '<span class="post-title">%title</span>',
                ) );
            }

        // End of the loop.
        endwhile;
    ?>

    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

<?php
/**
 * Template file of Collection Press To Author Display
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/author_display.php"
 * */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <a href="<?php the_permalink(); ?>">
        <?php if (has_post_thumbnail()) {
            the_post_thumbnail();
} ?>
    </a>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php the_content(); ?>
</article> <!-- .et_pb_post -->

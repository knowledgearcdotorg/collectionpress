<?php
/**
 * Template file of Collection Press To Author Display
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/cp_author_list.php"
 * */
global $wpdb;
$limit = $posts_per_page = get_option("posts_per_page");
$clist =1;
$slist =1;
$offset = 0;
if (isset($_GET) && isset($_GET['offset'])) :
    if ($_GET['offset']!='') :
        $offset = $_GET['offset'];
        $limit = $offset+$posts_per_page;
    endif;
endif;
$found_posts = $wpdb->get_var("
        SELECT      COUNT(ID)
        FROM        $wpdb->posts
        WHERE 		$wpdb->posts.post_type = 'cp_authors'"
        );
$total_pages = ceil($found_posts/$posts_per_page);

if (isset($_GET) && isset($_GET['starts_with']) && ($_GET['starts_with']!='')) :
    $starts_with = $_GET['starts_with'];
    if ($starts_with=="0_9") :
        $starts_with = 0;
    endif;
    //query
    $postids = $wpdb->get_col($wpdb->prepare("
	SELECT      ID
	FROM        $wpdb->posts
	WHERE       $wpdb->posts.post_title >= '%s'
	AND 		$wpdb->posts.post_type = 'cp_authors'
	ORDER BY    $wpdb->posts.post_title ASC", $starts_with));
    if (count($postids)) :
        $offset = $found_posts - count($postids) ;
    endif;
    $limit = $offset+$posts_per_page;
endif;

$prev_link='';
$next_link='';
$poffset = ($offset - $posts_per_page);

if ($limit >= $found_posts) :
    $limit = $found_posts;
endif;
if ($offset <= 0 || $poffset <= 0) :
    $poffset = 0;
    $limit = $posts_per_page;
endif;


if ($offset!=0 && $limit < $found_posts) :
    $prev_link= add_query_arg("offset",$poffset,get_permalink());
    $next_link= add_query_arg("offset",$limit,get_permalink());
elseif ($offset == 0 && $limit < $found_posts) :
    $next_link= add_query_arg("offset",$limit,get_permalink());
elseif ($limit >= $found_posts) :
    $prev_link= add_query_arg("offset",$poffset,get_permalink());
endif;


$startCapital = 65;
$sort_html = "<div class=''>";
    $sort_html .= "<ul class='aplhasort-wrap list-inline'>";
        $sort_html .= "<li class ='alphabet-lists'><a href='". add_query_arg("sort", "0_9" ,get_permalink()) ."'>0-9</a></li>";
            for ($i = 0; $i<26; $i++) :
                $sort_html .= "<li class ='alphabet-lists'><a href='". add_query_arg("starts_with", chr($startCapital + $i) ,get_permalink()) ."'>" .  chr($startCapital + $i) . "</a></li>";
            endfor;
        $sort_html .= '</ul>';
    $sort_html .='<p class="pagination-info">Now showing items '.$offset.'-'.$limit.' of '.$found_posts.'</p>';
$sort_html .= '</div>';


$sort_html_list = "<div class='col-x-8'>
    <select class='aphabet-list visible-xs'>";
        $sort_html_list .= "<option value='". add_query_arg("sort", "0_9" ,get_permalink()) ."'>0-9</option>";
        for ($i = 0; $i<26; $i++) :
            $sort_html_list .= "<option  value='". add_query_arg("starts_with", chr($startCapital + $i) ,get_permalink()) ."'>" .  chr($startCapital + $i) . "</option>";
        endfor;
    $sort_html_list .= "</select>";
    $sort_html_list .='<p style="padding: 10px 0 ;">Now showing items '.$offset.'-'.$limit.' of '.$found_posts.'</p>';
$sort_html .= '</div>';

if (isset($postids)) :
    $author_results = new WP_Query(array(
                "post__in"       =>$postids,
                "post_type"      =>"cp_authors",
                "post_status"    =>"publish",
                "orderby"        =>"title",
                "order"          =>"ASC",
                "posts_per_page" =>$posts_per_page,
                "cache_results"  => false,
                ));
else :
    $author_results = new WP_Query(array(
                "post_type"      =>"cp_authors",
                "post_status"    =>"publish",
                "orderby"        =>"title",
                "order"          =>"ASC",
                "posts_per_page" =>$posts_per_page,
                "cache_results"  => false,
                "offset"          => $offset,
                "limit"          => $limit,
                ));
endif;

?>

<?php if ($author_results->have_posts() ): ?>
    <?php echo $sort_html; ?>
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
            <?php if ($prev_link) : ?>
                <span class="previous" >
                    <a class="previous-page-link  page-numbers" href="<?php echo $prev_link ?>">&laquo;</a>
                </span>
            <?php endif; ?>
            <?php if ($next_link) : ?>
                <span class="next" >
                    <a class="next-page-link  page-numbers" href="<?php echo $next_link ?>">&raquo;</a>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php else : ?>
    <p><?php echo __('No results found.', 'cpress'); ?></p>
<?php endif; ?>            

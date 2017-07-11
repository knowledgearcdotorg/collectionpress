<?php
/**
 * Template file of Collection Press To Author Display
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/item_display.php"
 * */
?>
<ul>
    <?php foreach ($response->response->docs as $doc) : ?>
        <?php $parts = array();?>
        <li>
            <?php if (is_array($title = $doc->title)) : ?>
                <?php $title = array_shift($title); ?>

                <?php  if ($handle = $doc->handle) : ?>
                    <?php $url = add_query_arg("item_id",$doc->{"search.resourceid"},get_permalink($page_id)); ?>
                    <?php $title = sprintf('<a href="%s" target="_blank">%s</a>', $url, $title); ?>
                <?php endif; ?>
                <?php $parts[] = $title; ?>
            <?php endif; ?>

            <?php if (is_array($publisher = &$doc->{"dc.publisher"})) : ?>
                <?php $parts[] = array_shift($publisher); ?>
            <?php endif; ?>

            <?php if (is_array($dateIssued = $doc->dateIssued)) : ?>
                <?php $parts[] = array_shift($dateIssued); ?>
            <?php endif; ?>

            <?php echo implode(", ", $parts); ?>            
        </li>
    <?php endforeach; ?>
</ul>
<?php if ($limit) : ?>
    <?php
    $total_count    = $response->response->numFound;
    $start_page     = $response->response->start;
    $limit          = $response->responseHeader->params->rows;
    $total_results  = count($response->response->docs);
    $total_pages    = ceil($total_count/$limit);
    ?>
    <div class="pagination">
        <?php
        $big = 999999999; // need an unlikely integer
        echo paginate_links(array(
            'base'      =>str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format'    =>'?paged=%#%',
            'prev_text' =>__('&laquo;'),
            'next_text' =>__('&raquo;'),
            'current'   =>max(1, get_query_var('paged')),
            'total'     =>$total_pages
            ));
        ?>
    </div>
<?php endif; ?>

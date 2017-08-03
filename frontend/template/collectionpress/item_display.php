<?php
/**
 * Template file of Collection Press To Author Display
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/item_display.php"
 * */
?>
<h3><?php echo __("Author's Works", 'cpress') ?></h3>
<?php if (isset($response->response->docs) && !empty($response->response->docs)) : ?>
    <ul>
        <?php foreach ($response->response->docs as $doc) : ?>
            <?php $parts = array();?>
            <li>
                <?php if (is_array($title = $doc->title)) : ?>
                    <?php $title = array_shift($title); ?>

                    <?php  if ($handle = $doc->handle) : ?>
                        <?php
                        if(isset($options['display_item']) && $options['display_item']=='within_wp' && isset($options['item_page'])){
                            $url = add_query_arg("item_id",$doc->{"search.resourceid"},get_permalink($options['item_page']));
                        } else {
                            $url = $options['handle_url']."/".$handle;
                        }
                        ?>
                        <?php $title = sprintf('<a href="%s" target="_blank">%s</a>', esc_url($url), $title); ?>
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
                //~ 'base'      =>str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format'    =>'?citem=%#%',
                'prev_text' =>__('&laquo;'),
                'next_text' =>__('&raquo;'),
                'current'   =>max(1, $paged),
                'total'     =>$total_pages
                ));
            ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <p><?php echo __('No works currently archived.', 'cpress'); ?></p>
<?php endif; ?>

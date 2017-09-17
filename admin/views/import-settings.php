<?php
/**
 * Import Authors by CSV
 */
?>
<style>
    .import-select{
        border: 2px solid #aedc3f;
        padding: 20px;
        margin-bottom:20px;
    }
    .import-area{
        border: 2px solid #eab700;
        padding: 20px;
    }
    .import-area label, .import-select label {
        font-weight:bold;
    }
</style>
<div class="wrap">

    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    

    <?php settings_errors('collectionpress-notices'); ?>

    <div>
        <div id="post-body" class="metabox-holder columns-2">

            <div class="post-body-content collectionpress-main">
                <table class="import-select">
                    <tr>
                        <td>
                            <label><?php echo __('Import Author by', 'cpress') ?>:</label>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>
                                        <label>
                                            <input type='radio' name='import_method' id="import_by_csv"
                                                value='import_by_csv'> <?php echo __('By Uploading CSV', 'cpress'); ?>
                                        </label>       
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>
                                            <input type='radio' name='import_method' id="import_by_solr"
                                                value='import_by_solr'> <?php echo __('By SOLR', 'cpress'); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div id="csv_wrap" style="display:none;">
                    <form method="post" action="<?php echo admin_url('admin.php?page=collectionpress-csv-import')?>" enctype="multipart/form-data">
                    <?php  wp_nonce_field('import_author_nonce', 'import_author_nonce'); ?>
                        <table class="import-area">
                            <tr>
                                <td>
                                    <label for="import_file"><?php echo __('Upload CSV', 'cpress') ?>:</label>
                                </td>
                                <td>
                                    <input name="import_file" type="file" id="import_file" class="input-text regular-text" required/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="skip_lines"><?php echo __('Skip Number of Lines', 'cpress') ?>:</label>
                                </td>
                                <td>
                                    <input type="number" min='0' name="skip_lines" value="0" required/>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>
                    </form>            
                </div>

                <div id="solr_wrap" style="display:none;">
                    <?php
                    $options = collectionpress_settings();
                    $url = $options['rest_url'];
                    ?>
                    <form method="post" action="<?php echo admin_url('admin.php?page=collectionpress-solr-import')?>" enctype="multipart/form-data">
                    <?php  wp_nonce_field('import_author_nonce', 'import_author_nonce'); ?>
                        <table class="import-area">
                            <tr>
                                <td>
                                    <label for="rest_url"><?php echo __('Rest URL', 'cpress') ?>:</label>
                                </td>
                                <td>
                                    <input name="rest_url" type="text" id="rest_url" class="input-text regular-text"
                                    value="<?php echo  $url ?>" required/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="skip_lines"><?php echo __('Skip Number of Lines', 'cpress') ?>:</label>
                                </td>
                                <td>
                                    <input type="number" min='0' name="skip_lines" value="0" required/>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>
                    </form>            
                </div>
                
            </div>

        </div>
    </div>

</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("input[name='import_method']").click(function(){
            if (jQuery('#import_by_csv').is(':checked')) {
                jQuery('#solr_wrap').css('display','none');
                jQuery('#csv_wrap').slideDown('500');
            }
            if (jQuery('#import_by_solr').is(':checked')) {
                jQuery('#csv_wrap').css('display','none');
                jQuery('#solr_wrap').slideDown('500');
            }
        });
    });
</script>

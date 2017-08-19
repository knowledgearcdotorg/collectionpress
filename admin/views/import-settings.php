<?php
/**
 * Import Authors by CSV
 */
?>
<style>
    .import-area{
        border: 2px solid #eab700;
        padding: 20px;
    }
</style>
<div class="wrap">

    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <h4><?php echo __("Upload CSV file and select the checkbox to add Authors", "cpress"); ?></h4>

    <?php settings_errors('collectionpress-notices'); ?>

    <div>
        <div id="post-body" class="metabox-holder columns-2">

            <div class="post-body-content collectionpress-main">
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
                        <tr>
                            <td>
                                <label for="names_per_page"><?php echo __('Names per Page', 'cpress') ?>:</label>
                           </td>
                            <td>
                                <input type="number" min='1' name="names_per_page" value="20" required/>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>

        </div>
    </div>

</div>


<div class="xpush">
    <?php if (!empty($notice)) : ?>
        <div id="message" class="notice <?php echo $notice['class']; ?> is-dismissible">
            <p><?php echo $notice['message']; ?></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
        </div>
    <?php endif; ?>
    <div class="xpush__header flex">
        <span class="logo flex">3xpush.com</span>
        <span class="title"><?php _e( 'Settings', '3xpush' ); ?></span>
    </div>
    <div class="xpush__text">
        <h3><b><?php _e( 'Settings-text-1', '3xpush' ); ?>: </b></h3>
        <p><?php _e( 'Settings-text-2', '3xpush' ); ?> <a href="https://3xpush.com" target="_blank">3xpush.com</a></p>
        <p><?php _e( 'Settings-text-3', '3xpush' ); ?></p>
        <p><?php _e( 'Settings-text-4', '3xpush' ); ?></p>
        <p><?php _e( 'Settings-text-5', '3xpush' ); ?></p>
        <p><?php _e( 'Settings-text-6', '3xpush' ); ?></p>
        <p><?php _e( 'Settings-text-7', '3xpush' ); ?></p>
    </div>
    <form class="xpush__form flex column" action="" method="post">
        <div class="form-block">
            <label>API KEY:</label>
            <div class="flex">
                <input type="text"<?php if ($fclass) : ?> readonly <?php endif; ?>name="api-key" class="required input input-lg" value="<?php echo $api_key; ?>" placeholder="">
                <button class="btn" id="check-key-btn" <?php if ($fclass) : ?>style="display: none;"<?php endif; ?>><?php echo _e('Check', '3xpush'); ?></button>
                <button class="btn" id="remove-key-btn" <?php if (!$fclass) : ?>style="display: none;"<?php endif; ?>><?php echo _e('Delete', '3xpush'); ?></button>
            </div>
        </div>
        <input type="hidden" name="form-action" value="" />
        <div class="hidden-block" <?php if (!$fclass) : ?>style="display: none;"<?php endif; ?>>
            <div class="form-block">
                <label>Site ID: (<? echo __('point without', '3xpush'); ?> #)</label>
                <input type="text" name="psx_site_id" class="input input-md required" value="<?php echo $api_settings['psx_site_id']; ?>" placeholder="">
            </div>
            <div class="form-block">
                <label><?php _e( 'Pause before show', '3xpush' ); ?>: <span class="tooltip" data-tooltip="<?php echo _e('Tooltip-1', '3xpush'); ?>">?</span></label>
                <input type="text" name="psx_time" class="input input-sm required" value="<?php echo $api_settings['psx_time']; ?>" placeholder=""> sek
            </div>
            <div class="form-block checkbox">
                <input type="checkbox" <?php if (!empty($api_settings['blocksite']) && $api_settings['blocksite'] == 'check' || $xpush_tuned != 1) : ?>checked<?php endif; ?> name="blocksite" value="check" />
                <label><?php _e( 'Close the site with a block', '3xpush' ); ?>: <span class="tooltip" data-tooltip="<?php echo _e('Tooltip-2', '3xpush'); ?>">?</span></label>
            </div>
            <div class="form-block checkbox">
                <input type="checkbox" <?php if (!empty($api_settings['hasBlockCross']) && $api_settings['hasBlockCross'] == 'check' || $xpush_tuned != 1) : ?>checked<?php endif; ?> name="hasBlockCross" value="check" />
                <label><?php _e( 'Draw a cross', '3xpush' ); ?>: <span class="tooltip" data-tooltip="<?php echo _e('Tooltip-3', '3xpush'); ?>">?</span></label>
            </div>
            <div class="form-block">
                <label><?php _e( 'Text in the block', '3xpush' ); ?>: <span class="tooltip" data-tooltip="<?php echo _e('Tooltip-4', '3xpush'); ?>">?</span></label>
                <input type="text" name="blockText" class="input input-lg" value="<?php echo $api_settings['blockText']; ?>" placeholder="">
            </div>
            <div class="form-block">
                <label><?php _e( 'Pass as Subscriber', '3xpush' ); ?>: <span class="tooltip" data-tooltip="<?php echo _e('Tooltip-5', '3xpush'); ?>">?</span></label>
                <select class="select" name="psx_tag">
                    <?php foreach ($tag_options as $key => $val) : ?>
                        <option value="<?php echo $key; ?>"<?php if ($api_settings['psx_tag'] == $key) : ?> selected <?php endif; ?>><?php echo $val; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-block image-block">
                <label><?php _e( 'Default icon', '3xpush' ); ?>: <span class="tooltip" data-tooltip="<?php echo _e('Tooltip-6', '3xpush'); ?>">?</span></label>
                <div class="flex">
                    <div>
                        <a class="upload" id="xpush_media_manager">
                            <?php echo _e('Upload image', '3xpush'); ?>
                        </a>
                        <label><?php _e( 'Square size', '3xpush' ); ?></label>
                    </div>
                    <div class="upload-image" style="background-image: url(<?php echo $icon; ?>);"></div>
                </div>
            </div>
            <input type="hidden" name="psx_site_icon" value="<? echo $icon; ?>" />
            <input type="submit" class="btn submit" value="<?php _e( 'Save', '3xpush' ); ?>" />
        </div>
    </form>
</div>
<a href="/?TB_inline&width=350&height=100&inlineId=modal-content" id="modal-trigger" class="thickbox" style="display:none;"></a>
<div id="modal-content" style="display:none;"></div>

<?php if (count($log) > 0) : ?>
    <div class="xpush__log">
        <?php foreach ($log as $item) : ?>
            <div class="item"><?php echo $item->p_content; ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    var xpushUploadMsg = {
        'valid_file' : '<?php _e('Valid download file', '3xpush'); ?>',
        'image_size' : '<?php _e('Image size should be', '3xpush'); ?>',
        'error_load' : '<?php _e('Error loading image', '3xpush'); ?>'
    };
</script>
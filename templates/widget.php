<div class="xpush__widget">
    <div class="xpush__form flex column">
        <div class="form-block checkbox flex">
            <input type="checkbox" checked name="xpush_send" value="1" />
            <label><?php _e( 'Send notification', '3xpush' ); ?></label>
        </div>
        <div class="hidden-block">
            <div class="form-block">
                <label><?php _e( 'Dispatch time', '3xpush' ); ?></label>
                <input type="text" class="datepicker" name="xpush-date" value="<?php echo current_time('Y-m-d H:i', 0); ?>" placeholder="yyyy-mm-dd hh:ii"/>
            </div>
            <div class="form-block">
                <label><?php _e( 'Sites', '3xpush' ); ?></label>
                <select name="xpush-sites-list" multiple="multiple" class="b-multiselect">
                    <?php $index = 0; foreach ($xpush_options['xpush_sites'] as $key => $val) : ?>
                        <option data-option="xpush_sites" value="<?php echo $key; ?>"<?php if ($key == $api_settings['psx_site_id']) : ?> selected <?php endif; ?>><?php echo $val; ?></option>
                        <?php $index++; endforeach; ?>
                </select>
            </div>
            <div class="form-block">
                <label><?php _e( 'Regions', '3xpush' ); ?></label>
                <select name="xpush_regions-list" multiple="multiple" class="b-multiselect">
                    <?php foreach ($xpush_options['xpush_regions'] as $item) : ?>
                        <option data-option="xpush_regions" value="<?php echo $item['iso']; ?>"><?php 
                        if (get_bloginfo('language')== 'ru-RU')
                         {
                        echo $item['ru'];
                        } else {
                        echo $item['en'];    
                        }
                        
                         ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-block">
                <label><?php _e( 'Langs', '3xpush' ); ?></label>
                <select name="xpush_langs-list" multiple="multiple" class="b-multiselect">
                    <?php foreach ($xpush_options['xpush_langs'] as $item) : ?>
                        <option data-option="xpush_langs" value="<?php echo $item['iso']; ?>"><?php 
                         if (get_bloginfo('language')== 'ru-RU')
                         {
                        echo $item['ru'];
                        } else {
                        echo $item['en'];    
                        } 
                        ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-block">
                <label><?php _e( 'Tags', '3xpush' ); ?></label>
                <select name="xpush_tags-list" multiple="multiple" class="b-multiselect">
                    <?php foreach ($xpush_options['xpush_tags'] as $item) : ?>
                        <option data-option="xpush_tags" value="<?php echo mb_strtolower($item); ?>"><?php echo $item; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="xpush_sites" value="<?php if (!empty($api_settings['psx_site_id'])) echo $api_settings['psx_site_id']; ?>" />
            <input type="hidden" name="xpush_regions" value="<?php if (!empty($xpush_options['xpush_regions'][0]['iso']))  echo mb_strtolower($xpush_options['xpush_regions'][0]['iso']); ?>" />
            <input type="hidden" name="xpush_langs" value="<?php if (!empty($xpush_options['xpush_langs'][0]['iso'])) echo mb_strtolower($xpush_options['xpush_langs'][0]['iso']); ?>" />
            <input type="hidden" name="xpush_tags" value="" />
        </div>
    </div>
</div>
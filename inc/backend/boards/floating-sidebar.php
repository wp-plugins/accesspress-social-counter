<div class="apsc-boards-tabs" id="apsc-board-float-sidebar-settings" style="display:none">
    <h3><?php _e('Floating Sidebar Settings', 'aps-counter'); ?></h3>
    <div class="apsc-option-inner-wrapper">
        <label><?php _e('Enable Floating Sidebar', 'aps-counter'); ?></label>
        <div class="apsc-option-field">
            <label>
                <input type="checkbox" name="floating_sidebar[active]" value="1"/><div class="apsc-option-side-note"><?php _e('Check if you want to show floating sidebar in the frontend', 'aps-counter'); ?></div>
            </label>
        </div>
    </div>
    <div class="apsc-option-innner-wrapper">
        <label><?php _e('Show sidebar in', 'aps-counter'); ?> :</label>
        <div class="apsc-option-field">
            <select name="floating_sidebar[show]">
                <option value="all"><?php _e('All pages', 'aps-counter'); ?></option>
                <option value="only_homepage"><?php _e('Only on homepage', 'aps-counter'); ?></option>
                <option value="except_homepage"><?php _e('Except homepage', 'aps-counter'); ?></option>
            </select>
        </div>
    </div>
    <div class="apsc-option-inner-wrapper">
        <label><?php _e('Choose Theme', 'aps-counter'); ?></label>
        <div class="apsc-option-field">
            <label><input type="radio" name="floating_sidebar[theme]" value="theme-1"/><?php _e('Theme 1','aps-counter');?></label>
            <label><input type="radio" name="floating_sidebar[theme]" value="theme-2"/><?php _e('Theme 2','aps-counter');?></label>
            <label><input type="radio" name="floating_sidebar[theme]" value="theme-3"/><?php _e('Theme 3','aps-counter');?></label>
            <label><input type="radio" name="floating_sidebar[theme]" value="theme-4"/><?php _e('Theme 4','aps-counter');?></label>
            <label><input type="radio" name="floating_sidebar[theme]" value="theme-5"/><?php _e('Theme 5','aps-counter');?></label>
        </div>
    </div>
    <div class="apsc-option-inner-wrapper">
        <label><?php _e('Show Tooltip','aps-counter');?></label>
        <div class="apsc-option-field">
            <label><input type="checkbox" name="floating_sidebar[tooltip]" value="1"/><div class="apsc-option-side-note"><?php _e('Check if you want to show tooltip','aps-counter');?></div></label>
        </div>
    </div>
    <div class="apsc-option-inner-wrapper">
        <label><?php _e('Tooltip Background Color','aps-counter');?></label>
        <div class="apsc-option-field">
            <label><input type="text" name="floating_sidebar[bg_color]" class="apsc-colorpicker"/></label>
        </div>
    </div>
    <div class="apsc-option-inner-wrapper">
        <label><?php _e('Tooltip Text Color','aps-counter');?></label>
        <div class="apsc-option-field">
            <input type="text" name="floating_sidebar[text_color]" class="apsc-colorpicker"/>
        </div>
    </div>
</div>
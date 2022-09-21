<?php 
	global $pcb_private_custom_tab_params,$pcb_private_page_params;
	extract($pcb_private_page_params);
	extract($pcb_private_custom_tab_params);

?>

<div style="display:none;" class='upmp-private-page-tab-content upmp-private-page-<?php echo $tab_id;?>-tab-content'>
	<div class="upmp-setting-panel">
        <form method="post" id="" action="<?php echo admin_url( 'admin.php?page=upmp-private-user-page&&tab=pcb_section_private_page_user&pcb_private_page_user='.$current_user_id ); ?>" >
            
        <?php 
            wp_nonce_field( 'pcb_private_page_nonce', 'pcb_private_page_nonce_field' );
            if($_REQUEST && isset($_REQUEST['pcb_private_page_user'])){ 
        ?> 
            <div class="upmp-row" >
                <div class="upmp-label"><?php echo __('Name','upmp'); ?></div>
                <div class="upmp-field"><?php echo $display_name; ?></div>
                <input type="hidden" name="pcb_user_id" value="<?php echo $current_user_id; ?>" />
                <div class="upmp-clear"></div>
            </div>
            <div class="upmp-row" >
                <div class="upmp-label"><?php echo __('Private content','upmp'); ?></div>
                <div class="upmp-field"><?php echo $main_content; ?></div>
                <div class="upmp-clear"></div>
            </div>
            <div class="upmp-row">
                <div class="upmp-label">&nbsp;</div>
                <div class="upmp-field">
                    <input type="hidden" name="pcb_tab_id" value="<?php echo $tab_id; ?>" />
                    <input type="submit" name="pcb_private_page_content_submit" id="pcb_private_page_content_submit" value="<?php _e('Save','upmp'); ?>" class="upmp-button-primary" />
                </div>
                <div class="upmp-clear"></div>
            </div>
            <div class="upmp-clear"></div>
        <?php } ?>
        </form>
    </div>
	
</div>
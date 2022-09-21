<?php 
    global $pcb_private_page_settings_data; 
    extract($pcb_private_page_settings_data);

    $user_query = new WP_User_Query( array( 'role' => 'administrator' ) );
    $user_results = $user_query->get_results();

?>

<form method="post" action="">
<table class="form-table upmp-settings-list">

                <tr>
                    <th><label for=""><?php echo __('Private Page','upmp'); ?></label></th>
                    <td style="width:500px;">
                        <select name="pcb_private_page_general[private_page_id]" id="pcb_private_page_id" class="upmp-select2-setting" placeholder="<?php _e('Select','upmp'); ?>" >
                            
                            <?php 
                                if($private_page_id != '0'){ ?>
                                    <option selected value="<?php echo $private_page_id; ?>"><?php echo get_the_title($private_page_id); ?></option>
                            <?php }  ?>
                        </select>
                        <div class='upmp-settings-help'><?php _e('This setting is used to define the private page with [pcb_private_page_pro] shortcode.','upmp'); ?></div>
                    </td>
                    
                </tr>

                <tr>
                    <th><label for=""><?php echo __('Enable Admin Email Notifications for','upmp'); ?></label></th>
                    <td style="width:500px;">
                        <?php foreach ($user_results as $user) { 
                                $checked_admin_notify_emails_status = '';
                                if(in_array($user->user_email, $admin_notify_emails)){
                                    $checked_admin_notify_emails_status = ' checked ';
                                }
                        ?>
                            <input type="checkbox" name="pcb_private_page_general[admin_notify_emails][]" <?php echo $checked_admin_notify_emails_status; ?> value="<?php echo $user->user_email; ?>" /><?php echo $user->user_email; ?><br/>
                        
                        <?php } ?>
                        <div class='upmp-settings-help'><?php _e('This setting is used to specify the administrator emails who receives notifications on user activities.','upmp'); ?></div>
                    </td>
                    
                </tr>
                        
                
    <input type="hidden" name="pcb_private_page_general[private_mod]"  value="1" />                        
    <input type="hidden" name="pcb_tab" value="<?php echo $tab; ?>" />    
</table>

    <?php submit_button(); ?>
</form>
<?php
	global $upmp,$private_page_tab_params;
	extract($private_page_tab_params);

	$user_roles = $upmp->roles_capability->pcb_user_roles();

    $visibility = get_post_meta( $post->ID, '_pcb_private_tab_visibility', true );
    $visible_roles = get_post_meta( $post->ID, '_pcb_private_tab_visibility_roles', true );
    if(!is_array($visible_roles)){
    	$visible_roles = array();
    }
    $tab_status = get_post_meta( $post->ID, '_pcb_private_tab_status', true );
    $tab_type = get_post_meta( $post->ID, '_pcb_private_tab_type', true );
    $tab_predefined_type = get_post_meta( $post->ID, '_pcb_private_tab_predefined_type', true );
    // $upload_permission_type = get_post_meta( $post->ID, '_pcb_private_tab_upload_permission_type', true );

    // $visible_upload_roles = get_post_meta( $post->ID, '_pcb_private_tab_upload_permission_roles', true );
    // if(!is_array($visible_upload_roles)){
    // 	$visible_upload_roles = array();
    // }
    

    $show_role_field = '';
    if( $visibility == 'role'){
    	$show_role_field = " style='display:block;' ";
    }

    $show_predefined_type_field = '';
    if( $tab_type == 'predefined'){
    	$show_predefined_type_field = " style='display:block;' ";
    }

    // $show_upload_type_field = '';
    // if( $tab_type == 'files'){
    // 	$show_upload_type_field = " style='display:block;' ";
    // }

    // $show_upload_role_field = '';
    // if( $upload_permission_type == 'role'){
    // 	$show_upload_role_field = " style='display:block;' ";
    // }

    

?>


<div class="pcb_post_meta_row">
	<div class="pcb_post_meta_row_label"><strong><?php _e('Visibility','upmp'); ?></strong></div>
	<div class="pcb_post_meta_row_field">
		<select id="pcb_private_tab_visibility" name="pcb_private_tab_visibility" class="upmp-select2-setting">
			<option value='none' <?php selected('none',$visibility); ?> ><?php _e('Please Select','upmp'); ?></option>
			<option value='member' <?php selected('member',$visibility); ?> ><?php _e('Members','upmp'); ?></option>
			<option value='role' <?php selected('role',$visibility); ?> ><?php _e('Selected User Roles','upmp'); ?></option>
			
			<?php
			
			  	$private_tab_visibility_conditions_params = array('visibility' => $visibility);
		
				$display = apply_filters('pcb_private_tab_visibility_conditions','', $private_tab_visibility_conditions_params );
				echo $display;
			?>
		</select>
	</div>
</div>
<div class="upmp-clear"></div>

<div id="pcb_private_tab_role_panel" class="pcb_post_meta_row" <?php echo $show_role_field; ?> >
	<div class="pcb_post_meta_row_label"><strong><?php _e('Allowed User Roles','upmp'); ?></strong></div>
	<div class="pcb_post_meta_row_field">
		<?php foreach($user_roles as $role_key => $role){
				$checked_val = ''; 

				if(in_array($role_key, $visible_roles)  ){
					$checked_val = ' checked '; 
	
				}
				if($role_key != 'administrator'){
			?>
			<input type="checkbox" <?php echo $checked_val; ?> name="pcb_private_tab_visibility_roles[]" value='<?php echo $role_key; ?>'><?php echo $role; ?><br/>
			<?php } ?>	
		<?php } ?>		
	</div>

</div>

<div class="upmp-clear"></div>

<div class="pcb_post_meta_row">
	<div class="pcb_post_meta_row_label"><strong><?php _e('Tab Type','upmp'); ?></strong></div>
	<div class="pcb_post_meta_row_field">
		<select id="pcb_private_tab_type" name="pcb_private_tab_type" class="upmp-select2-setting">
			<option value='content' <?php selected('content',$tab_type); ?> ><?php _e('Content','upmp'); ?></option>
			<option value='predefined' <?php selected('predefined',$tab_type); ?> ><?php _e('Predefined','upmp'); ?></option>
			<!-- <option value='messages' <?php selected('messages',$tab_type); ?> ><?php _e('Messages','upmp'); ?></option>
			<option value='files' <?php selected('files',$tab_type); ?> ><?php _e('Files','upmp'); ?></option>
			 -->
			<?php			
			  	$private_tab_type_params = array('tab_type' => $tab_type);		
				$display = apply_filters('pcb_private_tab_types','', $private_tab_type_params );
				echo $display;
			?>
		</select>
	</div>
</div>
<div class="upmp-clear"></div>

<div id="pcb_private_tab_predefined_type_panel" class="pcb_post_meta_row" <?php echo $show_predefined_type_field; ?> >
	<div class="pcb_post_meta_row_label"><strong><?php _e('Predefined Types','upmp'); ?></strong></div>
	<div class="pcb_post_meta_row_field">
		<select id="pcb_private_tab_predefined_type" name="pcb_private_tab_predefined_type" class="upmp-select2-setting">
			<option value='upme_profile' <?php selected('upme_profile',$tab_predefined_type); ?> ><?php _e('User Profiles Made Easy Profile','upmp'); ?></option>
			<option value='upme_member_list' <?php selected('upme_member_list',$tab_predefined_type); ?> ><?php _e('User Profiles Made Easy Member List','upmp'); ?></option>
			
			
			<?php			
			  	$private_tab_type_params = array('post' => $post,'tab_type' => $tab_type, 'tab_predefined_type' => $tab_predefined_type );		
				$display = apply_filters('pcb_private_tab_predefined_types','', $private_tab_type_params );
				echo $display;
			?>
		</select>
	</div>
</div>

<div class="upmp-clear"></div>

<?php 
	$private_tab_predefined_additional_fields = array('visibility' => $visibility, 'post' => $post, 'tab_type' => $tab_type, 'tab_predefined_type' => $tab_predefined_type);
	echo apply_filters('pcb_private_tab_predefined_additional_fields','', $private_tab_predefined_additional_fields); 
?>

<div class="upmp-clear"></div>

<!-- <div id="pcb_private_tab_upload_type_panel" class="pcb_post_meta_row" <?php echo $show_upload_type_field; ?> >
	<div class="pcb_post_meta_row_label"><strong><?php _e('Upload Permission Type','upmp'); ?></strong></div>
	<div class="pcb_post_meta_row_field">
		<select id="pcb_private_tab_upload_permission_type" name="pcb_private_tab_upload_permission_type" class="upmp-select2-setting">
			<option value='none' <?php selected('none',$upload_permission_type); ?> ><?php _e('Please Select','upmp'); ?></option>
			<option value='member' <?php selected('member',$upload_permission_type); ?> ><?php _e('Members','upmp'); ?></option>
			<option value='role' <?php selected('role',$upload_permission_type); ?> ><?php _e('Selected User Roles','upmp'); ?></option>
			
			<?php
			
			  	$private_tab_upload_permission_type_params = array('upload_permission_type' => $upload_permission_type);
       			$display = apply_filters('pcb_private_tab_upload_permission_types','', $private_tab_upload_permission_type_params );
				echo $display;
			?>
		</select>
	</div>
</div>
<div class="upmp-clear"></div> -->

<!-- <div id="pcb_private_tab_upload_role_panel" class="pcb_post_meta_row" <?php echo $show_upload_role_field; ?> >
	<div class="pcb_post_meta_row_label"><strong><?php _e('Upload Allowed Roles','upmp'); ?></strong></div>
	<div class="pcb_post_meta_row_field">
		<?php foreach($user_roles as $role_key => $role){
				$checked_val = ''; 

				if(in_array($role_key, $visible_upload_roles)  ){
					$checked_val = ' checked '; 
	
				}
				if($role_key != 'administrator'){
			?>
			<input type="checkbox" <?php echo $checked_val; ?> name="pcb_private_tab_file_upload_roles[]" value='<?php echo $role_key; ?>'><?php echo $role; ?><br/>
			<?php } ?>	
		<?php } ?>		
	</div>

</div> -->

<div class="upmp-clear"></div>

<div class="pcb_post_meta_row">
	<div class="pcb_post_meta_row_label"><strong><?php _e('Tab Status','upmp'); ?></strong></div>
	<div class="pcb_post_meta_row_field">
		<select id="pcb_private_tab_status" name="pcb_private_tab_status" class="upmp-select2-setting">
			<option value='enabled' <?php selected('enabled',$tab_status); ?> ><?php _e('Enabled','upmp'); ?></option>
			<option value='disabled' <?php selected('disabled',$tab_status); ?> ><?php _e('Disabled','upmp'); ?></option>
			
		</select>
	</div>
</div>
<div class="upmp-clear"></div>


<?php 
	$private_tab_visibility_additional_fields = array('visibility' => $visibility, 'post' => $post);
	echo apply_filters('pcb_private_tab_visibility_additional_fields','', $private_tab_visibility_additional_fields); 
?>

<div class="upmp-clear"></div>

<?php wp_nonce_field( 'pcb_private_tab_settings', 'pcb_private_tab_settings_nonce' ); ?>



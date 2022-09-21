<?php
	global $pcb_private_page_files_data;
	extract($pcb_private_page_files_data);

	$url = esc_url($_SERVER['REQUEST_URI']);
	$url = pcb_add_query_string($url,'pcb_private_page_file_download=yes');
	$url = pcb_add_query_string($url,'pcb_file_id='.$file_data['id']);
 		
?>
<div class='upmp-private-page-file-item'  data-file-id="<?php echo $file_data['id']; ?>" >
	<div class='upmp-private-page-file-item-row'    >
		<div class='upmp-private-page-file-item-name' ><?php echo $file_data['file_name']; ?></div>

		<?php 
			  if(!current_user_can('manage_options') && $file_data['admin_status'] == 'ACTIVE' && $file_data['user_read_status'] == 'UNREAD'){
		?>
				<span class="upmp-file-read-status"><?php echo __('UNREAD','upmp'); ?></span>
		<?php } ?>

		<?php 
			  if(current_user_can('manage_options') && $file_data['admin_status'] == 'INACTIVE' && $file_data['admin_read_status'] == 'UNREAD'){
		?>
				<span class="upmp-file-read-status"><?php echo __('UNREAD','upmp'); ?></span>
		<?php } ?>

		<?php if(get_current_user_id() == $file_data['user_id'] && $file_data['admin_status'] != 'ACTIVE'){ ?>
			<div class='upmp-private-page-file-item-delete' ><?php echo __('Delete','upmp'); ?></div>
		<?php }else if(current_user_can('manage_options')){ ?>
			<div data-user-id="<?php echo $file_data['user_id']; ?>" class='upmp-private-page-admin-file-item-delete' ><?php echo __('Delete','upmp'); ?></div>
		<?php } ?>
		<div class='upmp-private-page-file-item-view' ><?php echo __('View Details','upmp'); ?></div>
		<div class='upmp-private-page-file-item-download' ><a href="<?php echo $url; ?>"><?php echo __('Download','upmp'); ?></a></div>
		<div class='upmp-clear'></div>
	</div>
	<div class='upmp-private-page-file-item-data upmp-private-page-file-item-data-closed' >
		<div class='upmp-private-page-file-item-data-row' >
			<div class="upmp-private-page-files-list-label" ><?php echo __('Uploaded By','upmp'); ?></div>
			<div class="upmp-private-page-files-list-field" ><?php echo $user_display_name; ?></div>
			<div class='upmp-clear'></div>
		</div>
		<div class='upmp-private-page-file-item-data-row' >
			<div class="upmp-private-page-files-list-label" ><?php echo __('Description','upmp'); ?></div>
			<div class="upmp-private-page-files-list-field" ><?php echo wp_unslash($file_data['description']); ?></div>
			<div class='upmp-clear'></div>
		</div>
	</div>
	<div class='upmp-clear'></div>
</div>




<?php
	global $pcb_private_page_data;
	extract($pcb_private_page_data);

	$content_tab_title = apply_filters('pcb_content_tab_title', __('Content','upmp') , array() );
	$discussion_tab_title = apply_filters('pcb_discussion_tab_title', __('Discussion','upmp') , array() );
	$files_tab_title = apply_filters('pcb_files_tab_title', __('Files','upmp') , array() );

	$filtered_main_content = apply_filters('the_content', $main_content);
?>

<div class='upmp-private-page-container upmp-private-page-single'>

	<!-- <div class='upmp-group-title'><?php echo $group_data->post_title; ?></div> -->
	<div class='upmp-private-page-tabs'>
		<?php if($private_page_content_tab_status){ ?>
			<div class='upmp-private-page-tab upmp-private-page-content-tab' data-tab-id='upmp-private-page-content' ><?php echo $content_tab_title; ?></div>		
		<?php } ?>

		<?php if($private_page_discussion_tab_status){ ?>
			<div class='upmp-private-page-tab upmp-private-page-disscussion-tab' data-tab-id='upmp-private-page-disscussion' ><?php echo $discussion_tab_title; ?></div>
		<?php } ?>

		<?php if($private_page_files_tab_status){ ?>
			<div class='upmp-private-page-tab upmp-private-page-files-tab' data-tab-id='upmp-private-page-files' ><?php echo $files_tab_title; ?></div>
		<?php } ?>

		<?php do_action('pcb_private_page_user_custom_tabs', array('current_user_id' => $current_user_id, 'pcb_private_page_data' => $pcb_private_page_data ) ); ?>
		

		<div class="upmp-clear"></div>
	</div>
	<div class='upmp-private-page-tabs-content'>
		<?php if($private_page_content_tab_status){ ?>
		<!-- wpautop(do_shortcode() is removed. -->
		<div style="display:block;" class='upmp-private-page-tab-content upmp-private-page-content-tab-content'><?php echo $filtered_main_content; ?></div>
		<?php } ?>

		<?php if($private_page_discussion_tab_status){ ?>
		<div style="display:none;" class='upmp-private-page-tab-content upmp-private-page-disscussion-tab-content'>
			<div class="upmp-private-page-disscussion-tab-post">
				<div class="upmp-private-page-disscussion-tab-msg" style="display:none;"></div>
				<div class="upmp-private-page-disscussion-tab-editor"><textarea placeholder="<?php echo __('Write a Message','upmp'); ?>" class="" name="" id="" ></textarea></div>
				<div class="upmp-private-page-disscussion-tab-members">
					
					
					<span><input type="button" class="pcb_button_color upmp-private-page-disscussion-tab-submit" value="<?php echo __('Post','upmp'); ?>" name="" id="" /></span>
				
				</div>
				
			</div>	

			<div class="upmp-private-page-messages-list">
				<?php
					foreach ($messages as $msg_key => $msg_data) {

				?>
					<div class="upmp-private-page-messages-single-block"  data-message-id="<?php echo $msg_data['message_id']; ?>" >
						<div class="upmp-private-page-messages-single-block-header">
							<?php if($msg_data['admin_status'] == 'ACTIVE'){ ?>
								<div class="upmp-private-page-messages-single-block-avatar"><?php echo get_avatar(pcb_ADMIN_GRAVATAR_EMAIL,50); ?></div>
								<div class="upmp-private-page-messages-single-block-names">
								<div class="upmp-comment-name"><?php echo __('Administrator','upmp'); ?></div>

							<?php }else{ ?>
								<div class="upmp-private-page-messages-single-block-avatar"><?php echo $msg_data['message_avatar']; ?></div>
								<div class="upmp-private-page-messages-single-block-names">
								<div class="upmp-comment-name"><?php echo $msg_data['user_display_name']; ?></div>
							<?php } ?>
							
							
								<div><?php $date = DateTime::createFromFormat('Y-m-d H:i:s', $msg_data['message_date']);
											echo $date->format('F j, Y, g:i a'); ?>
									<?php 
										  if($msg_data['admin_status'] == 'ACTIVE' && $msg_data['user_read_status'] == 'UNREAD'){
									?>
											<span class="upmp-message-read-status"><?php echo __('UNREAD','upmp'); ?></span>
									<?php } ?>
								</div>
							</div>
							<?php if(current_user_can('manage_options') || (get_current_user_id() == $msg_data['user_id'] && $msg_data['admin_status'] != 'ACTIVE' )){ ?>
							<div class="upmp-private-page-messages-single-message-delete"><?php echo __('Delete','upmp'); ?></div>
							<?php } ?>
							
						</div>
						<div class="upmp-clear"></div>
						<div class="upmp-private-page-messages-single-block-message">
							<?php echo wp_unslash($msg_data['message']); ?>
						</div>
						<div class="upmp-private-page-messages-single-block-actions">						
							<div class="upmp-private-page-messages-single-block-view-comment"><?php echo count($msg_data['comments']); ?> <?php echo __('Comments','upmp'); ?></div>
							<div class="upmp-clear"></div>
						</div>
						<div class="upmp-private-page-messages-single-block-comments">
							<?php
								foreach ($msg_data['comments'] as $comments_key => $comments_data) {
							?>
								<div id="upmp-private-page-messages-single-block-comment"  data-message-id="<?php echo $comments_data['message_id']; ?>" class="upmp-private-page-messages-single-block-comment ">
									<?php if($comments_data['admin_status'] == 'ACTIVE'){ ?>
										<div class="upmp-private-page-messages-single-block-avatar"><?php echo get_avatar(pcb_ADMIN_GRAVATAR_EMAIL,50); ?></div>
										<div class="upmp-private-page-messages-single-block-names">
										<div class="upmp-comment-name"><?php echo __('Administrator','upmp'); ?></div>

									<?php }else{ ?>
										<div class="upmp-private-page-messages-single-block-avatar"><?php echo $comments_data['message_avatar']; ?></div>
										<div class="upmp-private-page-messages-single-block-names">
										<div class="upmp-comment-name"><?php echo $comments_data['user_display_name']; ?></div>
									<?php } ?>

									
										<div class="upmp-comment-message"><?php echo wp_unslash($comments_data['message']); ?></div>
										<div class="upmp-comment-date"><?php $date = DateTime::createFromFormat('Y-m-d H:i:s', $comments_data['message_date']);
											echo $date->format('F j, Y, g:i a'); ?>

											<?php 
												  if($comments_data['admin_status'] == 'ACTIVE' && $comments_data['user_read_status'] == 'UNREAD'){
											?>
													<span class="upmp-message-read-status"><?php echo __('UNREAD','upmp'); ?></span>
											<?php } ?>
										</div>
									</div>
									<?php if( current_user_can('manage_options') || (get_current_user_id() == $comments_data['user_id'] && $comments_data['admin_status'] != 'ACTIVE') ){ ?>
										<div class="upmp-private-page-messages-single-comment-delete"><?php echo __('Delete','upmp'); ?></div>
									<?php } ?>
									<div class="upmp-clear"></div>
								</div>
							<?php
								}
							?>
						</div>
						<div class="upmp-private-page-messages-single-block-add-comments">
							<div class="upmp-private-page-messages-single-comment-avatar"><?php echo $msg_data['current_user_avatar']; ?></div>
							<div class="upmp-private-page-messages-single-comment-editor">
								<textarea class="upmp-private-page-messages-single-comment"></textarea>
								
							</div>
							<div class="upmp-private-page-messages-single-comment-button">
								<input class="pcb_button_color upmp-private-page-messages-single-comment-save"  data-message-id="<?php echo $msg_data['message_id']; ?>" type="button" value="<?php echo __('Comment','upmp');?>" />
							</div>		 
							<div class="upmp-clear"></div>				
						</div>
					</div>
				<?php
						# code...
					}
				?>
				
					
						
			</div>

			<?php if($message_pagination_status){ ?>
				<div  data-pagination-page="<?php echo $message_pagination_page; ?>" class="upmp-private-page-messages-pagination"><?php echo __('View More Messages','upmp'); ?></div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if($private_page_files_tab_status){ ?>
		<div style="display:none;" class='upmp-private-page-tab-content upmp-private-page-files-tab-content'>
			<div class='upmp-private-page-files-create'>
				<div class='upmp-private-page-files-add'>
					<div class='upmp-private-page-files-add-btn'><?php echo __('Add File','upmp'); ?></div>
				</div>
				<div class='upmp-private-page-files-add-form' >
					<form class="upmp-private-page-file-upload-form">
						<div class="upmp-private-page-files-msg" style="display:none" ></div>
						<div class='upmp-private-page-files-add-form-row'>
							<div class='upmp-private-page-files-add-form-label'><?php echo __('File Title','upmp'); ?></div>
							<div class='upmp-private-page-files-add-form-field'>
								<input type="text" class="upmp-private-page-file-name" name="pcb_private_page_file_name" />
							</div>
							<div class="upmp-clear"></div>
						</div>
						<div class='upmp-private-page-files-add-form-row'>
							<div class='upmp-private-page-files-add-form-label'><?php echo __('Description','upmp'); ?></div>
							<div class='upmp-private-page-files-add-form-field'>
								<textarea class="upmp-private-page-file-desc" name="pcb_private_page_file_desc" ></textarea>
							</div>
							<div class="upmp-clear"></div>
						</div>
						<div class='upmp-private-page-files-add-form-row'>
							<div class='upmp-private-page-files-add-form-label'><?php echo __('File','upmp'); ?></div>
							<div class='upmp-private-page-files-add-form-field'>
								<input type="file" class="upmp-private-page-file" name="pcb_private_page_file" />
							</div>
							<div class="upmp-clear"></div>
						</div>
						<div class='upmp-private-page-files-add-form-row'>
							<div class='upmp-private-page-files-add-form-label'>&nbsp;
								
								<input type="hidden" class="upmp-private-page-file-nonce" name="pcb_private_page_file_nonce" />
							</div>
							<div class='upmp-private-page-files-add-form-field'><input type="submit" class="upmp-private-page-file-upload" value="<?php echo __('Upload','upmp'); ?>" /></div>
							<div class="upmp-clear"></div>
						</div>
					</form>
				</div>

				
			</div>
			<div class="upmp-private-page-files-list-msg" style="display:none" ></div>
			<div class='upmp-private-page-files-list' >
				<?php
					$url = esc_url($_SERVER['REQUEST_URI']);
					$url = pcb_add_query_string($url,'pcb_private_page_file_download=yes');

					foreach($files_list as $file_row){

						$url = pcb_add_query_string($url,'pcb_file_id='.$file_row->id);
	       

						$user_display_name = get_user_meta($file_row->user_id,'first_name',true)." ".get_user_meta($file_row->user_id,'last_name',true) ;
		                if(trim($user_display_name) == ''){
		                    $user_display_name = get_user_meta($file_row->user_id,'nickname',true) ;                    
		                }

		                if($file_row->admin_status == 'ACTIVE'){
		                	$user_display_name = __('Administrator','upmp');
		                }
				?>
					<div class='upmp-private-page-file-item'  data-file-id="<?php echo $file_row->id; ?>" >
						<div class='upmp-private-page-file-item-row'    >
							<div class='upmp-private-page-file-item-name' ><?php echo $file_row->file_name; ?></div>
							<?php 
								  if($file_row->admin_status == 'ACTIVE' && $file_row->user_read_status == 'UNREAD'){
							?>
									<span class="upmp-file-read-status"><?php echo __('UNREAD','upmp'); ?></span>
							<?php } ?>

							<?php if(get_current_user_id() == $file_row->user_id && $file_row->admin_status != 'ACTIVE'){ ?>
							<div class='upmp-private-page-file-item-delete' ><?php echo __('Delete','upmp'); ?></div>
							<?php } ?>
							<div class='upmp-private-page-file-item-view' ><?php echo __('View Details','upmp'); ?></div>
							<div class='upmp-private-page-file-item-download' ><a href="<?php echo $url; ?>" ><?php echo __('Download','upmp'); ?></a></div>
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
								<div class="upmp-private-page-files-list-field" ><?php echo wp_unslash($file_row->description); ?></div>
								<div class='upmp-clear'></div>
							</div>
						</div>
						<div class='upmp-clear'></div>
					</div>	
				<?php
					}
				?>
			</div>
			<?php if($file_pagination_status){ ?>
				<div  data-pagination-page="<?php echo $file_pagination_page; ?>" class="upmp-private-page-files-pagination"><?php echo __('View More Files','upmp'); ?></div>
			<?php } ?>
		</div>
		<?php } ?>

		<?php do_action('pcb_private_page_user_custom_tabs_content', array('current_user_id' => $current_user_id, 'pcb_private_page_data' => $pcb_private_page_data ) ); ?>
		
		
	</div>
	<div class="upmp-clear"></div>
</div>



<div id="upmp-private-page-messages-single-block" class="upmp-private-page-messages-single-block upmp-private-page-messages-single-template" >
	<div class="upmp-private-page-messages-single-block-header">
		<div class="upmp-private-page-messages-single-block-avatar"></div>
		<div class="upmp-private-page-messages-single-block-names">
			<div class="upmp-message-name"></div>
			<div class="upmp-message-date"></div>
		</div>
		<div class="upmp-private-page-messages-single-message-delete"><?php echo __('Delete','upmp'); ?></div>
	</div>
	<div class="upmp-clear"></div>
	<div class="upmp-private-page-messages-single-block-message">
		
	</div>
	<div class="upmp-private-page-messages-single-block-actions">						
		<div class="upmp-private-page-messages-single-block-view-comment">0 <?php echo __('Comments','upmp'); ?></div>
		<div class="upmp-clear"></div>
	</div>
	<div class="upmp-private-page-messages-single-block-comments">
		<div></div>
	</div>
	<div class="upmp-private-page-messages-single-block-add-comments">
		<div class="upmp-private-page-messages-single-comment-avatar"></div>
		<div class="upmp-private-page-messages-single-comment-editor">
			<textarea class="upmp-private-page-messages-single-comment"></textarea>
			
		</div>
		<div class="upmp-private-page-messages-single-comment-button">
			<input class="pcb_button_color upmp-private-page-messages-single-comment-save" type="button" value="<?php echo __('Comment','upmp'); ?>" />
		</div>		
		<div class="upmp-clear"></div>				
	</div>
</div>

<div id="upmp-private-page-messages-single-block-comment" class="upmp-private-page-messages-single-block-comment upmp-private-page-messages-single-block-comment-template">
	<div class="upmp-private-page-messages-single-comment-avatar"></div>
	<div class="upmp-private-page-messages-single-comment-names">
		<div class="upmp-comment-name"></div>
		<div class="upmp-comment-message"></div>
		<div class="upmp-comment-date"></div>
	</div>
	<div class="upmp-private-page-messages-single-comment-delete"><?php echo __('Delete','upmp'); ?></div>

	<div class="upmp-clear"></div>
</div>

<?php
	global $pcb_private_page_data;
	extract($pcb_private_page_data);


?>
<div id="upmp-private-page-messages-single-block"  data-message-id="<?php echo $message_data['id']; ?>" class="upmp-private-page-messages-single-block " >
	<div class="upmp-private-page-messages-single-block-header">
		<div class="upmp-private-page-messages-single-block-avatar">
			<?php 	if($message_data['admin_status'] == 'ACTIVE'){ 	
			 			echo get_avatar(pcb_ADMIN_GRAVATAR_EMAIL,50); 
			 		}else{ 
			 			echo $message_data['user_avatar'];

			 		} 
			?>
		</div>
		<div class="upmp-private-page-messages-single-block-names">
			<?php if($message_data['admin_status'] == 'ACTIVE'){ ?>				
				<div class="upmp-message-name"><?php echo __('Administrator','upmp'); ?></div>
			<?php }else{ ?>
				<div class="upmp-message-name"><?php echo $message_data['user_display_name']; ?></div>
			<?php } ?>
			
			<div class="upmp-message-date"><?php $date = DateTime::createFromFormat('Y-m-d H:i:s', $message_data['updated_at']);
											echo $date->format('F j, Y, g:i a'); ?>
				<?php if(!current_user_can('manage_options') && $message_data['admin_status'] == 'ACTIVE' && $message_data['user_read_status'] == 'UNREAD'){ ?>
						<span class="upmp-message-read-status"><?php echo __('UNREAD','upmp'); ?></span>
				<?php }
					  if(current_user_can('manage_options') && $message_data['admin_status'] == 'INACTIVE' && $message_data['admin_read_status'] == 'UNREAD'){
				?>
						<span class="upmp-message-read-status"><?php echo __('UNREAD','upmp'); ?></span>
				<?php } ?>
			</div>
		</div>

		<?php if( get_current_user_id() == $message_data['user_id'] && $message_data['admin_status'] != 'ACTIVE'){ ?>
			<div class="upmp-private-page-messages-single-message-delete"><?php echo __('Delete','upmp'); ?></div>
		<?php } else if(current_user_can('manage_options')){ ?>
			<div data-user-id="<?php echo $message_data['user_id']; ?>" class="upmp-private-page-admin-messages-single-message-delete"><?php echo __('Delete','upmp'); ?></div>
		<?php } ?>
	</div>
	<div class="upmp-clear"></div>
	<div class="upmp-private-page-messages-single-block-message">
		<?php echo wp_unslash($message_data['message']); ?>
	</div>
	<div class="upmp-private-page-messages-single-block-actions">						
		<div class="upmp-private-page-messages-single-block-view-comment">0 <?php echo __('Comments','upmp'); ?></div>
		<div class="upmp-clear"></div>
	</div>
	<div class="upmp-private-page-messages-single-block-comments">
		<?php
			if($comments_data){
				foreach ($comments_data as $key => $comment) {

					$user_comment_display_name = get_user_meta($comment['user_id'],'first_name',true)." ".get_user_meta($comment['user_id'],'last_name',true) ;
                    if(trim($user_comment_display_name) == ''){
                        $user_comment_display_name = get_user_meta($comment['user_id'],'nickname',true) ;                    
                    }

                    $comment_user_avatar = get_avatar( $comment['user_id'], 50 )
		?>
				<div id="upmp-private-page-messages-single-block-comment"  data-message-id="<?php echo $comment['id']; ?>" class="upmp-private-page-messages-single-block-comment ">
					<?php if($comment['admin_status'] == 'ACTIVE'){ ?>
						<div class="upmp-private-page-messages-single-comment-avatar"><?php echo get_avatar(pcb_ADMIN_GRAVATAR_EMAIL,50); ?></div>
						<div class="upmp-private-page-messages-single-comment-names">
						<div class="upmp-comment-name"><?php echo __('Administrator','upmp'); ?></div>

					<?php }else{ ?>
						<div class="upmp-private-page-messages-single-comment-avatar"><?php echo $comment_user_avatar; ?></div>
						<div class="upmp-private-page-messages-single-comment-names">
						<div class="upmp-comment-name"><?php echo $user_comment_display_name; ?></div>
					<?php } ?>
					
						<div class="upmp-comment-message"><?php echo wp_unslash($comment['message']); ?></div>
						<div class="upmp-comment-date">
							<?php $date = DateTime::createFromFormat('Y-m-d H:i:s', $comment['updated_at']);
											echo $date->format('F j, Y, g:i a'); ?>
							<?php if(!current_user_can('manage_options') && $comment['admin_status'] == 'ACTIVE' && $comment['user_read_status'] == 'UNREAD'){ ?>
									<span class="upmp-message-read-status"><?php echo __('UNREAD','upmp'); ?></span>
							<?php }
								  if(current_user_can('manage_options') && $comment['admin_status'] == 'INACTIVE' && $comment['admin_read_status'] == 'UNREAD'){
							?>
									<span class="upmp-message-read-status"><?php echo __('UNREAD','upmp'); ?></span>
							<?php } ?>
						</div>
					</div>
					<?php if( ( get_current_user_id() == $comment['user_id'] && $comment['admin_status'] != 'ACTIVE')){ ?>
						<div class="upmp-private-page-messages-single-comment-delete"><?php echo __('Delete','upmp'); ?></div>
					<?php }else if(current_user_can('manage_options')){ ?>
						<div data-user-id="<?php echo $comment['user_id']; ?>" class="upmp-private-page-admin-messages-single-comment-delete"><?php echo __('Delete','upmp'); ?></div>
					
					<?php } ?>
					<div class="upmp-clear"></div>
				</div>
		<?php
				}
			}
		?>
	</div>
	<div class="upmp-private-page-messages-single-block-add-comments">
		<?php if(current_user_can('manage_options')){ ?>
			<div class="upmp-private-page-messages-single-comment-avatar"><?php echo get_avatar(pcb_ADMIN_GRAVATAR_EMAIL,50);  ?></div>
		<?php }else{ ?>
			<div class="upmp-private-page-messages-single-comment-avatar"><?php echo $message_data['current_user_avatar']; ?></div>
		
		<?php } ?>
		<div class="upmp-private-page-messages-single-comment-editor">
			<textarea class="upmp-private-page-messages-single-comment"></textarea>
			
		</div>
		<div class="upmp-private-page-messages-single-comment-button">
			<?php if(get_current_user_id() == $message_data['current_user_id']){ ?>
				<input class="pcb_button_color upmp-private-page-messages-single-comment-save"  data-message-id="<?php echo $message_data['id']; ?>" type="button" value="<?php echo __('Comment','upmp'); ?>" />
			<?php }else if(get_current_user_id() != $message_data['current_user_id'] && current_user_can('manage_options')){ ?>
				<input data-user-id="<?php echo $message_data['current_user_id']; ?>" class="pcb_button_color upmp-private-page-admin-messages-single-comment-save"  data-message-id="<?php echo $message_data['id']; ?>" type="button" value="<?php echo __('Comment','upmp'); ?>" />
			
			<?php } ?>
		</div>		
		<div class="upmp-clear"></div>				
	</div>
</div>


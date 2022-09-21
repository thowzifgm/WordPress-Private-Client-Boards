jQuery(document).ready(function($) {

    jQuery(".upmp-private-page-tab").click(function(){
        var tab_class = "."+$(this).attr('data-tab-id')+"-tab-content";

        $(this).closest('.upmp-private-page-single').find('.upmp-private-page-tab-content').hide();

        $(this).closest('.upmp-private-page-single').find(tab_class).show();

        $(".upmp-private-page-tab").removeClass('upmp-private-page-active-tab');
        $(this).addClass('upmp-private-page-active-tab');
    });

    $('.upmp-private-page-disscussion-tab-submit').click(function(){
    	var post_message_container = $(this).closest('.upmp-private-page-disscussion-tab-post');
    	var discussion_container = $(this).closest('.upmp-private-page-disscussion-tab-content');

    	post_message_container.find('.upmp-private-page-disscussion-tab-msg').removeClass('upmp-message-info-error').removeClass('upmp-message-info-success').hide();
    	
    	var post_message = post_message_container.find('.upmp-private-page-disscussion-tab-editor textarea').val();
    	if($.trim(post_message) === ''){
    		post_message_container.find('.upmp-private-page-disscussion-tab-msg').html(UPMPPage.Messages.messageEmpty).addClass('upmp-message-info-error').show();
    	}else{

    		$.post(
	            UPMPPage.AdminAjax,
	            {
	                'action': 'pcb_add_private_page_post_message',
	                'verify_nonce': UPMPPage.nonce,
	                'message' : post_message,
	                'type' : 'message',
	                
	            },
	            function(response){
	            	post_message_container.find('.upmp-private-page-disscussion-tab-editor textarea').val("");

	                if(response.status == 'success'){
	                    discussion_container.find('.upmp-private-page-messages-list').prepend(response.data.messages_html);
	                }      
	            },"json"
	        );
    	}
    });

	$('.upmp-private-page-admin-disscussion-tab-submit').click(function(){
    	var post_message_container = $(this).closest('.upmp-private-page-disscussion-tab-post');
    	var discussion_container = $(this).closest('.upmp-private-page-disscussion-tab-content');
    	var user_id = $(this).attr('data-user-id');

    	post_message_container.find('.upmp-private-page-disscussion-tab-msg').removeClass('upmp-message-info-error').removeClass('upmp-message-info-success').hide();
    	
    	var post_message = post_message_container.find('.upmp-private-page-disscussion-tab-editor textarea').val();
    	if($.trim(post_message) === ''){
    		post_message_container.find('.upmp-private-page-disscussion-tab-msg').html(UPMPPage.Messages.messageEmpty).addClass('upmp-message-info-error').show();
    	}else{

    		$.post(
	            UPMPPage.AdminAjax,
	            {
	                'action': 'pcb_add_private_page_post_message',
	                'verify_nonce': UPMPPage.nonce,
	                'message' : post_message,
	                'type' : 'message',
	                'admin_status': 'ACTIVE',
	                'user_id' : user_id,
	            },
	            function(response){
	            	post_message_container.find('.upmp-private-page-disscussion-tab-editor textarea').val("");

	                if(response.status == 'success'){
	                    discussion_container.find('.upmp-private-page-messages-list').prepend(response.data.messages_html);
	                }      
	            },"json"
	        );
    	}
    });

	$('.upmp-private-page-tab-content').on("click",".upmp-private-page-messages-single-comment-save", function(){
		$(this).removeClass('upmp-error-outline');
		var message_container = $(this).closest('.upmp-private-page-messages-single-block-add-comments')
		var message = message_container.find('.upmp-private-page-messages-single-comment').val();
		var single_message_block = $(this).closest('.upmp-private-page-messages-single-block');
	
		if(message == ''){
			$(this).closest('.upmp-private-page-messages-single-block-add-comments').find('.upmp-private-page-messages-single-comment').addClass('upmp-error-outline');
		}else{
			$(this).removeClass('upmp-error-outline');
			var message_id = $(this).attr('data-message-id');
			// console.log(group_id+" "+ message_id);

			$.post(
	            UPMPPage.AdminAjax,
	            {
	                'action': 'pcb_add_private_page_comment_message',
	                'verify_nonce': UPMPPage.nonce,
	                'message_id' : message_id,
	                'message' : message,
	            },
	            function(response){
	            	message_container.find('.upmp-private-page-messages-single-comment').val("");

	                if(response.status == 'success'){
	                	var comments_container = single_message_block.find('.upmp-private-page-messages-single-block-comments');
	                	//console.log(comments_container.html());
	                    var new_comment_block = $("#upmp-private-page-messages-single-block-comment").clone().removeClass('upmp-group-messages-single-block-comment-template').prependTo(comments_container);
	                    
	                    new_comment_block.find('.upmp-comment-name').html(response.data.current_user_display_name);
	                    new_comment_block.find('.upmp-comment-date').html(response.data.message_date);
	                	new_comment_block.find('.upmp-comment-message').html(response.data.message);
	                	new_comment_block.find('.upmp-private-page-messages-single-comment-avatar').html('');
	                	new_comment_block.find('.upmp-private-page-messages-single-comment-avatar').html(response.data.avatar);
	                	console.log(response.data.avatar);
	                    new_comment_block.removeClass('upmp-private-page-messages-single-block-comment-template');

	                    new_comment_block.attr('data-private-page-id',response.data.group_id);
	                	new_comment_block.attr('data-message-id',response.data.message_id);
	                    // console.log(response.data);
	                    // var group_messages_list = discussion_container.find(".upmp-group-messages-list");
	                    
	                }      
	            },"json"
	        );
		}
	});

	$('.upmp-private-page-tab-content').on("click",".upmp-private-page-messages-pagination", function(){
		var pagination_button = $(this);
    	var data_page = $(this).attr("data-pagination-page");
    	var discussion_container = $(this).closest('.upmp-private-page-disscussion-tab-content');

		$.post(
            UPMPPage.AdminAjax,
            {
                'action': 'pcb_load_private_page_paginated_messages',
                'verify_nonce': UPMPPage.nonce,
                'data_page' : data_page
            },
            function(response){

                if(response.status == 'success'){
                    if(response.message_pagination_status == '0'){
                    	pagination_button.hide();
                    }else{
                    	pagination_button.attr('data-pagination-page', response.data_page );
                    }

                    discussion_container.find('.upmp-private-page-messages-list').append(response.messages_html);
                }      
            },"json"
        );    	
    });

	$("body").on("click",".upmp-private-page-messages-single-comment-delete", function(){
		var comment_block = $(this).closest('.upmp-private-page-messages-single-block-comment');
		var comment_id   = comment_block.attr('data-message-id');

		if(confirm(UPMPPage.Messages.confirmDelete)){
			$.post(
	            UPMPPage.AdminAjax,
	            {
	                'action': 'pcb_delete_private_page_comment',
	                'verify_nonce': UPMPPage.nonce,
	                'comment_id':   comment_id
	            },
	            function(response){

	                if(response.status == 'success'){
	                	comment_block.before('<div class="upmp-private-page-disscussion-tab-msg upmp-message-info-success" id="upmp-private-page-disscussion-delete-error" >' + response.msg + '</div>');
	                	comment_block.remove();
	                	setTimeout(function(){ $('#upmp-private-page-disscussion-delete-error').remove(); }, 3000);

	                }else if(response.status == 'error'){	                	
	                	comment_block.before('<div class="upmp-private-page-disscussion-tab-msg upmp-message-info-error" id="upmp-private-page-disscussion-delete-error" >' + response.msg + '</div>');
	                	setTimeout(function(){ $('#upmp-private-page-disscussion-delete-error').remove(); }, 3000);
	                }      
	            },"json"
	        );
		}
		  
	});

	$("body").on("click",".upmp-private-page-messages-single-message-delete", function(){
		var message_block = $(this).closest('.upmp-private-page-messages-single-block');
		var message_id   = message_block.attr('data-message-id');

		if(confirm(UPMPPage.Messages.confirmDelete)){
			$.post(
	            UPMPPage.AdminAjax,
	            {
	                'action': 'pcb_delete_private_page_message',
	                'verify_nonce': UPMPPage.nonce,
	                'message_id':   message_id
	            },
	            function(response){

	                if(response.status == 'success'){
	                	message_block.before('<div class="upmp-private-page-disscussion-tab-msg upmp-message-info-success" id="upmp-private-page-disscussion-delete-error" >' + response.msg + '</div>');
	                	message_block.remove();
	                	setTimeout(function(){ $('#upmp-private-page-disscussion-delete-error').remove(); }, 3000);

	                }else if(response.status == 'error'){	                	
	                	message_block.before('<div class="upmp-private-page-disscussion-tab-msg upmp-message-info-error" id="upmp-private-page-disscussion-delete-error" >' + response.msg + '</div>');
	                	setTimeout(function(){ $('#upmp-private-page-disscussion-delete-error').remove(); }, 3000);
	                }      
	            },"json"
	        );
		}
		  
	});

	$('.upmp-private-page-tab-content').on("click",".upmp-private-page-admin-messages-single-comment-save", function(){
		$(this).removeClass('upmp-error-outline');
		var message_container = $(this).closest('.upmp-private-page-messages-single-block-add-comments')
		var message = message_container.find('.upmp-private-page-messages-single-comment').val();
		var single_message_block = $(this).closest('.upmp-private-page-messages-single-block');
		var user_id = $(this).attr('data-user-id');
	
		if(message == ''){
			$(this).closest('.upmp-private-page-messages-single-block-add-comments').find('.upmp-private-page-messages-single-comment').addClass('upmp-error-outline');
		}else{
			$(this).removeClass('upmp-error-outline');
			var message_id = $(this).attr('data-message-id');
			// console.log(group_id+" "+ message_id);

			$.post(
	            UPMPPage.AdminAjax,
	            {
	                'action': 'pcb_add_private_page_comment_message',
	                'verify_nonce': UPMPPage.nonce,
	                'message_id' : message_id,
	                'message' : message,
	                'admin_status' : 'ACTIVE',
	                'user_id' : user_id,
	            },
	            function(response){
	            	message_container.find('.upmp-private-page-messages-single-comment').val("");

	                if(response.status == 'success'){
	                	var comments_container = single_message_block.find('.upmp-private-page-messages-single-block-comments');
	                	console.log(comments_container.html());
	                    var new_comment_block = $("#upmp-private-page-messages-single-block-comment").clone().removeClass('upmp-group-messages-single-block-comment-template').prependTo(comments_container);
	                    new_comment_block.find('.upmp-comment-name').html(response.data.current_user_display_name);
	                    new_comment_block.find('.upmp-comment-date').html(response.data.message_date);
	                	new_comment_block.find('.upmp-comment-message').html(response.data.message);
	                	new_comment_block.find('.upmp-private-page-messages-single-comment-avatar').html(response.data.avatar);
	                	new_comment_block.removeClass('upmp-private-page-messages-single-block-comment-template');
	                    new_comment_block.attr('data-private-page-id',response.data.group_id);
	                	new_comment_block.attr('data-message-id',response.data.message_id);
	                     console.log(response.data);
	                    // var group_messages_list = discussion_container.find(".upmp-group-messages-list");
	                    
	                }      
	            },"json"
	        );
		}
	});


	$(".upmp-private-page-files-add-btn").click(function(){
		$(this).closest('.upmp-private-page-files-create').find('.upmp-private-page-files-add-form').show();
	});

	$(".upmp-private-page-file-upload-form").submit(function(e){
	
		e.preventDefault();
		var file_form = $(this);

		file_form.find(".upmp-private-page-file-nonce").val(UPMPPage.nonce);
		var file_name = file_form.find(".upmp-private-page-file-name").val();
		var file_desc = file_form.find(".upmp-private-page-file-desc").val();
		var file = file_form.find(".upmp-private-page-file").val();

		var msg_container = file_form.find(".upmp-private-page-files-msg");
		msg_container.removeClass('upmp-message-info-error').removeClass('upmp-message-info-success');

		file_form.find(".upmp-private-page-file-name").removeClass("upmp-error-outline");
		file_form.find(".upmp-private-page-file").removeClass("upmp-error-outline");

		var err = 0;
		var err_msg = '';
		if(file_name == ''){
			err_msg += "" + UPMPPage.Messages.fileNameRequired + "<br/>";
			file_form.find(".upmp-private-page-file-name").addClass("upmp-error-outline");
			err++;
		}

		if(file == ''){
			err_msg += "" + UPMPPage.Messages.fileRequired + "<br/>";
			file_form.find(".upmp-private-page-file").addClass("upmp-error-outline");
			err++;
		}

		if(err != 0){
			msg_container.html(err_msg).addClass('upmp-message-info-error').show();
		}else{

			msg_container.html("").hide();

			var formObj = file_form;
        	var formURL = UPMPPage.AdminAjax+'?action=pcb_save_private_page_files';
        	var formData = new FormData(this);
        	console.log(formData);

        	jQuery.ajax({
	            url: formURL,
	            type: 'POST',
	            data:  formData,
	            mimeType:"multipart/form-data",
	            contentType: false,
	            cache: false,
	            dataType : "json",
	            processData:false,
	            success: function(data, textStatus, jqXHR)
	            {
	            	if(data.status == 'success'){
	            		msg_container.html(data.msg).removeClass('upmp-message-info-error').addClass('upmp-message-info-success').show();
	            		file_form.closest('.upmp-private-page-files-tab-content').find('.upmp-private-page-files-list').prepend(data.files_html);
	            	
	            	
	            	
	            	}else if(data.status == 'error'){
	            		msg_container.html(data.msg).removeClass('upmp-message-info-success').addClass('upmp-message-info-error').show();
	            	}
	            },
	            error: function(jqXHR, textStatus, errorThrown)
	            {
	                msg_container.html(err_msg).addClass('upmp-message-info-error').show();
	            }
	        });
		}
	});

	$("body").on("click",".upmp-private-page-file-item-view", function(){
		var file_item = $(this).closest('.upmp-private-page-file-item');
		var file_item_data = file_item.find('.upmp-private-page-file-item-data');
		if(file_item_data.hasClass('upmp-private-page-file-item-data-closed')){
			file_item_data.removeClass('upmp-private-page-file-item-data-closed').addClass('upmp-private-page-file-item-data-open');
		}else if(file_item_data.hasClass('upmp-private-page-file-item-data-open')){
			file_item_data.removeClass('upmp-private-page-file-item-data-open').addClass('upmp-private-page-file-item-data-closed');
		}else{
			file_item_data.removeClass('upmp-private-page-file-item-data-open').addClass('upmp-private-page-file-item-data-closed');
		}
	});

	$("body").on("click",".upmp-private-page-file-item-delete", function(){
		var file_item = $(this).closest('.upmp-private-page-file-item');
		var file_id   = file_item.attr('data-file-id');

		$.post(
            UPMPPage.AdminAjax,
            {
                'action': 'pcb_delete_private_page_file',
                'verify_nonce': UPMPPage.nonce,
                'file_id' : file_id
            },
            function(response){

                if(response.status == 'success'){
                	file_item.closest('.upmp-private-page-files-tab-content').find('.upmp-private-page-files-list-msg').removeClass('upmp-message-info-error').addClass('upmp-message-info-success').html(""+response.msg+"<br/>").show();
                	file_item.remove();

                }else if(response.status == 'error'){
                	file_item.closest('.upmp-private-page-files-tab-content').find('.upmp-private-page-files-list-msg').removeClass('upmp-message-info-success').addClass('upmp-message-info-error').html(""+response.msg+"<br/>").show();

                }      
            },"json"
        );  
	});

	$('.upmp-private-page-files-pagination').click(function(){
		var pagination_button = $(this);
    	var data_page = $(this).attr("data-pagination-page");
    	var files_container = $(this).closest('.upmp-private-page-files-tab-content');

		$.post(
            UPMPPage.AdminAjax,
            {
                'action': 'pcb_load_private_page_paginated_files',
                'verify_nonce': UPMPPage.nonce,
                'data_page' : data_page
            },
            function(response){

                if(response.status == 'success'){
                    if(response.file_pagination_status == '0'){
                    	pagination_button.hide();
                    }else{
                    	pagination_button.attr('data-pagination-page', response.data_page );
                    }

                    files_container.find('.upmp-private-page-files-list').append(response.files_html);
                }      
            },"json"
        );    	
    });

    // Admin functions of private page
    $("body").on("click",".upmp-private-page-admin-messages-single-message-delete", function(){
		var message_block = $(this).closest('.upmp-private-page-messages-single-block');
		var message_id   = message_block.attr('data-message-id');
		var user_id = $(this).attr('data-user-id');

		if(confirm(UPMPPage.Messages.confirmDelete)){
			$.post(
	            UPMPPage.AdminAjax,
	            {
	                'action': 'pcb_delete_private_page_message',
	                'verify_nonce': UPMPPage.nonce,
	                'message_id':   message_id,
	                'user_id' : user_id
	            },
	            function(response){

	                if(response.status == 'success'){
	                	message_block.before('<div class="upmp-private-page-disscussion-tab-msg upmp-message-info-success" id="upmp-private-page-disscussion-delete-error" >' + response.msg + '</div>');
	                	message_block.remove();
	                	setTimeout(function(){ $('#upmp-private-page-disscussion-delete-error').remove(); }, 3000);

	                }else if(response.status == 'error'){	                	
	                	message_block.before('<div class="upmp-private-page-disscussion-tab-msg upmp-message-info-error" id="upmp-private-page-disscussion-delete-error" >' + response.msg + '</div>');
	                	setTimeout(function(){ $('#upmp-private-page-disscussion-delete-error').remove(); }, 3000);
	                }      
	            },"json"
	        );
		}
		  
	});

	$("body").on("click",".upmp-private-page-admin-messages-single-comment-delete", function(){
		var comment_block = $(this).closest('.upmp-private-page-messages-single-block-comment');
		var comment_id   = comment_block.attr('data-message-id');
		var user_id = $(this).attr('data-user-id');

		if(confirm(UPMPPage.Messages.confirmDelete)){
			$.post(
	            UPMPPage.AdminAjax,
	            {
	                'action': 'pcb_delete_private_page_comment',
	                'verify_nonce': UPMPPage.nonce,
	                'comment_id':   comment_id,
	                'user_id' : user_id
	            },
	            function(response){

	                if(response.status == 'success'){
	                	comment_block.before('<div class="upmp-private-page-disscussion-tab-msg upmp-message-info-success" id="upmp-private-page-disscussion-delete-error" >' + response.msg + '</div>');
	                	comment_block.remove();
	                	setTimeout(function(){ $('#upmp-private-page-disscussion-delete-error').remove(); }, 3000);

	                }else if(response.status == 'error'){	                	
	                	comment_block.before('<div class="upmp-private-page-disscussion-tab-msg upmp-message-info-error" id="upmp-private-page-disscussion-delete-error" >' + response.msg + '</div>');
	                	setTimeout(function(){ $('#upmp-private-page-disscussion-delete-error').remove(); }, 3000);
	                }      
	            },"json"
	        );
		}
		  
	});

	$('.upmp-private-page-tab-content').on("click",".upmp-private-page-admin-messages-pagination", function(){
		var pagination_button = $(this);
    	var data_page = $(this).attr("data-pagination-page");
    	var discussion_container = $(this).closest('.upmp-private-page-disscussion-tab-content');
    	var user_id = $(this).attr('data-user-id');

		$.post(
            UPMPPage.AdminAjax,
            {
                'action': 'pcb_load_private_page_paginated_messages',
                'verify_nonce': UPMPPage.nonce,
                'data_page' : data_page,
                'user_id' : user_id
            },
            function(response){

                if(response.status == 'success'){
                    if(response.message_pagination_status == '0'){
                    	pagination_button.hide();
                    }else{
                    	pagination_button.attr('data-pagination-page', response.data_page );
                    }

                    discussion_container.find('.upmp-private-page-messages-list').append(response.messages_html);
                }      
            },"json"
        );    	
    });

	$(".upmp-private-page-admin-file-upload-form").submit(function(e){
	
		e.preventDefault();
		var file_form = $(this);

		file_form.find(".upmp-private-page-file-nonce").val(UPMPPage.nonce);
		var file_name = file_form.find(".upmp-private-page-file-name").val();
		var file_desc = file_form.find(".upmp-private-page-file-desc").val();
		var file = file_form.find(".upmp-private-page-file").val();
		var user_id = $(this).attr('data-user-id');

		var msg_container = file_form.find(".upmp-private-page-files-msg");
		msg_container.removeClass('upmp-message-info-error').removeClass('upmp-message-info-success');

		file_form.find(".upmp-private-page-file-name").removeClass("upmp-error-outline");
		file_form.find(".upmp-private-page-file").removeClass("upmp-error-outline");

		var err = 0;
		var err_msg = '';
		if(file_name == ''){
			err_msg += "" + UPMPPage.Messages.fileNameRequired + "<br/>";
			file_form.find(".upmp-private-page-file-name").addClass("upmp-error-outline");
			err++;
		}

		if(file == ''){
			err_msg += "" + UPMPPage.Messages.fileRequired + "<br/>";
			file_form.find(".upmp-private-page-file").addClass("upmp-error-outline");
			err++;
		}

		if(err != 0){
			msg_container.html(err_msg).addClass('upmp-message-info-error').show();
		}else{

			msg_container.html("").hide();

			var formObj = file_form;
        	var formURL = UPMPPage.AdminAjax+'?action=pcb_save_private_page_files';
        	var formData = new FormData(this);
        	formData.append("user_id", user_id);
        	formData.append("admin_status", "ACTIVE");

        	console.log(formData);

        	jQuery.ajax({
	            url: formURL,
	            type: 'POST',
	            data:  formData,
	            mimeType:"multipart/form-data",
	            contentType: false,
	            cache: false,
	            dataType : "json",
	            processData:false,
	            success: function(data, textStatus, jqXHR)
	            {
	            	if(data.status == 'success'){
	            		msg_container.html(data.msg).removeClass('upmp-message-info-error').addClass('upmp-message-info-success').show();
	            		file_form.closest('.upmp-private-page-files-tab-content').find('.upmp-private-page-files-list').prepend(data.files_html);
	            	
	            	
	            	
	            	}else if(data.status == 'error'){
	            		msg_container.html(data.msg).removeClass('upmp-message-info-success').addClass('upmp-message-info-error').show();
	            	}
	            },
	            error: function(jqXHR, textStatus, errorThrown)
	            {
	                msg_container.html(err_msg).addClass('upmp-message-info-error').show();
	            }
	        });
		}
	});


	$("body").on("click",".upmp-private-page-admin-file-item-delete", function(){
		var file_item = $(this).closest('.upmp-private-page-file-item');
		var file_id   = file_item.attr('data-file-id');
		var user_id = $(this).attr('data-user-id');

		$.post(
            UPMPPage.AdminAjax,
            {
                'action': 'pcb_delete_private_page_file',
                'verify_nonce': UPMPPage.nonce,
                'file_id' : file_id,
                'user_id' : user_id
            },
            function(response){

                if(response.status == 'success'){
                	file_item.closest('.upmp-private-page-files-tab-content').find('.upmp-private-page-files-list-msg').removeClass('upmp-message-info-error').addClass('upmp-message-info-success').html(""+response.msg+"<br/>").show();
                	file_item.remove();

                }else if(response.status == 'error'){
                	file_item.closest('.upmp-private-page-files-tab-content').find('.upmp-private-page-files-list-msg').removeClass('upmp-message-info-success').addClass('upmp-message-info-error').html(""+response.msg+"<br/>").show();

                }      
            },"json"
        );  
	});

	$('.upmp-private-page-admin-files-pagination').click(function(){
		var pagination_button = $(this);
    	var data_page = $(this).attr("data-pagination-page");
    	var files_container = $(this).closest('.upmp-private-page-files-tab-content');
    	var user_id = $(this).attr('data-user-id');

		$.post(
            UPMPPage.AdminAjax,
            {
                'action': 'pcb_load_private_page_paginated_files',
                'verify_nonce': UPMPPage.nonce,
                'data_page' : data_page,
                'user_id' : user_id
            },
            function(response){

                if(response.status == 'success'){
                    if(response.file_pagination_status == '0'){
                    	pagination_button.hide();
                    }else{
                    	pagination_button.attr('data-pagination-page', response.data_page );
                    }

                    files_container.find('.upmp-private-page-files-list').append(response.files_html);
                }      
            },"json"
        );    	
    });

	var url = window.location.href;
    if(url.indexOf('pcb_pp_file_id') != -1){
       jQuery('.upmp-private-page-files-tab').trigger('click');            
    }

    if(url.indexOf('pcb_pp_message_id') != -1){
       jQuery('.upmp-private-page-disscussion-tab').trigger('click');            
    }

    if(url.indexOf('pcb_pp_content') != -1){
       jQuery('.upmp-private-page-content-tab').trigger('click');            
    }
    

});
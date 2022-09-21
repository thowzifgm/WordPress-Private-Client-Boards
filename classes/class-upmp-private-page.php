<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Manage content restriction shortcodes */
class pcb_Private_Page{
    
    public $current_user;
    public $private_content_settings;
    
    /* intialize the settings and shortcodes */
    public function __construct(){
        global $upmp;

        add_action('init', array($this, 'init'));            
      
        add_shortcode('pcb_private_page_pro', array($this,'private_content_page_pro'));
        add_shortcode('pcb_private_page_pro_admin', array($this,'private_content_page_pro_admin'));
            
        add_action( 'wp_ajax_pcb_add_private_page_post_message', array($this, 'add_private_page_post_message'));
        add_action( 'wp_ajax_pcb_add_private_page_comment_message', array($this, 'add_private_page_comment_message'));
        add_action( 'wp_ajax_pcb_load_private_page_paginated_messages', array($this, 'private_page_paginated_messages'));
        
        add_action( 'wp_ajax_pcb_delete_private_page_comment', array($this, 'delete_private_page_comment'));
        add_action( 'wp_ajax_pcb_delete_private_page_message', array($this, 'delete_private_page_message'));
        
        add_action( 'wp_ajax_pcb_save_private_page_files', array($this, 'save_private_page_files'));
        add_action('init', array( $this, 'pcb_private_page_file_download'));
        add_action( 'wp_ajax_pcb_delete_private_page_file', array($this, 'delete_private_page_file'));
        add_action( 'wp_ajax_pcb_load_private_page_paginated_files', array($this, 'private_page_paginated_files'));
        
    }

    public function init(){
        $this->current_user = get_current_user_id(); 

        $this->private_content_settings  = get_option('pcb_options'); 
       
    }
    
    /* Display private content for logged in user */
    public function private_content_page_pro($atts,$content){
        global $upmp,$wpdb,$pcb_private_page_data;
        if(isset($atts) && is_array($atts))
            extract($atts);

        $this->private_content_settings  = get_option('pcb_options');  

        if(is_user_logged_in()){
            $user_id = get_current_user_id();
            $private_page_file_id = isset($_GET['pcb_pp_file_id']) ? (int) $_GET['pcb_pp_file_id'] : 0;
            $private_page_message_id = isset($_GET['pcb_pp_message_id']) ? (int) $_GET['pcb_pp_message_id'] : 0;
            $private_page_comment_id = isset($_GET['pcb_pp_comment_id']) ? (int) $_GET['pcb_pp_comment_id'] : 0;

            $pagination_messages_limit = 5;
            $pagination_files_limit = 5;

            $pcb_private_page_data['current_user_id'] = $user_id;
            $pcb_private_page_data['message_pagination_status'] = false;
            $pcb_private_page_data['message_pagination_page'] = 1;


            if($private_page_message_id == '0'){
                $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE parent_message_id= %d and user_id = %d order by updated_at desc ", 0 , $user_id );
                $result_total = $wpdb->get_results($sql_total);

                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE parent_message_id= %d and user_id = %d order by updated_at desc limit $pagination_messages_limit  ", 0 , $user_id );
                $result = $wpdb->get_results($sql);


            }else{
                $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE id= %d and user_id = %d order by updated_at desc ", $private_page_message_id , $user_id );
                $result_total = $wpdb->get_results($sql_total);

                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE id= %d and user_id = %d order by updated_at desc limit $pagination_messages_limit  ", $private_page_message_id , $user_id );
                $result = $wpdb->get_results($sql);
            }
            

            if(count($result_total) > count($result)){
                $pcb_private_page_data['message_pagination_status'] = true;
            }


            $messages = array();
            $update_msg_status_user_ids = array();
            if($result){
                
                foreach($result as $row){
                    $update_msg_status_user_ids[] = $row->id;

                    $user_display_name = get_user_meta($row->user_id,'first_name',true)." ".get_user_meta($row->user_id,'last_name',true) ;
                    if(trim($user_display_name) == ''){
                        $user_display_name = get_user_meta($row->user_id,'nickname',true) ;                    
                    }

                    if($row->admin_status == 'ACTIVE'){
                        $user_display_name = __('Administrator','upmp');
                    }


                    $sql_comments  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE parent_message_id= %d order by updated_at desc limit 5  ",  $row->id );
                    $result_comments = $wpdb->get_results($sql_comments);

                    $comments = array();
                    if($result_comments){
                        foreach($result_comments as $row_comment){
                            $update_msg_status_user_ids[] = $row_comment->id;

                            $user_comment_display_name = get_user_meta($row_comment->user_id,'first_name',true)." ".get_user_meta($row_comment->user_id,'last_name',true) ;
                            if(trim($user_comment_display_name) == ''){
                                $user_comment_display_name = get_user_meta($row_comment->user_id,'nickname',true) ;                    
                            }

                            if($row_comment->admin_status == 'ACTIVE'){
                                $user_comment_display_name = __('Administrator','upmp');
                            }

                            $comment_date = $row_comment->updated_at;
                     
                            $comment_message_avatar = get_avatar( $row_comment->user_id, 50 );
                            if($row_comment->admin_status == 'ACTIVE'){
                                $comment_message_avatar = get_avatar( pcb_ADMIN_GRAVATAR_EMAIL, 50 );
                            }
                            $comments[] = array('message' => $row_comment->message, 'user_id' => $row_comment->user_id , 'message_id' => $row_comment->id, 'parent_message_id' => $row_comment->parent_message_id,
                                    'user_display_name' => $user_comment_display_name, 'message_date' => $comment_date , 
                                    'message_avatar' => $comment_message_avatar ,'admin_status' => $row_comment->admin_status,
                                    'admin_read_status' => $row_comment->admin_read_status, 'user_read_status' => $row_comment->user_read_status);
                        }
                    }

                    $message_date = $row->updated_at;
                    
                    $message_avatar = get_avatar( $row->user_id, 50 );
                    if($row->admin_status == 'ACTIVE'){
                        $message_avatar = get_avatar( pcb_ADMIN_GRAVATAR_EMAIL, 50 );
                    }
                    $messages[] = array('message' => $row->message,'user_id' => $row->user_id , 'message_id' => $row->id, 'parent_message_id' => $row->parent_message_id,
                                    'user_display_name' => $user_display_name, 'message_date' => $message_date , 
                                    'message_avatar' => $message_avatar , 'current_user_avatar' => get_avatar( $user_id, 50 ),
                                    'comments' =>  $comments, 'admin_status' => $row->admin_status,
                                    'admin_read_status' => $row->admin_read_status, 'user_read_status' => $row->user_read_status );

                }

                $update_msg_status_user_ids = implode(',', $update_msg_status_user_ids);
                if(trim($update_msg_status_user_ids) != ''){
                    $sql_update_msg  = $wpdb->prepare( "UPDATE {$wpdb->prefix}pcb_private_page_messages  set user_read_status='%s' WHERE id IN($update_msg_status_user_ids)  ", 'READ' );
                    $result_update_msg = $wpdb->get_results($sql_update_msg);
                }
            }

            $pcb_private_page_data['messages'] = $messages;

            /* Load Group Files List */
            if($private_page_file_id == '0'){
                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE user_id = %d and status='%s' order by updated_at desc limit $pagination_files_limit  ", $user_id, 'ACTIVE' );
                $files_list = $wpdb->get_results($sql);

                $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE user_id = %d and status='%s' order by updated_at desc ", $user_id, 'ACTIVE' );
                $result_total = $wpdb->get_results($sql_total);
            }else{
                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE id= %d and user_id = %d and status='%s' order by updated_at desc limit $pagination_files_limit  ", $private_page_file_id , $user_id, 'ACTIVE' );
                $files_list = $wpdb->get_results($sql);

                $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE id=%d and user_id = %d and status='%s' order by updated_at desc ", $private_page_file_id , $user_id, 'ACTIVE' );
                $result_total = $wpdb->get_results($sql_total);
            }
            

            $pcb_private_page_data['file_pagination_status'] = false;
            if(count($result_total) > count($files_list)){
                $pcb_private_page_data['file_pagination_status'] = true;
            }

            $pcb_private_page_data['file_pagination_page'] = 1;
            $pcb_private_page_data['files_list'] = $files_list;

            $update_file_status_user_ids = array();
            foreach($files_list as $file_row){
                $update_file_status_user_ids[] = $file_row->id;
            }
            $update_file_status_user_ids = implode(',', $update_file_status_user_ids);
            if(trim($update_file_status_user_ids) != ''){
                $sql_update_files  = $wpdb->prepare( "UPDATE {$wpdb->prefix}pcb_private_page_files  set user_read_status='%s' WHERE id IN($update_file_status_user_ids)  ", 'READ' );
                $result_update_files = $wpdb->get_results($sql_update_files);
            }


            // Private Page Content
            $sql  = $wpdb->prepare( "SELECT content FROM " . $wpdb->prefix . pcb_PRIVATE_CONTENT_TABLE . " WHERE user_id = %d  and tab_id = %d ", $user_id , 0);
            $result = $wpdb->get_results($sql);

            if($result){
                $main_content_html = apply_filters('the_content', stripslashes($result[0]->content));
                $pcb_private_page_data['main_content'] = do_shortcode($main_content_html);
                
                
            }else{
                $pcb_private_page_data['main_content'] = apply_filters('pcb_private_page_empty_message' , __('No content found.','upmp'));
            }

            $pcb_private_page_data['private_page_content_tab_status'] = apply_filters('pcb_private_page_content_tab_status', TRUE, array());
            $pcb_private_page_data['private_page_discussion_tab_status'] = apply_filters('pcb_private_page_discussion_tab_status', TRUE, array());
            $pcb_private_page_data['private_page_files_tab_status'] = apply_filters('pcb_private_page_files_tab_status', TRUE, array());


            ob_start();
            $upmp->template_loader->get_template_part( 'private-page-pro' );
            $display = ob_get_clean();

            return $display;
        }

        return apply_filters('pcb_private_page_empty_message' , __('Please login to view your private page.','upmp'));
    }


    public function add_private_page_post_message(){
        global $wpdb,$post,$upme,$upmp,$pcb_private_page_data;

        $message = isset($_POST['message']) ? wp_kses($_POST['message']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        $admin_status = isset($_POST['admin_status']) ? sanitize_text_field($_POST['admin_status']) : 'INACTIVE';
        $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';

        $message_id = '';
        if($type == 'message'){
            $message_id = '0';
        }        

        $current_user_id = get_current_user_id();
        if(!is_user_logged_in()){
            $user_json_results = array();
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to add message.','upmp') ));exit;
        }

        if($user_id != ''){ // Admin creates the message
            $admin_read_status = 'READ';
            $user_read_status = 'UNREAD';
            $current_user_id = $user_id;
        }else{ // User creates the message
            $admin_read_status = 'UNREAD';
            $user_read_status = 'READ';
        }

        if(check_ajax_referer( 'upmp-private-page', 'verify_nonce',false )){

            $message_date = date("Y-m-d H:i:s");

            $pcb_private_page_messages_table = "{$wpdb->prefix}pcb_private_page_messages";
            $wpdb->insert( 
                $pcb_private_page_messages_table, 
                array( 
                    'message'  => $message, 
                    'user_id' => $current_user_id,
                    'parent_message_id' => $message_id,
                    'updated_at' => $message_date,
                    'admin_status' => $admin_status,
                    'admin_read_status' => $admin_read_status,
                    'user_read_status' => $user_read_status,
                ), 
                array( 
                    '%s', 
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ) ); 

                
            $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE id = %d  ", $wpdb->insert_id );
            $result = $wpdb->get_results($sql,ARRAY_A);

            if(isset($result[0])){
                $current_user_display_name = get_user_meta($current_user_id,'first_name',true)." ".get_user_meta($current_user_id,'last_name',true) ;
                if(trim($current_user_display_name) == ''){
                    $current_user_display_name = get_user_meta($current_user_id,'nickname',true) ;                    
                }

                $message_date = DateTime::createFromFormat('Y-m-d H:i:s', $message_date);
                $message_date = $message_date->format('F j, Y, g:i a'); 

                $mentions_str = '';

                $message_data = $result[0];
                $message_data['current_user_display_name'] = $current_user_display_name;

                $message_data['current_user_avatar']  = get_avatar( $current_user_id , 50 );
                $message_data['current_user_id']  = $current_user_id;

                $user_display_name = get_user_meta($result[0]['user_id'],'first_name',true)." ".get_user_meta($result[0]['user_id'],'last_name',true) ;
                if(trim($user_display_name) == ''){
                    $user_display_name = get_user_meta($result[0]['user_id'],'nickname',true) ;                    
                }

                $message_data['user_display_name'] = $user_display_name;
                $message_data['user_avatar']  = get_avatar($result[0]['user_id'] , 50 );
                if($result[0]['admin_status'] == 'ACTIVE'){
                    $message_data['user_avatar']  = get_avatar(pcb_ADMIN_GRAVATAR_EMAIL , 50 );
                }

                // $message_data['receiver_display_name'] = __('Administrator','upmp');     

                $pcb_private_page_data['message_data'] = $message_data;
                $pcb_private_page_data['comments_data'] = array();

                ob_start();
                $upmp->template_loader->get_template_part( 'private-page-pro-message' );
                $messages_html .= ob_get_clean();
                
                if($admin_status == 'ACTIVE'){
                    $this->send_new_message_notification($current_user_id,$wpdb->insert_id,$wpdb->insert_id,"admin",$result[0]['message']);
                }else{
                    $this->send_new_message_notification($current_user_id,$wpdb->insert_id,$wpdb->insert_id,"user",$result[0]['message']);
                }
                
                $data = array('messages_html' => $messages_html );
                echo json_encode(array('status' => 'success' , 'data' => $data , 'msg' => __('Message posted successfully','upmp') ));exit;
       
       
            }else{
                echo json_encode(array('status' => 'error' , 'msg' => __('Message creation failed.','upmp') ));exit;
            }

            
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Invalid data submission','upmp') ));exit;
        }
    }

    public function add_private_page_comment_message(){
        global $wpdb,$post;

        $message_id = isset($_POST['message_id']) ? (int) $_POST['message_id'] : '';
        $message = isset($_POST['message']) ? wp_kses($_POST['message']) : '';
        $admin_status = isset($_POST['admin_status']) ? sanitize_text_field($_POST['admin_status']) : 'INACTIVE';
        $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';

        $current_user_id = get_current_user_id();
        $check_user_ids = $current_user_id;

        if($user_id != ''){ // Admin creates the message
            $admin_read_status = 'READ';
            $user_read_status = 'UNREAD';
            $current_user_id = $user_id;
        }else{ // User creates the message
            $admin_read_status = 'UNREAD';
            $user_read_status = 'READ';
        }

        if(!is_user_logged_in()){
            $user_json_results = array();
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to add comment.','upmp') ));exit;
        }

        if(check_ajax_referer( 'upmp-private-page', 'verify_nonce',false )){

            
                $message_date = date("Y-m-d H:i:s");

                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE id = %d ", $message_id);
                $result = $wpdb->get_results($sql);

                if($result){
                    $pcb_private_page_messages_table = "{$wpdb->prefix}pcb_private_page_messages";
                    $wpdb->insert( 
                        $pcb_private_page_messages_table, 
                        array( 
                            'message'  => $message, 
                            'user_id' => $current_user_id,
                            'parent_message_id' => $message_id,
                            'updated_at' => $message_date,
                            'admin_status' => $admin_status,
                            'admin_read_status' => $admin_read_status,
                            'user_read_status' => $user_read_status
                        ), 
                        array( 
                            '%s', 
                            '%d',
                            '%d',
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                        ) ); 

                    
                    $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE id = %d  ", $wpdb->insert_id );
                    $result = $wpdb->get_results($sql,ARRAY_A);

                    if(isset($result[0])){
                        $current_user_display_name = get_user_meta($current_user_id,'first_name',true)." ".get_user_meta($current_user_id,'last_name',true) ;
                        if(trim($current_user_display_name) == ''){
                            $current_user_display_name = get_user_meta($current_user_id,'nickname',true) ;                    
                        }

                        $message_display_date = DateTime::createFromFormat('Y-m-d H:i:s', $message_date);
                        $message_display_date = $message_display_date->format('F j, Y, g:i a'); 

                        $avatar = get_avatar( $current_user_id, 50 );
                        if($result[0]['admin_status'] == 'ACTIVE'){
                            $current_user_display_name = __('Administrator','upmp');
                            $avatar = get_avatar( pcb_ADMIN_GRAVATAR_EMAIL , 50 );
                        }

                        if($result[0]['admin_status'] == 'ACTIVE'){
                            $this->send_new_message_notification($current_user_id,$result[0]['parent_message_id'],$wpdb->insert_id,"admin",$result[0]['message']);
                        }else{
                            $this->send_new_message_notification($current_user_id,$result[0]['parent_message_id'],$wpdb->insert_id,"user",$result[0]['message']);
                        }

                        $mentions_str = '';
                        $data = array('message' => wp_unslash($result[0]['message']),  'message_id' => $wpdb->insert_id, 'parent_message_id' => $result[0]['parent_message_id'], 
                            'mentions' => $mentions_str, 'current_user_display_name' => $current_user_display_name,
                            'message_date' => $message_display_date, 'avatar' => $avatar );
                        echo json_encode(array('status' => 'success' , 'data' => $data , 'msg' => __('Message posted successfully','upmp') ));exit;
               
                    }else{
                        echo json_encode(array('status' => 'error' , 'msg' => __('Message creation failed.','upmp') ));exit;
                    }
                }else{
                    echo json_encode(array('status' => 'error' , 'msg' => __('Invalid data submission','upmp') ));exit;
            
                }
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Invalid data submission','upmp') ));exit;
        }
    }

    /* PRIVATE PAGE FUNCTIONS */
    public function private_page_paginated_messages(){        
        global $wpdb,$upmp,$pcb_private_page_data,$pcb_private_page_message_data;

        $pagination_messages_limit = 5;
        $pagination_limit_total = $pagination_messages_limit + 1;

        $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : '0';
        $data_page = isset($_POST['data_page']) ? (int) $_POST['data_page'] : '0';
        $offset = (int) ($pagination_messages_limit * $data_page);

        if(!is_user_logged_in()){
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to view messages.','upmp') ));exit;
        }
        
        if($user_id == '0'){
            $user_id = get_current_user_id();
        }

        $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE user_id = %d and parent_message_id=%d order by updated_at desc LIMIT $pagination_limit_total OFFSET $offset ", $user_id, 0 );
        $result_total = $wpdb->get_results($sql_total);

        $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE user_id = %d and parent_message_id=%d order by updated_at desc LIMIT $pagination_messages_limit OFFSET $offset  ", $user_id, 0 );
        $result = $wpdb->get_results($sql,ARRAY_A );

        $message_pagination_status = '0';
        if(count($result_total) > count($result)){
            $message_pagination_status = '1';
        }

        $messages_html = '';
        $update_msg_status_user_ids = array();
        foreach ($result as $key => $message_data) {
            $update_msg_status_user_ids[] = $message_data['id'];

            $current_user_display_name = get_user_meta($user_id,'first_name',true)." ".get_user_meta($user_id,'last_name',true) ;
            if(trim($current_user_display_name) == ''){
                $current_user_display_name = get_user_meta($user_id,'nickname',true) ;                    
            }

            $message_data['current_user_display_name'] = $current_user_display_name;
            $message_data['current_user_avatar']  = get_avatar( $user_id , 50 );
            $message_data['current_user_id']  = $user_id;

            $user_display_name = get_user_meta($message_data['user_id'],'first_name',true)." ".get_user_meta($message_data['user_id'],'last_name',true) ;
            if(trim($user_display_name) == ''){
                $user_display_name = get_user_meta($message_data['user_id'],'nickname',true) ;                    
            }

            $message_data['user_display_name'] = $user_display_name;
            $message_data['user_avatar']  = get_avatar( $message_data['user_id'] , 50 );

            $pcb_private_page_data['message_data'] = $message_data;

            // Load Comments Data
            $comments_sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE parent_message_id= %d order by updated_at desc ", $message_data['id'] );
            $comments_data = $wpdb->get_results($comments_sql,ARRAY_A );
            $pcb_private_page_data['comments_data'] = $comments_data;

            ob_start();
            $upmp->template_loader->get_template_part( 'private-page-pro-message' );
            $messages_html .= ob_get_clean();

            if($comments_data){
                foreach ($comments_data as $key => $comment) {
                    $update_msg_status_user_ids[] = $comment['id'];
                }
            }
        }

        $update_msg_status_user_ids = implode(',', $update_msg_status_user_ids);
        if($user_id == '0'){
            $sql_update_msg  = $wpdb->prepare( "UPDATE {$wpdb->prefix}pcb_private_page_messages  set user_read_status='%s' WHERE id IN($update_msg_status_user_ids)  ", 'READ' );
        }else{
            $sql_update_msg  = $wpdb->prepare( "UPDATE {$wpdb->prefix}pcb_private_page_messages  set admin_read_status='%s' WHERE id IN($update_msg_status_user_ids)  ", 'READ' );
        
        }

        $update_msg_status_user_ids = trim($update_msg_status_user_ids);
        if(!empty($update_msg_status_user_ids)){
            $result_update_msg = $wpdb->get_results($sql_update_msg);
        }

        echo json_encode(array('status' => 'success', 'message_pagination_status' => $message_pagination_status, 
            'data_page' => ($data_page + 1), 'messages_html' => $messages_html ));
        exit;
        
    }

    public function delete_private_page_comment(){
        global $wpdb,$post;

        $comment_id = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : '';
        $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

        $current_user_id = get_current_user_id();
        if($user_id != '0'){
            $current_user_id = $user_id;
        }

        if(!is_user_logged_in()){
            $user_json_results = array();
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to delete message.','upmp') ));exit;
        }

        if(check_ajax_referer( 'upmp-private-page', 'verify_nonce',false )){

            $delete_status = $wpdb->delete( "{$wpdb->prefix}pcb_private_page_messages" , array( 'id' => $comment_id, 'user_id' => $current_user_id ), array( '%d','%d' ) );
            if($delete_status){

                
                echo json_encode(array('status' => 'success' , 'data' => $data , 'msg' => __('Message deleted successfully','upmp') ));exit;
               
            }else{
                echo json_encode(array('status' => 'error' , 'msg' => __('Invalid data submission','upmp') ));exit;
            }
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Invalid data submission','upmp') ));exit;
        }
    }

    public function delete_private_page_message(){
        global $wpdb,$post;

        $message_id = isset($_POST['message_id']) ? (int) $_POST['message_id'] : '';
        $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

        $current_user_id = get_current_user_id();
        if($user_id != '0'){
            $current_user_id = $user_id;
        }

        if(!is_user_logged_in()){
            $user_json_results = array();
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to delete message.','upmp') ));exit;
        }

        if(check_ajax_referer( 'upmp-private-page', 'verify_nonce',false )){

            $delete_status = $wpdb->delete( "{$wpdb->prefix}pcb_private_page_messages" , array( 'id' => $message_id, 'user_id' => $current_user_id ), array( '%d','%d' ) );
            if($delete_status){

                $delete_comment_status = $wpdb->delete( "{$wpdb->prefix}pcb_private_page_messages", array( 'parent_message_id' => $message_id ), array( '%d' ) );
            
                echo json_encode(array('status' => 'success' , 'data' => $data , 'msg' => __('Message deleted successfully','upmp') ));exit;
               
            }else{
                echo json_encode(array('status' => 'error' , 'msg' => __('Invalid data submission','upmp') ));exit;
            }
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Invalid data submission','upmp') ));exit;
        }
    }


    public function save_private_page_files(){
        global $wpdb,$pcb_private_page_files_data,$upmp;
        $private_page_file_name    = isset($_POST['pcb_private_page_file_name']) ? sanitize_text_field($_POST['pcb_private_page_file_name']) : '';
        $private_page_file_desc    = isset($_POST['pcb_private_page_file_desc']) ? sanitize_text_field($_POST['pcb_private_page_file_desc']) : '';
        $private_page_file_nonce   = isset($_POST['pcb_private_page_file_nonce']) ? sanitize_text_field($_POST['pcb_private_page_file_nonce']) : '';
        $user_id    = isset($_POST['user_id']) ? (int) ($_POST['user_id']) : '0';
        $admin_status = isset($_POST['admin_status']) ? sanitize_text_field($_POST['admin_status']) : 'INACTIVE';
        
        if($user_id == '0'){ // USer creates the file
            $user_id = get_current_user_id();
            $admin_read_status = 'UNREAD';
            $user_read_status = 'READ';
        }else{ // Admin creates the file
            $admin_read_status = 'READ';
            $user_read_status = 'UNREAD';           
            
        }

        if($admin_status == 'ACTIVE' && !current_user_can('manage_options')) {
            echo json_encode(array('status' => 'error', 'msg' => __('Invalid permission.','upmp') ));exit;
        }      

        $allowed = array('image/jpeg','image/gif','image/png','audio/mpeg','audio/mp3','application/vnd.ms-powerpoint','application/mspowerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation','application/vnd.oasis.opendocument.presentation',
            'application/excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.oasis.opendocument.spreadsheet',
            'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain','application/vnd.oasis.opendocument.text',
            'application/pdf','application/force-download','application/x-rar-compressed', 'application/octet-stream','application/zip');

        $allowed_mime_types = apply_filters('pcb_private_page_files_allowed_file_types', $allowed, array('user_id' => $user_id));

        $allowed_exts = array('zip','rar','pdf','doc','docx','ppt','pptx','xls','xlsx','txt','odt','ods','odp','mp3','gif','png','jpeg','jpg');
        $allowed_exts = apply_filters('pcb_private_page_files_allowed_file_exts', $allowed_exts, array('user_id' => $user_id));

        if(!is_user_logged_in()){
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to upload files.','upmp') ));exit;
        }        

        if(check_ajax_referer( 'upmp-private-page', 'pcb_private_page_file_nonce',false )){

                $params['max_size']             = 2 * 1024 * 1024; // 2MB
                $params['allowed_extensions']   = $allowed_mime_types;
                $params['allowed_exts']         = $allowed_exts;

                $result_upload = pcb_process_file_upload($_FILES,$params);

                if(isset($result_upload['status']) && $result_upload['status'] == 'success'){

                    $file_date = date("Y-m-d H:i:s");

                    $pcb_private_page_files_table = "{$wpdb->prefix}pcb_private_page_files";
                    $wpdb->insert( 
                            $pcb_private_page_files_table, 
                            array( 
                                'file_name'         => $private_page_file_name,
                                'description'       => $private_page_file_desc, 
                                'user_id'           => $user_id,
                                'file_path'         => $result_upload['relative_file_path'],
                                'updated_at'        => $file_date,
                                'status'            => 'ACTIVE',
                                'admin_status'      => $admin_status,
                                'admin_read_status' => $admin_read_status,
                                'user_read_status'  => $user_read_status
                            ), 
                            array( 
                                '%s',
                                '%s', 
                                '%d',
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%s'
                            ) );                    
                    
                    $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE id = %d AND status='ACTIVE' order by updated_at desc ", $wpdb->insert_id );
                    $result = $wpdb->get_results($sql,ARRAY_A );

                    $files_html = '';
                    foreach ($result as $key => $file_data) {

                        $pcb_private_page_files_data['current_user_id']  = $user_id;

                        $user_display_name = get_user_meta($file_data['user_id'],'first_name',true)." ".get_user_meta($file_data['user_id'],'last_name',true) ;
                        if(trim($user_display_name) == ''){
                            $user_display_name = get_user_meta($file_data['user_id'],'nickname',true) ;                    
                        }

                        if($file_data['admin_status'] == 'ACTIVE'){
                            $user_display_name = __('Administrator','upmp');
                        }

                        $pcb_private_page_files_data['user_display_name'] = $user_display_name;

                        $pcb_private_page_files_data['file_data'] = $file_data;

                        ob_start();
                        $upmp->template_loader->get_template_part( 'single-private-page-file' );
                        $files_html .= ob_get_clean();
                    }

                    if($admin_status == 'ACTIVE'){
                        $this->send_new_file_notification($user_id,$wpdb->insert_id,"admin",$private_page_file_name,$private_page_file_desc);
                    }else{
                        $this->send_new_file_notification($user_id,$wpdb->insert_id,"user",$private_page_file_name,$private_page_file_desc);

                    }                    

                    echo json_encode(array("status" => "success","file_path" => $result[0]['file_path'],
                        "msg" => $result_upload['msg'], "files_html" => $files_html ));
                    exit;
                }else{
                    echo json_encode(array("status" => "error", "msg" => $result_upload['msg']));
                    exit;
                }


            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Invalid data submission','upmp') ));exit;
        }
    }

    public function delete_private_page_file(){
        global $wpdb,$post;

        $file_id = isset($_POST['file_id']) ? (int) $_POST['file_id'] : '';
        $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : '0';

        $current_user_id = get_current_user_id();
        if($user_id != 0){
            $current_user_id = $user_id;
        }

        if(!is_user_logged_in()){
            $user_json_results = array();
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to delete file.','upmp') ));exit;
        }

        if(check_ajax_referer( 'upmp-private-page', 'verify_nonce',false )){

                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE id = %d and user_id = %d ", $file_id, $current_user_id);
                $result = $wpdb->get_results($sql);

                if($result){
                    
                    
                    $pcb_group_files_table = "{$wpdb->prefix}pcb_private_page_files";
                    $result = $wpdb->update( 
                        $pcb_group_files_table, 
                        array( 
                            'status' => 'INACTIVE'
                        ), 
                        array( 'id' => $file_id ), 
                        array( 
                            '%s'
                        ), 
                        array( '%d' ) 
                    );


                    if($result){
                        echo json_encode(array('status' => 'success' , 'data' => $data , 'msg' => __('File deleted successfully','upmp') ));exit;
               
                    }else{
                        echo json_encode(array('status' => 'error' , 'msg' => __('File deleting failed.','upmp') ));exit;
                    }
                }else{
                    echo json_encode(array('status' => 'error' , 'msg' => __('Invalid data submission','upmp') ));exit;
            
                }
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Invalid data submission','upmp') ));exit;
        }
    }

    public function private_page_paginated_files(){        
        global $wpdb,$upmp,$pcb_private_page_data,$pcb_private_page_files_data;

        $pagination_files_limit = 5;
        $pagination_limit_total = $pagination_files_limit + 1;

        $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : '0';
        $data_page = isset($_POST['data_page']) ? (int) $_POST['data_page'] : '0';
        $offset = (int) ($pagination_files_limit * $data_page);

        if(!is_user_logged_in()){
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to view files.','upmp') ));exit;
        }

        
        if($user_id == '0'){
            $user_id = get_current_user_id();
        }

        $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE user_id = %d AND status='ACTIVE' order by updated_at desc LIMIT $pagination_limit_total OFFSET $offset ", $user_id );
        $result_total = $wpdb->get_results($sql_total);

        $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE user_id = %d AND status='ACTIVE' order by updated_at desc LIMIT $pagination_files_limit OFFSET $offset  ", $user_id );
        $result = $wpdb->get_results($sql,ARRAY_A );

        $file_pagination_status = '0';
        if(count($result_total) > count($result)){
            $file_pagination_status = '1';
        }

        $files_html = '';
        $update_file_status_user_ids = array();
        foreach ($result as $key => $file_data) {
            $update_file_status_user_ids[] = $file_data['id'];
           
            $pcb_private_page_files_data['current_user_id']  = $user_id;

            $user_display_name = get_user_meta($file_data['user_id'],'first_name',true)." ".get_user_meta($file_data['user_id'],'last_name',true) ;
            if(trim($user_display_name) == ''){
                $user_display_name = get_user_meta($file_data['user_id'],'nickname',true) ;                    
            }

            if($file_data['admin_status'] == 'ACTIVE'){
                $user_display_name = __('Administrator','upmp');
            }

            $pcb_private_page_files_data['user_display_name'] = $user_display_name;

            $pcb_private_page_files_data['file_data'] = $file_data;

            ob_start();
            $upmp->template_loader->get_template_part( 'single-private-page-file' );
            $files_html .= ob_get_clean();
        }

        $update_file_status_user_ids = implode(',', $update_file_status_user_ids);
        if($user_id == '0'){
            $sql_update_file  = $wpdb->prepare( "UPDATE {$wpdb->prefix}pcb_private_page_files  set user_read_status='%s' WHERE id IN($update_file_status_user_ids)  ", 'READ' );
        }else{
            $sql_update_file  = $wpdb->prepare( "UPDATE {$wpdb->prefix}pcb_private_page_files  set admin_read_status='%s' WHERE id IN($update_file_status_user_ids)  ", 'READ' );
        
        }

        $update_file_status_user_ids = trim($update_file_status_user_ids);
        if(!empty($update_file_status_user_ids)){
            $result_update_file = $wpdb->get_results($sql_update_file);
        }

        echo json_encode(array('status' => 'success', 'file_pagination_status' => $file_pagination_status, 
            'data_page' => ($data_page + 1), 'files_html' => $files_html ));
        exit;
        
    }

    public function pcb_private_page_file_download(){
        global $wpdb,$upmp;

        if(isset($_GET['pcb_private_page_file_download']) && sanitize_text_field($_GET['pcb_private_page_file_download']) =='yes'){
            $pcb_file_id = (int) $_GET['pcb_file_id'];

            if(!is_user_logged_in()){
                return;
            }

            $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE id = %d AND status='ACTIVE' order by updated_at desc ", $pcb_file_id );
            $result = $wpdb->get_results($sql,ARRAY_A );

            if($result){
                $file_link = $result[0]['file_path'];

                $upload_dir = wp_upload_dir(); 
                $file_dir =  $upload_dir['basedir'].$file_link;
                

                header('Cache-Control: public');
                header('Content-Description: File Transfer');
                header('Content-disposition: attachment;filename='.basename($file_dir));

                $extension = explode('.',$file_link);
                $extension = end($extension);
                $extension = strtolower($extension);

                switch($extension){
                    case 'pdf':
                        header('Content-Type: application/pdf');
                        break;
                    case 'doc':
                        header('Content-Type:application/msword');
                        break;
                    case 'docx':
                        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        break;
                    case 'odt':
                        header('Content-Type: application/vnd.oasis.opendocument.text');
                        break;
                    case 'xls':
                        header('Content-Type: application/excel');
                        break;
                    case 'xlsx':
                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                        break;  
                    case 'ods':
                        header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
                        break;                  
                    case 'ppt':
                        header('Content-Type: application/vnd.ms-powerpoint');
                        break;
                    case 'pptx':
                        header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
                        break;  
                    case 'odp':
                        header('Content-Type: application/vnd.oasis.opendocument.presentation');
                        break;                  
                    case 'txt':
                        header('Content-Type: text/plain');
                        break;
                    case 'mp3':
                        header('Content-Type: audio/mpeg');
                        break;
                }
                
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: '. filesize($file_dir));
                readfile($file_dir);
                exit;
            }


            
        }
    }


    public function private_content_page_pro_admin($atts,$content){
        global $upmp,$wpdb,$pcb_private_page_data;
        if(isset($atts) && is_array($atts))
            extract($atts);

        $this->private_content_settings  = get_option('pcb_options');  

        if(is_user_logged_in() && current_user_can('manage_options')){
            $private_page_file_id = isset($_GET['pcb_pp_file_id']) ? (int) $_GET['pcb_pp_file_id'] : 0;
            $private_page_message_id = isset($_GET['pcb_pp_message_id']) ? (int) $_GET['pcb_pp_message_id'] : 0;
            $private_page_comment_id = isset($_GET['pcb_pp_comment_id']) ? (int) $_GET['pcb_pp_comment_id'] : 0;

            $user_id = $atts['user_id'];
            $current_user_id = get_current_user_id();

            $pagination_messages_limit = 5;
            $pagination_files_limit = 5;

            

            $pcb_private_page_data['current_user_id'] = $user_id;
            $pcb_private_page_data['message_pagination_status'] = false;
            $pcb_private_page_data['message_pagination_page'] = 1;

            if($private_page_message_id == '0'){
                $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE parent_message_id= %d and user_id = %d order by updated_at desc ", 0 , $user_id);
                $result_total = $wpdb->get_results($sql_total);

                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE parent_message_id= %d and user_id = %d order by updated_at desc limit $pagination_messages_limit  ", 0 , $user_id );
                $result = $wpdb->get_results($sql);
            }else{
                $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE id= %d and user_id = %d order by updated_at desc ", $private_page_message_id , $user_id);
                $result_total = $wpdb->get_results($sql_total);

                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE id= %d and user_id = %d order by updated_at desc limit $pagination_messages_limit  ", $private_page_message_id , $user_id );
                $result = $wpdb->get_results($sql);
            }   

            if(count($result_total) > count($result)){
                $pcb_private_page_data['message_pagination_status'] = true;
            }


            $messages = array();
            $update_msg_status_user_ids = array();
            if($result){
                
                foreach($result as $row){
                    $update_msg_status_user_ids[] = $row->id;

                    $user_display_name = get_user_meta($row->user_id,'first_name',true)." ".get_user_meta($row->user_id,'last_name',true) ;
                    if(trim($user_display_name) == ''){
                        $user_display_name = get_user_meta($row->user_id,'nickname',true) ;                    
                    }

                    if($row->admin_status == 'ACTIVE'){
                        $user_display_name = __('Administrator','upmp');
                    }

                    $sql_comments  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_messages WHERE parent_message_id= %d order by updated_at desc limit 5  ",  $row->id );
                    $result_comments = $wpdb->get_results($sql_comments);

                    $comments = array();
                    if($result_comments){
                        foreach($result_comments as $row_comment){
                            $update_msg_status_user_ids[] = $row_comment->id;

                            $user_comment_display_name = get_user_meta($row_comment->user_id,'first_name',true)." ".get_user_meta($row_comment->user_id,'last_name',true) ;
                            if(trim($user_comment_display_name) == ''){
                                $user_comment_display_name = get_user_meta($row_comment->user_id,'nickname',true) ;                    
                            }

                            if($row_comment->admin_status == 'ACTIVE'){
                                $user_comment_display_name = __('Administrator','upmp');
                            }

                            $comment_date = $row_comment->updated_at;
                     
                            $comment_message_avatar = get_avatar( $row_comment->user_id, 50 );
                            if($row_comment->admin_status == 'ACTIVE'){
                                $comment_message_avatar = get_avatar( pcb_ADMIN_GRAVATAR_EMAIL, 50 );
                            }
                            $comments[] = array('message' => $row_comment->message, 'user_id' => $row_comment->user_id , 'message_id' => $row_comment->id, 'parent_message_id' => $row_comment->parent_message_id,
                                    'user_display_name' => $user_comment_display_name, 'message_date' => $comment_date , 
                                    'message_avatar' => $comment_message_avatar , 'admin_status' =>  $row_comment->admin_status, 
                                    'admin_read_status' => $row_comment->admin_read_status , 'user_read_status' => $row_comment->user_read_status );
                        }
                    }

                    $message_date = $row->updated_at;
                    
                    $message_avatar = get_avatar( $row->user_id, 50 );
                    if($row->admin_status == 'ACTIVE'){
                        $message_avatar = get_avatar( pcb_ADMIN_GRAVATAR_EMAIL, 50 );
                    }
                    $messages[] = array('message' => $row->message,'user_id' => $row->user_id , 'message_id' => $row->id, 'parent_message_id' => $row->parent_message_id,
                                    'user_display_name' => $user_display_name, 'message_date' => $message_date , 
                                    'message_avatar' => $message_avatar, 'current_user_avatar' => get_avatar( $current_user_id, 50 ),
                                    'comments' =>  $comments, 'admin_status' =>  $row->admin_status, 
                                    'admin_read_status' => $row->admin_read_status , 'user_read_status' => $row->user_read_status );

                }

                $update_msg_status_user_ids = implode(',', $update_msg_status_user_ids);
                $update_msg_status_user_ids = trim($update_msg_status_user_ids);
                if(!empty($update_msg_status_user_ids)){
                    $sql_update_msg  = $wpdb->prepare( "UPDATE {$wpdb->prefix}pcb_private_page_messages  set admin_read_status='%s' WHERE id IN($update_msg_status_user_ids)  ", 'READ' );
                    $result_update_msg = $wpdb->get_results($sql_update_msg);
                }
            }

            $pcb_private_page_data['messages'] = $messages;

            /* Load Group Files List */
            if($private_page_file_id == '0'){
                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE user_id = %d and status='%s' order by updated_at desc limit $pagination_files_limit  ", $user_id , 'ACTIVE' );
                $files_list = $wpdb->get_results($sql);

                $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE user_id = %d and status='%s' order by updated_at desc ", $user_id ,'ACTIVE' );
                $result_total = $wpdb->get_results($sql_total);
            }else{
                $sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE id = %d and user_id = %d and status='%s' order by updated_at desc limit $pagination_files_limit  ", $private_page_file_id , $user_id, 'ACTIVE' );
                $files_list = $wpdb->get_results($sql);

                $sql_total  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pcb_private_page_files WHERE id = %d and user_id = %d and status='%s' order by updated_at desc ", $private_page_file_id , $user_id, 'ACTIVE' );
                $result_total = $wpdb->get_results($sql_total);
            }

            

            $pcb_private_page_data['file_pagination_status'] = false;
            if(count($result_total) > count($files_list)){
                $pcb_private_page_data['file_pagination_status'] = true;
            }

            $pcb_private_page_data['file_pagination_page'] = 1;
            $pcb_private_page_data['files_list'] = $files_list;

            $update_file_status_user_ids = array();
            foreach($files_list as $file_row){
                $update_file_status_user_ids[] = $file_row->id;
            }
            $update_file_status_user_ids = implode(',', $update_file_status_user_ids);
            
            $update_file_status_user_ids = trim($update_file_status_user_ids);
            if(!empty($update_file_status_user_ids)){                
                $sql_update_files  = $wpdb->prepare( "UPDATE {$wpdb->prefix}pcb_private_page_files  set admin_read_status='%s' WHERE id IN($update_file_status_user_ids)  ", 'READ' );
                $result_update_files = $wpdb->get_results($sql_update_files);
            }

            // Private Page Content
            $sql  = $wpdb->prepare( "SELECT content FROM " . $wpdb->prefix . pcb_PRIVATE_CONTENT_TABLE . " WHERE user_id = %d  and tab_id = %d ", $user_id , 0 );
            $result = $wpdb->get_results($sql);

            if($result){
                $pcb_private_page_data['main_content'] = stripslashes(($result[0]->content));
                $pcb_private_page_data['main_content_display'] = apply_filters('the_content', $result[0]->content); 
            }else{
                $pcb_private_page_data['main_content'] = apply_filters('pcb_private_page_empty_message' , __('No content found.','upmp'));
                $pcb_private_page_data['main_content_display'] = apply_filters('pcb_private_page_empty_message' , __('No content found.','upmp'));
            
            }

            $pcb_private_page_data['private_page_content_tab_status'] = apply_filters('pcb_private_page_content_tab_status', TRUE, array());
            $pcb_private_page_data['private_page_discussion_tab_status'] = apply_filters('pcb_private_page_discussion_tab_status', TRUE, array());
            $pcb_private_page_data['private_page_files_tab_status'] = apply_filters('pcb_private_page_files_tab_status', TRUE, array());

            ob_start();
            $upmp->template_loader->get_template_part( 'private-page-pro-admin' );
            $display = ob_get_clean();
            return $display;
        }

        return apply_filters('pcb_private_page_empty_message' , __('No content found.','upmp'));
    }

    public function send_new_file_notification($user_id,$file_id,$type,$private_page_file_name,$private_page_file_desc){

        $data = isset($this->private_content_settings['private_page_general']) ? $this->private_content_settings['private_page_general'] : array();
        $private_page_id = isset($data['private_page_id']) ? $data['private_page_id'] : '0';


        $subject = apply_filters('pcb_new_private_page_file_notification_subject', __('New File Available','upmp') , array('type' => $type));
        $message = __("Hi","upmp"). "\r\n\r\n";

        if($type == 'user'){
            $admin_notify_emails = isset($data['admin_notify_emails']) ? (array) $data['admin_notify_emails'] : array();
            $email = implode(",", $admin_notify_emails);
            if($admin_notify_emails == ''){
                $email = get_option("admin_email");
            }

            $user_display_name = get_user_meta($user_id,'first_name',true)." ".get_user_meta($user_id,'last_name',true) ;
            if(trim($user_display_name) == ''){
                $user_display_name = get_user_meta($user_id,'nickname',true) ;                    
            }
            
            $file_link = admin_url( 'admin.php?page=upmp-private-user-page&&tab=pcb_section_private_page_user&pcb_pp_file_id='.$file_id.'&pcb_private_page_user='.$user_id );       

        }else if($type == 'admin'){
            $user = new WP_User( $user_id );
            $email = $user->user_email;

            $file_link = get_permalink($private_page_id) . "?pcb_pp_file_id=".$file_id;
        }

        $message .= __('New File is uploaded by ','upmp'). $user_display_name . "\r\n\r\n";
        $message .= __('File Name : ','upmp'). $private_page_file_name . "\r\n";
        $message .= __('File Description : ','upmp'). $private_page_file_desc . "\r\n\r\n";

        $message .= __('Please click the following link to view the file.','upmp'). "\r\n\r\n";
        $message .= $file_link.  "\r\n\r\n";

        $message .= __('Thanks','upmp').  "\r\n";
        $message .= get_bloginfo('name');

        
        $message = apply_filters('pcb_new_private_page_file_notification_message', $message , array('file_id'=> $file_id, 'type' => $type, 'user_id' => $user_id ,
            'file_name' => $private_page_file_name, 'file_description' => $private_page_file_desc, 'file_link' => $file_link));
        wp_mail($email, $subject, $message);
    }

    public function send_new_message_notification($user_id,$parent_message_id,$message_id,$type,$message){

        $data = isset($this->private_content_settings['private_page_general']) ? $this->private_content_settings['private_page_general'] : array();
        $private_page_id = isset($data['private_page_id']) ? $data['private_page_id'] : '0';


        $subject = apply_filters('pcb_new_private_page_message_notification_subject', __('New Message Available','upmp') , array('type' => $type));
        $email_message = __("Hi","upmp"). "\r\n\r\n";

        if($type == 'user'){
            $admin_notify_emails = isset($data['admin_notify_emails']) ? (array) $data['admin_notify_emails'] : array();
            $email = implode(",", $admin_notify_emails);
            if($admin_notify_emails == ''){
                $email = get_option("admin_email");
            }

            $user_display_name = get_user_meta($user_id,'first_name',true)." ".get_user_meta($user_id,'last_name',true) ;
            if(trim($user_display_name) == ''){
                $user_display_name = get_user_meta($user_id,'nickname',true) ;                    
            }
            
            $message_link = admin_url( 'admin.php?page=upmp-private-user-page&tab=pcb_section_private_page_user&pcb_pp_message_id='.$parent_message_id.'&pcb_pp_comment_id='.$message_id.'&pcb_private_page_user='.$user_id );       

        }else if($type == 'admin'){
            $user = new WP_User( $user_id );
            $email = $user->user_email;

            $user_display_name = __('Administrator','upmp');
            $message_link = get_permalink($private_page_id) . "?pcb_pp_message_id=".$parent_message_id."&pcb_pp_comment_id=".$message_id;
        }

        $email_message .= __('New Message is sent by ','upmp'). $user_display_name . "\r\n\r\n";
        $email_message .= __('Message : ','upmp'). $message . "\r\n\r\n";

        $email_message .= __('Please click the following link to view the message.','upmp'). "\r\n\r\n";
        $email_message .= $message_link .  "\r\n\r\n";

        $email_message .= __('Thanks','upmp').  "\r\n";
        $email_message .= get_bloginfo('name');


        

        $email_message = apply_filters('pcb_new_private_page_message_notification_message', $email_message , array('message_id'=> $message_id, 'type' => $type, 'user_id' => $user_id ,
            'parent_message_id' => $parent_message_id, 'message' => $message));
        
        wp_mail($email, $subject, $email_message);
    }

    public function send_new_private_content_notification($user_id){

        $data = isset($this->private_content_settings['private_page_general']) ? $this->private_content_settings['private_page_general'] : array();
        $private_page_id = isset($data['private_page_id']) ? $data['private_page_id'] : '0';

        $subject = apply_filters('pcb_new_private_page_content_notification_subject', __('New Private Content Available','upmp') , array('user_id' => $user_id));
        $email_message = __("Hi","upmp"). "\r\n\r\n";

        $content_link = get_permalink($private_page_id) . "?pcb_pp_content=yes";

        $email_message .= __('You have new content on your private page.','upmp');
        $email_message .= __('Please click the following link to view the updated content.','upmp'). "\r\n\r\n";
        $email_message .= $content_link . "\r\n\r\n";

        $email_message .= __('Thanks','upmp').  "\r\n";
        $email_message .= get_bloginfo('name');

        $user = new WP_User( $user_id );
        $email = $user->user_email;
        
        $email_message = apply_filters('pcb_new_private_page_content_notification_message', $email_message , array('user_id' => $user_id));
        wp_mail($email, $subject, $email_message);
    }

}
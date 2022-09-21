<?php

class pcb_Private_Tabs{

	public function __construct(){
		add_action( 'init',array($this,'register_private_page_tabs'));   
		add_action( 'add_meta_boxes', array($this,'private_page_tabs_meta_box'));
        add_action( 'save_post', array($this,'save_private_page_tabs'), 10, 3 );  

        add_action( 'pcb_private_page_admin_custom_tabs', array($this,'private_page_custom_tabs') );  
        add_action( 'pcb_private_page_admin_custom_tabs_content', array($this,'private_page_custom_tabs_content') );  
    
        add_action( 'pcb_private_page_user_custom_tabs', array($this,'private_page_custom_tabs') ); 
	    add_action( 'pcb_private_page_user_custom_tabs_content', array($this,'private_page_user_custom_tabs_content') );  
        
    }

	public function register_private_page_tabs(){
		register_post_type( pcb_PRIVATE_PAGE_TABS_POST_TYPE,
            array(
                'labels' => array(
                    'name'              => __('Private Portal Tabs','upmp'),
                    'singular_name'     => __('Private Portal Tab','upmp'),
                    'add_new'           => __('Add New','upmp'),
                    'add_new_item'      => __('Add New Private Portal Tab','upmp'),
                    'edit'              => __('Edit','upmp'),
                    'edit_item'         => __('Edit Private Portal Tab','upmp'),
                    'new_item'          => __('New Private Portal Tab','upmp'),
                    'view'              => __('View','upmp'),
                    'view_item'         => __('View Private Portal Tab','upmp'),
                    'search_items'      => __('Search Private Portal Tab','upmp'),
                    'not_found'         => __('No Private Portal Tab found','upmp'),
                    'not_found_in_trash' => __('No Private Portal Tab found in Trash','upmp'),
                ),

                'public' => true,
                'menu_position' => 100,
                'supports' => array( 'title','editor'),
                'has_archive' => false,
                'publicly_queryable'  => true
                
            )
        );

	}

	public function private_page_tabs_meta_box(){

        if(current_user_can('manage_options') || apply_filters('pcb_private_page_tabs_setting_meta_box_visibility',false,array() ) ){
        
            add_meta_box(
                        'upmp-private-page-tabs-general',
                        __( 'Private Portal Tab Settings', 'upmp' ),
                        array($this,'private_page_tab_details'),
                        pcb_PRIVATE_PAGE_TABS_POST_TYPE,
                        'normal',
                        'high'
                    );

            
        }
    }

    public function private_page_tab_details($post, $metabox){
        global $upmp,$private_page_tab_params;   

        $private_page_tab_params['post'] = $post;
        
        ob_start();
        $upmp->template_loader->get_template_part('private-page-tab-meta');    
        $display = ob_get_clean();  
        echo $display;
    }

    public function save_private_page_tabs($post_id, $post, $update){
        global $wpdb;

        if ( pcb_PRIVATE_PAGE_TABS_POST_TYPE != $post->post_type ) {
            return;
        }
        
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_posts', $post_id ) ) {
            return;
        }

        if(isset($_POST)){
            $visibility                         = isset($_POST['pcb_private_tab_visibility']) ? sanitize_text_field($_POST['pcb_private_tab_visibility']) : 'none'; 
            $visibility_roles                   = isset($_POST['pcb_private_tab_visibility_roles']) ? (array) $_POST['pcb_private_tab_visibility_roles'] : array();
            $tab_status                         = isset($_POST['pcb_private_tab_status']) ? sanitize_text_field($_POST['pcb_private_tab_status']) : 'enabled'; 
            $tab_type                           = isset($_POST['pcb_private_tab_type']) ? sanitize_text_field($_POST['pcb_private_tab_type']) : 'content'; 
            $tab_predefined_type                = isset($_POST['pcb_private_tab_predefined_type']) ? sanitize_text_field($_POST['pcb_private_tab_predefined_type']) : '';  

            // $upload_permission_type             = isset($_POST['pcb_private_tab_upload_permission_type']) ? $_POST['pcb_private_tab_upload_permission_type'] : 'none'; 
            // $visibility_upload_roles            = isset($_POST['pcb_private_tab_file_upload_roles']) ? $_POST['pcb_private_tab_file_upload_roles'] : array();
            
            update_post_meta( $post_id , '_pcb_private_tab_visibility' , $visibility);
            update_post_meta( $post_id , '_pcb_private_tab_visibility_roles' , $visibility_roles);
            update_post_meta( $post_id , '_pcb_private_tab_status' , $tab_status);
            update_post_meta( $post_id , '_pcb_private_tab_type' , $tab_type);
            update_post_meta( $post_id , '_pcb_private_tab_predefined_type' , $tab_predefined_type);  
            // update_post_meta( $post_id , '_pcb_private_tab_upload_permission_type' , $upload_permission_type);  
            // update_post_meta( $post_id , '_pcb_private_tab_upload_permission_roles' , $visibility_upload_roles);     

            do_action('pcb_private_tab_after_settings_save',$post_id , array());
        }
        
        
    }

    public function private_page_custom_tabs($params){
        $query = new WP_Query( array( 
            'posts_per_page' => -1,
            'meta_key'   => '_pcb_private_tab_status',
            'meta_value' => 'enabled',
            'post_type' => pcb_PRIVATE_PAGE_TABS_POST_TYPE ,
            'post_status' => 'publish'    ) );

        if ( $query->have_posts() ) {

            while ($query->have_posts()) : $query->the_post();
                $tab_id = get_the_ID();
                $tab_visibility = $this->verify_tab_visibility($tab_id,$params);
                
                if($tab_visibility){
                    echo "<div class='upmp-private-page-tab upmp-private-page-".$tab_id."-tab' 
                    data-tab-id='upmp-private-page-".$tab_id."' >".get_the_title()."</div>";
                }
                
            endwhile;
            wp_reset_query();

        }
    }

    public function private_page_custom_tabs_content($params){
        extract($params['pcb_private_page_params']);

        $query = new WP_Query( array( 
            'posts_per_page' => -1,
            'meta_key'   => '_pcb_private_tab_status',
            'meta_value' => 'enabled',
            'post_type' => pcb_PRIVATE_PAGE_TABS_POST_TYPE ,
            'post_status' => 'publish'    ) );

        if ( $query->have_posts() ) {

            while ($query->have_posts()) : $query->the_post();

                $post_id = get_the_ID();
                $private_tab_type = get_post_meta( $post_id , '_pcb_private_tab_type' , true);
                $private_tab_predefined_type = get_post_meta( $post_id , '_pcb_private_tab_predefined_type' , true);
                
                $tab_id = get_the_ID();
                $tab_visibility = $this->verify_tab_visibility($tab_id,$params);
                
                if($tab_visibility){
                    switch ( $private_tab_type) {
                        case 'content':
                            $this->load_private_content_tab($post_id,$user_id,$params['current_user_id'],'admin');
                            break;

                        case 'predefined':
                            $this->load_private_predefined_tab($post_id,$user_id,$params['current_user_id'],'admin',$private_tab_predefined_type);
                            break;
                    }
                }
                
                
            endwhile;
            wp_reset_query();

        }
    }

    public function private_page_user_custom_tabs_content($params){
        $user_id = get_current_user_id();

        $query = new WP_Query( array( 
            'posts_per_page' => -1,
            'meta_key'   => '_pcb_private_tab_status',
            'meta_value' => 'enabled',
            'post_type' => pcb_PRIVATE_PAGE_TABS_POST_TYPE ,
            'post_status' => 'publish'    ) );

        if ( $query->have_posts() ) {

            while ($query->have_posts()) : $query->the_post();

                $post_id = get_the_ID();
                $private_tab_type = get_post_meta( $post_id , '_pcb_private_tab_type' , true);
                $private_tab_predefined_type = get_post_meta( $post_id , '_pcb_private_tab_predefined_type' , true);
                
                $tab_id = get_the_ID();
                $tab_visibility = $this->verify_tab_visibility($tab_id,$params);
                
                if($tab_visibility){
                    switch ( $private_tab_type) {
                        case 'content':
                            $this->load_private_content_tab($post_id,$user_id,$params['current_user_id'],'user');
                            break;

                        case 'predefined':
                            $this->load_private_predefined_tab($post_id,$user_id,$params['current_user_id'],'user',$private_tab_predefined_type);
                            break;
                    }
                }
                
                
            endwhile;
            wp_reset_query();

        }
    }

    public function load_private_content_tab($post_id,$user_id,$current_user_id,$type){
        global $wpdb,$upmp,$pcb_private_custom_tab_params;
        $sql  = $wpdb->prepare( "SELECT content FROM " . $wpdb->prefix . pcb_PRIVATE_CONTENT_TABLE . " WHERE user_id = %d and tab_id = %d ", $user_id , $post_id );
        $result = $wpdb->get_results($sql);

        $pcb_private_custom_tab_params['tab_id'] = $post_id;
        $pcb_private_custom_tab_params['current_user_id'] = $current_user_id;
        $private_tab_default = get_post($post_id); 
        if($result || $private_tab_default->post_content != ''){
            
             
            if(isset($result[0])){
                $pcb_private_custom_tab_params['main_content'] = stripslashes(($result[0]->content));
            }else{
                $pcb_private_custom_tab_params['main_content'] = '';
            }
            
            $pcb_private_custom_tab_params['main_content_display'] = apply_filters('the_content', $private_tab_default->post_content);
            $pcb_private_custom_tab_params['main_content_display'] = $pcb_private_custom_tab_params['main_content_display'] . "  " . apply_filters('the_content',$pcb_private_custom_tab_params['main_content']);
               
        }else{
            $pcb_private_custom_tab_params['main_content'] = apply_filters('pcb_private_page_empty_message' , __('No content found.','upmp'));
            $pcb_private_custom_tab_params['main_content_display'] = apply_filters('pcb_private_page_empty_message' , __('No content found.','upmp'));
        }

        ob_start();

        if($type == 'admin'){
            $upmp->template_loader->get_template_part( 'private-page-admin-custom-content-tab' );
        
        }else{
            $upmp->template_loader->get_template_part( 'private-page-user-custom-content-tab' );
        
        }
        $display = ob_get_clean();
        echo $display;
    }

    public function load_private_predefined_tab($post_id,$user_id,$current_user_id,$type,$private_tab_predefined_type){
        global $wpdb,$upmp,$pcb_private_custom_tab_params;
        
        $private_tab_default = get_post($post_id); 
        $private_tab_default_content = ($private_tab_default->post_content);

        $pcb_private_custom_tab_params['tab_id'] = $post_id;
        $pcb_private_custom_tab_params['current_user_id'] = $current_user_id;
        $pcb_private_custom_tab_params['main_content'] = stripslashes(($private_tab_default_content));

        switch ($private_tab_predefined_type) {
            case 'upme_profile':
                $pcb_private_custom_tab_params['main_content'] .= " [upme] ";
                break;

            case 'upme_member_list':
                $pcb_private_custom_tab_params['main_content'] .= " [upme group='all' view='compact' users_per_page='10'] ";
                break;
            
            default:
                $addon_predefined_params = array( 'post_id' => $post_id , 'user_id' => $user_id, 'private_tab_predefined_type' => $private_tab_predefined_type);
                $pcb_private_custom_tab_params['main_content'] = apply_filters('pcb_addon_predefined_tab_content', $pcb_private_custom_tab_params['main_content'] , $addon_predefined_params);
                break;
        }

        $pcb_private_custom_tab_params['main_content_display'] = apply_filters('the_content', $private_tab_default_content );
        $pcb_private_custom_tab_params['main_content_display'] .= " ". $pcb_private_custom_tab_params['main_content'] . " ";

        ob_start();

        if($type == 'admin'){
            $upmp->template_loader->get_template_part( 'private-page-admin-custom-predefined-tab' );
        
        }else{
            $upmp->template_loader->get_template_part( 'private-page-user-custom-predefined-tab' );
        
        }
        $display = ob_get_clean();
        echo $display;
    }

    public function verify_tab_visibility($tab_id,$params){
        global $upmp;

        $tab_visibility = get_post_meta( $tab_id , '_pcb_private_tab_visibility' , true );
        $tab_visible_roles = (array) get_post_meta( $tab_id , '_pcb_private_tab_visibility_roles' , true );
            
        switch ($tab_visibility) {
            case 'none':
                return TRUE;
                break;
            
            case 'member':
                if(is_user_logged_in()){
                    return TRUE;
                }else{
                    return FALSE;
                }
                
                break;

            case 'role':
                $current_user_roles = $upmp->roles_capability->get_user_roles_by_id($params['current_user_id']);
                foreach ($current_user_roles as $role) {
                    if(in_array($role, $tab_visible_roles)){
                        return TRUE;
                    }
                }
                return FALSE;
                break;
        }

        return TRUE;
    }
}
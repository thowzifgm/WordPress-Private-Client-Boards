<?php

class pcb_Posts{

	public function __construct(){
		add_action('wp_ajax_pcb_load_published_posts', array($this, 'load_published_posts'));
        add_action('wp_ajax_pcb_load_published_pages', array($this, 'load_published_pages'));
        add_action('wp_ajax_pcb_load_published_cpt', array($this, 'load_published_cpt'));
        
	}

	public function load_published_pages(){
        global $wpdb;
        
        $search_text  = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';

        $post_json_results = array();

        $query = "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_title like '%".$search_text."%' && $wpdb->posts.post_status='publish'  && $wpdb->posts.post_type='page' order by $wpdb->posts.post_date desc limit 20";
        $result = $wpdb->get_results($query);
        if($result){
            foreach($result as $post_row){
                array_push($post_json_results , array('id' => $post_row->ID, 'name' => $post_row->post_title) ) ;
            }
        }           
        
        
        echo json_encode(array('items' => $post_json_results ));exit;
    }

    public function load_published_posts(){
        global $wpdb;
        
        $search_text  = isset($_POST['q']) ? sanitize_text_field( $_POST['q'] ): '';

        $post_json_results = array();

        $query = "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_title like '%".$search_text."%' && $wpdb->posts.post_status='publish'  && $wpdb->posts.post_type='post' order by $wpdb->posts.post_date desc limit 20";
        $result = $wpdb->get_results($query);
        if($result){
            foreach($result as $post_row){
                array_push($post_json_results , array('id' => $post_row->ID, 'name' => $post_row->post_title) ) ;
            }
        }           
        
        
        echo json_encode(array('items' => $post_json_results ));exit;
    }

    public function load_published_cpt(){
    	global $wpdb;
        
        $search_text  = isset($_POST['q']) ? sanitize_text_field( $_POST['q'] ) : '';
        $post_type  = isset($_POST['post_type']) ? sanitize_text_field( $_POST['post_type'] ) : '';

        $post_json_results = array();

        $query = "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_title like '%".$search_text."%' && $wpdb->posts.post_status='publish'  && $wpdb->posts.post_type='".$post_type."' order by $wpdb->posts.post_date desc limit 20";
        $result = $wpdb->get_results($query);
        if($result){
            foreach($result as $post_row){
                array_push($post_json_results , array('id' => $post_row->ID, 'name' => $post_row->post_title) ) ;
            }
        }           
        
        
        echo json_encode(array('items' => $post_json_results ));exit;
    }

    public function get_post_types(){

    	$skipped_types = array('post','page','attachment','revision','nav_menu_item');
    	$allowed_post_types = array();

    	$args = array();
    	$output = 'objects'; 
    	$post_types = get_post_types( $args, $output );
    	foreach ($post_types as $post_type => $post_type_data) {
    		if(!in_array($post_type, $skipped_types)){
    			$allowed_post_types[$post_type] = $post_type_data->label;
    		}
    	}
    	

    	return $allowed_post_types;
    }
}
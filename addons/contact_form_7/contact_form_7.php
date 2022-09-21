<?php

add_filter('pcb_private_tab_predefined_types','pcb_private_tab_predefined_contact_form_7',10,2);
function pcb_private_tab_predefined_contact_form_7($option , $private_tab_type_params ){
	extract($private_tab_type_params);

	$option .= "<option value='contact_form_7' " . selected('contact_form_7',$tab_predefined_type). " >". __('Contact Form 7 - Forms','upmp') ."</option>";
	return $option;		
}

add_filter('pcb_private_tab_predefined_additional_fields','pcb_predefined_additional_fields_contact_form_7',10,2);
function pcb_predefined_additional_fields_contact_form_7($display, $private_tab_predefined_additional_fields){
	extract($private_tab_predefined_additional_fields);
	$post_id = isset($post->ID) ? $post->ID : 0;
	$cf7_form = (int) get_post_meta( $post_id , '_pcb_private_tab_cf7_form' , true );
    
    $display_css = 'display:none';
    if($tab_predefined_type == 'contact_form_7'){
    	$display_css = 'display:block';
    }

	$display .= '<div style="'.$display_css.'" id="pcb_private_tab_predefined_type_cf7_panel" class="pcb_predefined_post_meta_row pcb_post_meta_row"  >
					<div class="pcb_post_meta_row_label"><strong>'.__("Contact Form","upmp").'</strong></div>
					<div class="pcb_post_meta_row_field">
						<select id="pcb_private_tab_predefined_type_contact_form" name="pcb_private_tab_predefined_type_contact_form" class="upmp-select2-setting">
							<option selected value="'.$cf7_form.'" >'.get_the_title($cf7_form).'</option>
						</select>
					</div>
				</div>';

	return $display;
}

add_action('admin_enqueue_scripts', 'pcb_load_cf7_scripts',9);
function pcb_load_cf7_scripts(){

	wp_register_script('pcb_cf7', pcb_PLUGIN_URL . 'addons/contact_form_7/upmp-cf7.js', array('pcb_select2_js'));
    wp_enqueue_script('pcb_cf7');

    $custom_js_strings = array(        
            'AdminAjax' => admin_url('admin-ajax.php'),
    );

    wp_localize_script('pcb_cf7', 'UPMPCF7', $custom_js_strings);
}

add_action('wp_ajax_pcb_load_published_cf7_forms', 'load_published_cf7_forms');
function load_published_cf7_forms(){
	global $wpdb;
        
    $search_text  = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';

    $post_json_results = array();

    $query = "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_title like '%".$search_text."%' && $wpdb->posts.post_status='publish'  && $wpdb->posts.post_type='wpcf7_contact_form' order by $wpdb->posts.post_date desc limit 20";
    $result = $wpdb->get_results($query);
    if($result){
        foreach($result as $post_row){
            array_push($post_json_results , array('id' => $post_row->ID, 'name' => $post_row->post_title) ) ;
        }
    }   
    
    echo json_encode(array('items' => $post_json_results ));exit;
}

add_action('pcb_private_tab_after_settings_save','pcb_private_tab_after_settings_save_cf7',10,2);
function pcb_private_tab_after_settings_save_cf7($post_id , $params){
	extract($params);

	$cf7_form    = isset($_POST['pcb_private_tab_predefined_type_contact_form']) ? (int) $_POST['pcb_private_tab_predefined_type_contact_form'] : 0;
    update_post_meta( $post_id , '_pcb_private_tab_cf7_form' , $cf7_form);
            
}

add_filter('pcb_addon_predefined_tab_content', 'pcb_addon_predefined_tab_cf7_content',10,2);
function pcb_addon_predefined_tab_cf7_content($display, $params){
	extract($params);

	if($private_tab_predefined_type == 'contact_form_7'){
		$cf7_form = get_post_meta( $post_id , '_pcb_private_tab_cf7_form' , true);
		$display = " [contact-form-7 id='".$cf7_form."' ] ";
	}
	return $display; 
}

		
		
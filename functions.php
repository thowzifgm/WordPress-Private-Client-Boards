<?php

if (!function_exists('pcb_add_query_string')) {

    function pcb_add_query_string($link, $query_str) {

        $build_url = $link;

        $query_comp = explode('&', $query_str);

        foreach ($query_comp as $param) {
            $params = explode('=', $param);
            $key = isset($params[0]) ? $params[0] : '';
            $value = isset($params[1]) ? $params[1] : '';
            $build_url = esc_url_raw(add_query_arg($key, $value, $build_url));
        }

        return $build_url;
    }

}

if (!function_exists('pcb_process_file_upload')) {
    function pcb_process_file_upload($array,$params) {

        /* File upload conditions */
        $default['allowed_extensions'] = array("image/gif", "image/jpeg", "image/png");
        $default['allowed_exts'] = array('gif','png','jpeg','jpg');

        // Set default to 500KB
        $default['max_size'] = 512000;
        
        $default['image_height'] = 0;
        $default['image_width']  = 0;
  
        $params = wp_parse_args( $params, $default );

        extract($params);
    
        $errors = '';

        if (isset($_FILES)) {
            foreach ($_FILES as $key => $array) {
                
                extract($array);
                if ($name) {

                    $clean_file = true;

                                   
                    preg_match("/.(".implode("|",$allowed_exts).")$/i",$name, $extstatus_matches);
                    
 
                    if (!in_array($type, $allowed_extensions) ) {
                        $errors = __('The file you have selected for has a file extension that is not allowed. Please choose a different file.','upmp').'<br/>';
                    } elseif ($size > $max_size) {
                        $errors = __('The file you have selected exceeds the maximum allowed file size.', 'upmp').'<br/>';
                    } elseif ($clean_file == false) {
                        $errors = __('The file you selected appears to be corrupt or not a real image file.', 'upmp').'<br/>';
                    } 
                    elseif (count($extstatus_matches) == 0) {
                        
                        $errors = __('The file you have selected has a file extension that is not allowed. Please choose a different file.', 'upmp').'<br/>';
                    } 

                    else {
                        
                        $upload_file_custom_validation_params = array('id'=> $user_id, 'key'=> $key, 'height'=>$image_height, 'width'=> $image_width );
                        $custom_errors = apply_filters('pcb_upload_file_custom_validation',array('status'=>false, 'msg'=>'') ,$upload_file_custom_validation_params);

                        if(!$custom_errors['status']){
                            /* Upload image */
                            // Checking for valid uploads folder
                            if (!( $upload_dir = wp_upload_dir() ))
                                $upload_dir =  false;


                            if ($upload_dir) {
                                $target_path = $upload_dir['basedir'] . "/upmp/";

                                // Checking for upload directory, if not exists then new created.
                                if (!is_dir($target_path))
                                    mkdir($target_path, 0777);

                                $base_name = sanitize_file_name(basename($name));
                                $base_name = preg_replace('/\.(?=.*\.)/', '_', $base_name);

                                $target_path = $target_path . time() . '_' . $base_name;

                                $nice_url = $upload_dir['baseurl'] . "/upmp/";
                                $time_val = time();

                                $relative_file_path = "/upmp/".$time_val . '_' . $base_name;
                                $nice_url = $nice_url . $time_val . '_' . $base_name;
                                move_uploaded_file($tmp_name, $target_path);
                                
                                do_action('pcb_after_move_uploaded_file', array('file_path' => $target_path,
                                                                                        'base_name' => $base_name,
                                                                                        'user_id' => $user_id
                                                                                        ) );
                                
                                return array('status' => 'success', 'relative_file_path' => $relative_file_path, 'file_path' => $nice_url, 'msg' => __('File uploaded successfully.','upmp'));
                            }
                        }else{
                            $errors = $custom_errors['msg'];
                        }
                    }


                }else{
                    $errors = __('Please select a file to upload.','upmp');
                    return array('status' => 'error', 'msg' => $errors);
                }
            }
        }
        return array('status' => 'error', 'msg' => $errors);
    }
}

if (!function_exists('pcb_add_query_string')) {

    function pcb_add_query_string($link, $query_str) {

        $build_url = $link;

        $query_comp = explode('&', $query_str);

        foreach ($query_comp as $param) {
            $params = explode('=', $param);
            $key = isset($params[0]) ? $params[0] : '';
            $value = isset($params[1]) ? $params[1] : '';
            $build_url = esc_url_raw(add_query_arg($key, $value, $build_url));
        }

        return $build_url;
    }

}

if (!function_exists('pcb_email_set_content_type_html')) {
    function pcb_email_set_content_type_html( $content_type ) {
        return 'text/html';
    }
}





if (!function_exists('pcb_current_page_url')) {
    function pcb_current_page_url() {
        $current_page_url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
        $current_page_url .= $_SERVER["REQUEST_URI"];

        $parsed_url = parse_url($current_page_url);
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query     = isset($parsed_url['query']) ? $parsed_url['query'] : '';

        $current_page_url = $scheme.$user.$pass.$host.$port.$path;
        if($query != '')
            $current_page_url = $scheme.$user.$pass.$host.$port.$path."?".$query;

        return $current_page_url;
    }
}








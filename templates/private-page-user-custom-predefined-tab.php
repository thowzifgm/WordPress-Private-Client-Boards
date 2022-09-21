<?php 
	global $pcb_private_custom_tab_params,$pcb_private_page_params;
	extract($pcb_private_custom_tab_params);

    $filtered_main_content = apply_filters('the_content', $main_content); 
?>

<div style="display:none;" class='upmp-private-page-tab-content upmp-private-page-<?php echo $tab_id;?>-tab-content'>
	<?php echo wpautop($filtered_main_content); ?>
</div>
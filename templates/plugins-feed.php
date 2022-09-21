<?php
	global $upmp,$wpexpert_plugins_data;
	extract($wpexpert_plugins_data);

	foreach($plugins as $plugin){ 
            $plugin = (array) $plugin;
            extract($plugin);   
?>

			<div class="wpexpert-plugins-panel-single">
				<div class="wpexpert-plugins-panel-single-image">
					<img src="<?php echo $plugin_image; ?>" />
				</div>
				<div class="wpexpert-plugins-panel-single-title">
				<?php echo $plugin_name; ?>
				</div>
				<div class="wpexpert-plugins-panel-single-desc">
				<?php echo $plugin_desc; ?>		
				</div>
				<a class="wpexpert-plugins-panel-single-more" target="_blank" href="<?php echo $plugin_link; ?>"><?php _e('View More','wpexpert'); ?></a>
			</div>

<?php
	}
?>
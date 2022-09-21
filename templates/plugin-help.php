<?php

$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'getting_started';

$title = sprintf( __( 'Welcome to Private Client Boards %s', 'upmp' ), '' ) ;
$desc = __( 'Thank you for choosing Private Client Boards.','upmp');

?>

<div class="wrap about-wrap">
	<h1><?php echo $title; ?></h1>
	<div class="about-text">
		<?php echo $desc; ?>
		
	</div>
	<div  ><img class='upmp-help-docs-img' src='http://www.thowzif.com/wp-content/uploads/2017/07/custom-tab-2.png' /></div>
	<div><a class='upmp-help-docs' href='http://www.thowzif.com/ultimate-private-member-portal'><?php echo __('View Documentation','upmp'); ?></a></div>
</div>

<div class='wrap'>
	<h2><?php echo get_admin_page_title(); ?></h2>
	<div class='metabox-holder'>
		<div class='postbox'>
			<h3><?php _e('Welcome to Zoho for Wordpress', 'zoho-wp'); ?></h3>
			<div class='inside'><?php _e('Thanks for installing ZohoWP! To get started, visit the OAuth page to connect your Zoho account.', 'zoho-wp'); ?></div>
		</div>
	</div>
	<?php do_action('zohowp_admin_general_content'); ?>
</div>

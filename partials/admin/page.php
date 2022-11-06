<div class='wrap'>
	<h2><?php echo get_admin_page_title(); ?></h2>
	<?php settings_errors(static::SLUG); ?>
	<form method='post' action='options.php'>
		<?php
		settings_fields(static::SLUG);
		do_settings_sections(static::SLUG);
		?>
		<?php submit_button(); ?>
	</form>
</div>

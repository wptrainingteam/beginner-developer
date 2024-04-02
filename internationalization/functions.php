<?php
/**
 * Enqueue theme.js file in the dashboard.
 */
add_action( 'admin_enqueue_scripts', 'internationalization_enqueue_scripts' );
function internationalization_enqueue_scripts() {
	wp_enqueue_script(
		'internationalization-theme-js',
		get_stylesheet_directory_uri() . '/assets/theme.js',
		array(),
		'1.0.0',
		true
	);
}

/**
 * Create a submenu item under the "Appearance" menu.
 */
add_action( 'admin_menu', 'internationalization_add_submenu_page' );
function internationalization_add_submenu_page() {
	add_submenu_page(
		'themes.php',
		'Internationalization',
		'Internationalization',
		'manage_options',
		'internationalization',
		'internationalization_display_page'
	);
}

/**
 * Render the page for the submenu item.
 */
function internationalization_display_page() {
	?>
	<div class="wrap">
		<h1>Internationalization Settings</h1>
		<p>This is a settings page for the Internationalization theme</p>
		<button id="internationalization-settings-button" class="button button-primary">Alert</button>
	</div>
	<?php
}
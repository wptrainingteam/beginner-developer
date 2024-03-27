<?php
/**
 * Plugin Name:     WP Learn Plugin Security
 * Description:     Badly coded Plugin
 * Author:          Jonathan Bossenger
 * Author URI:      https://jonthanbossenger.com
 * Text Domain:     wp-learn-plugin-security
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         WP_Learn_Plugin_Security
 */

/**
 * Update these with the page slugs of your success and error pages
 */
define( 'WPLEARN_SUCCESS_PAGE_SLUG', 'form-success-page' );
define( 'WPLEARN_ERROR_PAGE_SLUG', 'form-error-page' );

/**
 * Setting up some URL constants
 */
define( 'WPLEARN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPLEARN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Set up the required form submissions table
 */
register_activation_hook( __FILE__, 'wp_learn_setup_table' );
function wp_learn_setup_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'form_submissions';

	$sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  name varchar (100) NOT NULL,
	  email varchar (100) NOT NULL,
	  PRIMARY KEY  (id)
	)";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

/**
 * Enqueue Admin assets
 */
add_action( 'admin_enqueue_scripts', 'wp_learn_enqueue_script' );
function wp_learn_enqueue_script() {
	wp_register_script(
		'wp-learn-admin',
		WPLEARN_PLUGIN_URL . 'assets/admin.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);
	wp_enqueue_script( 'wp-learn-admin' );
	wp_localize_script(
		'wp-learn-admin',
		'wp_learn_ajax',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}

/**
 * Enqueue Frontend assets
 */
add_action( 'wp_enqueue_scripts', 'wp_learn_enqueue_script_frontend' );
function wp_learn_enqueue_script_frontend() {
	wp_register_style(
		'wp-learn-style',
		WPLEARN_PLUGIN_URL . 'assets/style.css',
		array(),
		'1.0.0'
	);
	wp_enqueue_style( 'wp-learn-style' );
}

/**
 * Submission Form
 * https://developer.wordpress.org/reference/functions/add_shortcode/
 */
add_shortcode( 'wp_learn_form_shortcode', 'wp_learn_form_shortcode' );
function wp_learn_form_shortcode( $atts ) {
	$atts = shortcode_atts (
		array(
			'class' => 'red',
		),
		$atts
	);
	ob_start();
	?>
	<div id="wp_learn_form" class="<?php echo $atts['class'] ?>">
		<form method="post">
			<input type="hidden" name="wp_learn_form" value="submit">
			<div>
				<label for="email">Name</label>
				<input type="text" id="name" name="name" placeholder="Name">
			</div>
			<div>
				<label for="email">Email address</label>
				<input type="text" id="email" name="email" placeholder="Email address">
			</div>
			<div>
				<input type="submit" id="submit" name="submit" value="Submit">
			</div>
		</form>
	</div>
	<?php
	$form = ob_get_clean();
	return $form;
}

/**
 * Process the form data and redirect
 * https://developer.wordpress.org/reference/hooks/wp/
 */
add_action( 'wp', 'wp_learn_maybe_process_form' );
function wp_learn_maybe_process_form() {
	if (!isset($_POST['wp_learn_form'])){
		return;
	}
	$name = $_POST['name'];
	$email = $_POST['email'];

	global $wpdb;
	$table_name = $wpdb->prefix . 'form_submissions';

	$sql = "INSERT INTO $table_name (name, email) VALUES ('$name', '$email')";
	$result = $wpdb->query($sql);
	if ( 0 < $result ) {
		wp_redirect( WPLEARN_SUCCESS_PAGE_SLUG );
		die();
	}

	wp_redirect( WPLEARN_ERROR_PAGE_SLUG );
	die();
}

/**
 * Create an admin page to show the form submissions
 */
add_action( 'admin_menu', 'wp_learn_submenu', 11 );
function wp_learn_submenu() {
	add_submenu_page(
		'tools.php',
		esc_html__( 'WP Learn Admin Page', 'wp_learn' ),
		esc_html__( 'WP Learn Admin Page', 'wp_learn' ),
		'manage_options',
		'wp_learn_admin',
		'wp_learn_render_admin_page'
	);
}

/**
 * Render the form submissions admin page
 */
function wp_learn_render_admin_page(){
	$submissions = wp_learn_get_form_submissions();
	?>
	<div class="wrap" id="wp_learn_admin">
		<h1>Admin</h1>
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Email</th>
				</tr>
			</thead>
			<?php foreach ($submissions as $submission){ ?>
				<tr>
					<td><?php echo $submission->name?></td>
					<td><?php echo $submission->email?></td>
					<td><a class="delete-submission" data-id="<?php echo $submission->id?>" style="cursor:pointer;">Delete</a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php
}

/**
 * Get all the form submissions
 *
 * @return array|object|null
 */
function wp_learn_get_form_submissions() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'form_submissions';

	$sql     = "SELECT * FROM $table_name";
	$results = $wpdb->get_results( $sql );

	return $results;
}

/**
 * Ajax Hook to delete the form submissions
 */
add_action( 'wp_ajax_delete_form_submission', 'wp_learn_delete_form_submission' );
function wp_learn_delete_form_submission() {
	$id = $_POST['id'];
	global $wpdb;
	$table_name = $wpdb->prefix . 'form_submissions';

	$sql    = "DELETE FROM $table_name WHERE id = $id";
	$result = $wpdb->get_results( $sql );

	return wp_send_json( array( 'result' => $result ) );
}





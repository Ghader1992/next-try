<?php
/**
 * Plugin Name:       Custom Advanced Product Fields PRO
 * Plugin URI:        https://example.com/capf-pro
 * Description:       Adds advanced custom fields to WooCommerce products with conditional logic and pricing options.
 * Version:           1.0.0
 * Author:            Jules (AI Developer)
 * Author URI:        https://example.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       capf-pro
 * Domain Path:       /languages
 * WC requires at least: 3.0
 * WC tested up to: latest
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'CAPF_PRO_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 */
define( 'CAPF_PRO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'CAPF_PRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin base name.
 */
define( 'CAPF_PRO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-capf-activator.php (if we create one, or handle here)
 */
function activate_capf_pro() {
	// Placeholder for activation tasks, e.g., creating custom tables, setting default options.
	// For now, we can ensure WooCommerce is active.
	if ( ! class_exists( 'WooCommerce' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			esc_html__( 'Custom Advanced Product Fields PRO requires WooCommerce to be installed and active.', 'capf-pro' ),
			esc_html__( 'Plugin Activation Error', 'capf-pro' ),
			array( 'back_link' => true )
		);
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-capf-deactivator.php (if we create one, or handle here)
 */
function deactivate_capf_pro() {
	// Placeholder for deactivation tasks, e.g., removing custom tables if a setting is checked, flushing rewrite rules.
}

register_activation_hook( __FILE__, 'activate_capf_pro' );
register_deactivation_hook( __FILE__, 'deactivate_capf_pro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require CAPF_PRO_PLUGIN_DIR . 'includes/class-capf-loader.php';

/**
 * The class responsible for defining all actions that occur in the admin area.
 */
require CAPF_PRO_PLUGIN_DIR . 'admin/class-capf-admin.php';

// Eventually, we'll also need:
// require CAPF_PRO_PLUGIN_DIR . 'public/class-capf-public.php';
// require CAPF_PRO_PLUGIN_DIR . 'includes/class-capf-field-group.php'; // Already created, but might be loaded differently or used by admin/public classes

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_capf_pro() {

	$loader = new CAPF_Loader();

	// Admin hooks
	$plugin_admin = new CAPF_Admin( 'capf-pro', CAPF_PRO_VERSION );

	$loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
	$loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
	$loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	// Public hooks (example, will be properly defined later)
	// $plugin_public = new CAPF_Public( 'capf-pro', CAPF_PRO_VERSION );
	// $loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
	// $loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	$loader->run();

}

// Check if WooCommerce is active before running the plugin
function capf_pro_init() {
	if ( class_exists( 'WooCommerce' ) ) {
		run_capf_pro();
	} else {
		// Add an admin notice if WooCommerce is not active
		add_action( 'admin_notices', 'capf_pro_woocommerce_missing_notice' );
	}
}
add_action( 'plugins_loaded', 'capf_pro_init' );

/**
 * Display an admin notice if WooCommerce is not active.
 */
function capf_pro_woocommerce_missing_notice() {
	?>
	<div class="error">
		<p>
			<?php
			printf(
				/* translators: %s: Plugin name */
				esc_html__( '%s requires WooCommerce to be installed and active. Please install and activate WooCommerce.', 'capf-pro' ),
				'<strong>' . esc_html__( 'Custom Advanced Product Fields PRO', 'capf-pro' ) . '</strong>'
			);
			?>
		</p>
	</div>
	<?php
}

?>

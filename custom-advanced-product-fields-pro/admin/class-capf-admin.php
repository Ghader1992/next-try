<?php
/**
 * CAPF_Admin
 *
 * Handles the admin-facing functionality of the plugin,
 * including the settings pages, metaboxes, and saving data.
 *
 * @package CustomAdvancedProductFieldsPro/Admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * CAPF_Admin class.
 */
class CAPF_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the admin menu for the plugin.
	 *
	 * This function will be called by the CAPF_Loader.
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'woocommerce', // Parent slug
			__( 'Product Custom Fields', 'capf-pro' ), // Page title
			__( 'Product Custom Fields', 'capf-pro' ), // Menu title
			'manage_woocommerce', // Capability
			'capf-product-fields', // Menu slug
			array( $this, 'display_settings_page' ) // Function to display the page
		);
	}

	/**
	 * Render the settings page for the plugin.
	 *
	 * This function will be responsible for displaying the UI
	 * for managing global field groups.
	 */
	public function display_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php esc_html_e( 'Manage your global product field groups here. You will be able to create, edit, and delete field groups that can then be assigned to products.', 'capf-pro' ); ?></p>

			<!-- Field group management UI will go here -->
			<div id="capf-field-groups-container">
				<h2><?php esc_html_e( 'Field Groups', 'capf-pro' ); ?></h2>
				<a href="#" class="button button-primary" id="capf-add-new-group"><?php esc_html_e( 'Add New Field Group', 'capf-pro' ); ?></a>

				<!-- Table or list of existing field groups will be displayed here -->
				<p><?php esc_html_e( 'Existing field groups will be listed here.', 'capf-pro' ); ?></p>
			</div>

		</div>
		<?php
	}

	/**
	 * Enqueue admin-specific stylesheets.
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_styles( $hook ) {
		// Only load on our admin page
		if ( 'woocommerce_page_capf-product-fields' !== $hook ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name . '_admin', CAPF_PRO_PLUGIN_URL . 'assets/css/admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Enqueue admin-specific JavaScript.
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_scripts( $hook ) {
		// Only load on our admin page
		if ( 'woocommerce_page_capf-product-fields' !== $hook ) {
			return;
		}
		wp_enqueue_script( $this->plugin_name . '_admin', CAPF_PRO_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), $this->version, true ); // Changed last param to true to load in footer
	}

}
?>

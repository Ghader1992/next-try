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
				<a href="#" class="button button-primary" id="capf-add-new-group-button"><?php esc_html_e( 'Add New Field Group', 'capf-pro' ); ?></a>

				<div id="capf-add-new-group-form-wrapper" style="display: none; margin-top: 20px; padding: 20px; border: 1px solid #ccd0d4; background-color: #fff;">
					<h3><?php esc_html_e( 'Create New Field Group', 'capf-pro' ); ?></h3>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="capf-new-group-name"><?php esc_html_e( 'Group Name', 'capf-pro' ); ?></label>
							</th>
							<td>
								<input type="text" id="capf-new-group-name" name="capf_new_group_name" class="regular-text" value="" />
								<p class="description"><?php esc_html_e( 'Enter a name for this field group (e.g., "Engraving Options", "Gift Wrapping").', 'capf-pro' ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit">
						<button type="button" id="capf-save-new-group" class="button button-primary"><?php esc_html_e( 'Save Field Group', 'capf-pro' ); ?></button>
						<button type="button" id="capf-cancel-new-group" class="button button-secondary" style="margin-left: 10px;"><?php esc_html_e( 'Cancel', 'capf-pro' ); ?></button>
					</p>
				</div>

				<!-- Table or list of existing field groups will be displayed here -->
				<div id="capf-existing-field-groups" style="margin-top: 30px;">
					<?php
					global $wpdb;
					$table_name_groups = $wpdb->prefix . 'capf_field_groups';
					$field_groups = $wpdb->get_results( "SELECT * FROM {$table_name_groups} ORDER BY name ASC" );

					if ( empty( $field_groups ) ) {
						echo '<p>' . esc_html__( 'No field groups created yet. Click "Add New Field Group" to get started.', 'capf-pro' ) . '</p>';
					} else {
						echo '<ul class="capf-field-groups-list">';
						foreach ( $field_groups as $group ) {
							echo '<li data-group-id="' . esc_attr( $group->id ) . '">';
							echo '<strong>' . esc_html( $group->name ) . '</strong>';
							echo ' (ID: ' . esc_html( $group->id ) . ')';
							// Later: add field count, product count
							echo ' <small><a href="#" class="capf-edit-group">' . esc_html__( 'Edit', 'capf-pro' ) . '</a> | ';
							echo '<a href="#" class="capf-delete-group" style="color:#a00;">' . esc_html__( 'Delete', 'capf-pro' ) . '</a></small>';
							echo '</li>';
						}
						echo '</ul>';
					}
					?>
				</div>
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
		wp_enqueue_script( $this->plugin_name . '_admin', CAPF_PRO_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), $this->version, true );

		// Localize script for AJAX and internationalization
		$localized_data = array(
			'create_group_nonce' => wp_create_nonce( 'capf_create_group_nonce_action' ),
			'i18n' => array(
				'emptyGroupName' => __( 'Group name cannot be empty.', 'capf-pro' ),
				'saving'         => __( 'Saving...', 'capf-pro' ),
				'saveGroup'      => __( 'Save Field Group', 'capf-pro' ),
				'errorPrefix'    => __( 'Error: ', 'capf-pro' ),
				'ajaxError'      => __( 'An unexpected error occurred. Please try again.', 'capf-pro' ),
				'edit'           => __( 'Edit', 'capf-pro' ),
				'delete'         => __( 'Delete', 'capf-pro' ),
			),
		);
		wp_localize_script( $this->plugin_name . '_admin', 'capf_admin_params', $localized_data );
	}

	/**
	 * AJAX handler for creating a new field group.
	 */
	public function ajax_create_field_group() {
		// Verify nonce
		check_ajax_referer( 'capf_create_group_nonce_action', 'security' );

		// Check user capabilities
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to perform this action.', 'capf-pro' ) ) );
		}

		// Sanitize group name
		$group_name = isset( $_POST['group_name'] ) ? sanitize_text_field( wp_unslash( $_POST['group_name'] ) ) : '';

		if ( empty( $group_name ) ) {
			wp_send_json_error( array( 'message' => __( 'Group name cannot be empty.', 'capf-pro' ) ) );
		}

		global $wpdb;
		$table_name_groups = $wpdb->prefix . 'capf_field_groups';

		$result = $wpdb->insert(
			$table_name_groups,
			array(
				'name' => $group_name,
			),
			array(
				'%s', // name
			)
		);

		if ( false === $result ) {
			wp_send_json_error( array( 'message' => __( 'Failed to save the field group to the database.', 'capf-pro' ) . $wpdb->last_error ) );
		}

		$new_group_id = $wpdb->insert_id;

		wp_send_json_success(
			array(
				'group_id'   => $new_group_id,
				'group_name' => $group_name,
				'message'    => __( 'Field group created successfully.', 'capf-pro' ),
			)
		);
	}
}
?>

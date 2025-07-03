<?php
/**
 * CAPF_Install
 *
 * Handles plugin installation tasks, primarily database table creation.
 *
 * @package CustomAdvancedProductFieldsPro/Includes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * CAPF_Install class.
 */
class CAPF_Install {

	/**
	 * Create custom database tables required by the plugin.
	 *
	 * Uses dbDelta to create/update tables.
	 */
	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Table: wp_capf_field_groups
		// Stores information about each group of custom fields.
		// - id: BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY - Unique identifier for the field group.
		// - name: VARCHAR(255) NOT NULL - The name of the field group (e.g., "Customization Options").
		// - created_at: DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP - Timestamp of when the group was created.
		// - updated_at: DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP - Timestamp of when the group was last updated.
		$table_name_groups = $wpdb->prefix . 'capf_field_groups';
		$sql_groups = "CREATE TABLE $table_name_groups (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql_groups );

		// Table: wp_capf_fields
		// Stores individual field details within a group.
		// - id: BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY - Unique identifier for the field.
		// - group_id: BIGINT UNSIGNED NOT NULL - Foreign key referencing id in wp_capf_field_groups.
		// - field_label: VARCHAR(255) NOT NULL - The human-readable label shown to the user.
		// - field_name: VARCHAR(255) NOT NULL - The internal name/key for the field, unique within a group.
		// - field_type: VARCHAR(50) NOT NULL - Type of field (e.g., 'text', 'number', 'checkbox', 'select').
		// - is_required: BOOLEAN NOT NULL DEFAULT 0 - Whether the field is mandatory.
		// - options: TEXT DEFAULT NULL - For 'select', 'checkbox', 'radio' types, stores serialized JSON data for options.
		// - conditional_logic: TEXT DEFAULT NULL - Stores serialized JSON data for show/hide rules.
		// - field_order: INT NOT NULL DEFAULT 0 - To control the display order of fields within a group.
		// - min_value: DECIMAL(10,2) DEFAULT NULL - For 'number' type, minimum allowed value.
		// - max_value: DECIMAL(10,2) DEFAULT NULL - For 'number' type, maximum allowed value.
		// - placeholder: VARCHAR(255) DEFAULT NULL - Placeholder text for text/number fields.
		// - default_value: VARCHAR(255) DEFAULT NULL - Default value for the field.
		// - created_at: DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP - Timestamp of when the field was created.
		// - updated_at: DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP - Timestamp of when the field was last updated.
		$table_name_fields = $wpdb->prefix . 'capf_fields';
		$sql_fields = "CREATE TABLE $table_name_fields (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			group_id BIGINT UNSIGNED NOT NULL,
			field_label VARCHAR(255) NOT NULL,
			field_name VARCHAR(255) NOT NULL,
			field_type VARCHAR(50) NOT NULL,
			is_required BOOLEAN NOT NULL DEFAULT 0,
			options TEXT DEFAULT NULL,
			conditional_logic TEXT DEFAULT NULL,
			field_order INT NOT NULL DEFAULT 0,
			min_value DECIMAL(19,4) DEFAULT NULL, -- Adjusted precision for currency
			max_value DECIMAL(19,4) DEFAULT NULL, -- Adjusted precision for currency
			placeholder VARCHAR(255) DEFAULT NULL,
			default_value TEXT DEFAULT NULL, -- Changed to TEXT for longer default values (e.g. textarea)
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY group_id (group_id),
			UNIQUE KEY unique_field_name_in_group (group_id, field_name)
		) $charset_collate;";
		dbDelta( $sql_fields );

        // Add a version to options table to track db version for future upgrades
        update_option('capf_pro_db_version', CAPF_PRO_VERSION);
	}

    /**
     * Placeholder for potential deactivation tasks like removing tables.
     * For now, tables are not removed on deactivation, only on uninstall (if implemented).
     */
    public static function remove_tables() {
        // global $wpdb;
        // $table_name_fields = $wpdb->prefix . 'capf_fields';
        // $table_name_groups = $wpdb->prefix . 'capf_field_groups';
        // $wpdb->query( "DROP TABLE IF EXISTS $table_name_fields" );
        // $wpdb->query( "DROP TABLE IF EXISTS $table_name_groups" );
        // delete_option('capf_pro_db_version');
    }
}
?>

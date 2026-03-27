<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Amoeba_Dashboard
 * Handles the list view of all created modals.
 */
class Amoeba_Dashboard {


    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    }

    /**
     * Adds the admin menu pages.
     */
    public function add_admin_menu() {
        // Main Menu: Dashboard / List
        add_menu_page(
            __( 'Amoeba Modals', 'custom-modal-creator' ),
            __( 'Amoeba Modals', 'custom-modal-creator' ),
            'manage_options',
            'amoeba-modals',
            array( $this, 'render_dashboard_page' ),
            'dashicons-carrot', // Carrot
        );

        add_submenu_page(
            'amoeba-modals',
            __( 'All Modals', 'custom-modal-creator' ),
            __( 'All Modals', 'custom-modal-creator' ),
            'manage_options',
            'amoeba-modals',
            array( $this, 'render_dashboard_page' )
        );

        add_submenu_page(
            'amoeba-modals',
            __( 'Add New Modal', 'custom-modal-creator' ),
            __( 'Add New', 'custom-modal-creator' ),
            'manage_options',
            'amoeba-settings', // Points to the editor class slug
            null 
        );
    }

    public function render_dashboard_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'amoeba_modals';
        $modals = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC" );

        // Handle deletions
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
            check_admin_referer( 'delete_modal_' . $_GET['id'] );
            $wpdb->delete( $table_name, array( 'id' => intval( $_GET['id'] ) ) );
            echo '<div class="updated"><p>' . __( 'Modal deleted.', 'custom-modal-creator' ) . '</p></div>';
            // Refresh list
            $modals = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC" );
        }

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e( 'Amoeba Modals', 'custom-modal-creator' ); ?></h1>
            <a href="<?php echo admin_url( 'admin.php?page=amoeba-settings' ); ?>" class="page-title-action"><?php _e( 'Add New', 'custom-modal-creator' ); ?></a>
            <hr class="wp-header-end">

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="column-id" style="width: 50px;"><?php _e( 'ID', 'custom-modal-creator' ); ?></th>
                        <th scope="col"><?php _e( 'Title', 'custom-modal-creator' ); ?></th>
                        <th scope="col"><?php _e( 'Shortcode', 'custom-modal-creator' ); ?></th>
                        <th scope="col"><?php _e( 'Color', 'custom-modal-creator' ); ?></th>
                        <th scope="col"><?php _e( 'Actions', 'custom-modal-creator' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $modals ) : ?>
                        <?php foreach ( $modals as $modal ) : ?>
                            <tr>
                                <td><?php echo esc_html( $modal->id ); ?></td>
                                tube
                                <td><strong><a href="<?php echo admin_url( 'admin.php?page=amoeba-settings&id=' . $modal->id ); ?>"><?php echo esc_html( $modal->title ); ?></a></strong></td>
                                <td><code>[amoeba_modal id="<?php echo esc_html( $modal->id ); ?>"]</code></td>
                                <td>
                                    <span style="display:inline-block; width: 20px; height: 20px; background: <?php echo esc_attr( $modal->hex_color ); ?>; border: 1px solid #ccc; vertical-align: middle; margin-right: 5px;"></span>
                                    <?php echo esc_html( $modal->hex_color ); ?>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url( 'admin.php?page=amoeba-settings&id=' . $modal->id ); ?>" class="button button-small"><?php _e( 'Edit', 'custom-modal-creator' ); ?></a>
                                    <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=amoeba-modals&action=delete&id=' . $modal->id ), 'delete_modal_' . $modal->id ); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php _e( 'Are you sure?', 'custom-modal-creator' ); ?>')"><?php _e( 'Delete', 'custom-modal-creator' ); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5"><?php _e( 'No modals found. Create your first one!', 'custom-modal-creator' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

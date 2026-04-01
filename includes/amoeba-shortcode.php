<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Amoeba_Shortcode
 * Handles the [amoeba_modal id="X"] shortcode.
 */
class Amoeba_Shortcode {

    public function __construct() {
        add_shortcode( 'amoeba_modal', array( $this, 'render_shortcode' ) );
    }

    /**
     * Renders the shortcode output.
     */
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'amoeba_modal' );

        $id = intval( $atts['id'] );
        if ( ! $id ) {
            return '';
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'amoeba_modals';
        $modal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );

        if ( ! $modal ) {
            return '';
        }

        // Generate a unique ID for this instance to avoid collisions
        $unique_id = 'amoeba-modal-' . $modal->id . '-' . wp_rand( 1000, 9999 );

        ob_start();
        ?>
        <style>
            #<?php echo esc_attr( $unique_id ); ?>.amoeba-modal-card {
                border-color: <?php echo esc_attr( $modal->hex_color ); ?>;
            }
            <?php echo $modal->custom_css; // Custom CSS from DB ?>
        </style>

        <!-- Container Box -->
        <div id="<?php echo esc_attr( $unique_id ); ?>" class="amoeba-modal-card">
            <?php if ( $modal->picture_url ) : ?>
                <div class="amoeba-trigger-image">
                    <img src="<?php echo esc_url( $modal->picture_url ); ?>" alt="<?php echo esc_attr( $modal->title ); ?>">
                </div>
            <?php endif; ?>
            <h3 class="amoeba-trigger-title"><?php echo esc_html( $modal->title ); ?></h3>
            
            <!-- Actual Trigger Box -->
            <div class="view-cv amoeba-modal-trigger" data-modal-target="<?php echo esc_attr( $unique_id ); ?>-overlay">
                <p><span class="dashicons dashicons-media-document"></span>
                    <?php _e( 'View CV', 'amoeba-modal-creator' ); ?></p>
            </div>
        </div>

        <!-- Modal Overlay -->
        <div id="<?php echo esc_attr( $unique_id ); ?>-overlay" class="amoeba-modal-overlay">
            <div class="amoeba-modal-content">
                <span class="amoeba-modal-close">&times;</span>
                <div class="amoeba-modal-flex-container">
                    <?php if ( $modal->picture_url ) : ?>
                        <div class="amoeba-modal-image">
                            <img src="<?php echo esc_url( $modal->picture_url ); ?>" alt="<?php echo esc_attr( $modal->title ); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="amoeba-modal-text-content">
                        <h2 class="amoeba-modal-title"><?php echo esc_html( $modal->title ); ?></h2>
                        <div class="amoeba-modal-body">
                            <?php echo do_shortcode( wpautop( $modal->content ) ); ?>
                        </div>
                    </div>
                    <?php if ( $modal->linkedin || $modal->twitter ) : ?>
                        <div class="amoeba-modal-socials">
                            <?php if ( $modal->linkedin ) : ?>
                                <a href="<?php echo esc_url( $modal->linkedin ); ?>" target="_blank"><i class="bi bi-linkedin"></i></a>
                            <?php endif; ?>
                            <?php if ( $modal->twitter ) : ?>
                                <a href="<?php echo esc_url( $modal->twitter ); ?>" target="_blank"><i class="bi bi-twitter-x"></i></a>
                            <?php endif; ?>
                        </div>   
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new Amoeba_Shortcode();

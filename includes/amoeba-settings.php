<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Amoeba_Settings
 * Handles the editing and creation of modals in the DB.
 */
class Amoeba_Settings {

    /**
     * Amoeba_Settings constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'handle_save' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_scripts' ) );
    }


    public function enqueue_media_scripts( $hook ) {
        // Only load on our specific settings page
        if ( 'admin_page_amoeba-settings' !== $hook ) {
            return;
        }
        wp_enqueue_media();
    }

    /**
     * Adds a hidden/submenu page for editing.
     */
    public function add_admin_menu() {
        add_submenu_page(
            null,
            __( 'Edit Modal', 'amoeba-modal-creator' ),
            __( 'Edit Modal', 'amoeba-modal-creator' ),
            'manage_options',
            'amoeba-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function handle_save() {
        if ( ! isset( $_POST['amoeba_save_modal'] ) || ! check_admin_referer( 'amoeba_save_modal_action' ) ) {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'amoeba_modals';

        $id = isset( $_POST['modal_id'] ) ? intval( $_POST['modal_id'] ) : 0;
        $data = array(
            'title'       => sanitize_text_field( $_POST['title'] ),
            'content'     => wp_kses_post( $_POST['content'] ),
            'picture_url' => esc_url_raw( $_POST['picture_url'] ),
            'hex_color'   => sanitize_hex_color( $_POST['hex_color'] ),
            'custom_css'  => wp_strip_all_tags( $_POST['custom_css'] ),
        );

        if ( $id > 0 ) {
            $wpdb->update( $table_name, $data, array( 'id' => $id ) );
            $message = 'updated';
        } else {
            $wpdb->insert( $table_name, $data );
            $id = $wpdb->insert_id;
            $message = 'created';
        }

        wp_redirect( admin_url( 'admin.php?page=amoeba-settings&id=' . $id . '&message=' . $message ) );
        exit;
    }

    public function render_settings_page() {
        global $wpdb;
        $id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        $modal = null;

        if ( $id > 0 ) {
            $table_name = $wpdb->prefix . 'amoeba_modals';
            $modal = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );
        }

        $title = $modal ? $modal->title : '';
        $content = $modal ? $modal->content : '';
        $picture_url = $modal ? $modal->picture_url : '';
        $hex_color = $modal ? $modal->hex_color : '#333333';
        $custom_css = $modal ? $modal->custom_css : '';

        ?>
        <div class="wrap">
            <h1><?php echo $id > 0 ? __( 'Edit Modal', 'amoeba-modal-creator' ) : __( 'Add New Modal', 'amoeba-modal-creator' ); ?></h1>
            
            <?php if ( isset( $_GET['message'] ) ) : ?>
                <div class="updated"><p><?php echo $_GET['message'] === 'updated' ? __( 'Modal updated.', 'amoeba-modal-creator' ) : __( 'Modal created.', 'amoeba-modal-creator' ); ?></p></div>
            <?php endif; ?>

            <form method="post" action="">
                <?php wp_nonce_field( 'amoeba_save_modal_action' ); ?>
                <input type="hidden" name="modal_id" value="<?php echo esc_attr( $id ); ?>">
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="title"><?php _e( 'Title', 'amoeba-modal-creator' ); ?></label></th>
                        <td><input name="title" type="text" id="title" value="<?php echo esc_attr( $title ); ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="content"><?php _e( 'Content (HTML)', 'amoeba-modal-creator' ); ?></label></th>
                        <td><textarea name="content" id="content" rows="15" cols="50" class="large-text"><?php echo esc_textarea( $content ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="picture_url"><?php _e( 'Picture URL', 'amoeba-modal-creator' ); ?></label></th>
                        <td>
                            <input name="picture_url" type="text" id="picture_url" value="<?php echo esc_attr( $picture_url ); ?>" class="regular-text">
                            <button type="button" class="button" id="amoeba_upload_btn"><?php _e( 'Select Image', 'amoeba-modal-creator' ); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="hex_color"><?php _e( 'Hex Color', 'amoeba-modal-creator' ); ?></label></th>
                        <td><input name="hex_color" type="text" id="hex_color" value="<?php echo esc_attr( $hex_color ); ?>" class="regular-text" placeholder="#ffffff"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="custom_css"><?php _e( 'Custom CSS', 'amoeba-modal-creator' ); ?></label></th>
                        <td><textarea name="custom_css" id="custom_css" rows="10" cols="50" class="large-text"><?php echo esc_textarea( $custom_css ); ?></textarea></td>
                    </tr>
                    <?php if ( $id > 0 ) : ?>
                    <tr>
                        <th scope="row"><?php _e( 'Shortcode', 'amoeba-modal-creator' ); ?></th>
                        <td><code>[amoeba_modal id="<?php echo esc_attr( $id ); ?>"]</code></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <p class="submit">
                    <input type="submit" name="amoeba_save_modal" id="submit" class="button button-primary" value="<?php _e( 'Save Modal', 'amoeba-modal-creator' ); ?>">
                    <a href="<?php echo admin_url( 'admin.php?page=amoeba-modals' ); ?>" class="button"><?php _e( 'Back to List', 'amoeba-modal-creator' ); ?></a>
                </p>
            </form>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($){
                var file_frame;
                $('#amoeba_upload_btn').on('click', function(e) {
                    e.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( file_frame ) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: '<?php _e( "Select or Upload Image", "amoeba-modal-creator" ); ?>',
                        button: {
                            text: '<?php _e( "Use this image", "amoeba-modal-creator" ); ?>',
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    file_frame.on('select', function() {
                        var attachment = file_frame.state().get('selection').first().toJSON();
                        $('#picture_url').val(attachment.url);
                    });

                    // Finally, open the modal
                    file_frame.open();
                });

                // Initialize CodeMirror for Content (HTML)
                const contentTextarea = document.getElementById('content');
                let contentEditor;
                if (contentTextarea) {
                    contentEditor = CodeMirror.fromTextArea(contentTextarea, {
                        mode: "htmlmixed",
                        lineNumbers: true,
                        theme: "default",
                        autoCloseTags: true,
                        lineWrapping: true
                    });
                }

                // Initialize CodeMirror for Custom CSS
                const cssTextarea = document.getElementById('custom_css');
                let cssEditor;
                if (cssTextarea) {
                    cssEditor = CodeMirror.fromTextArea(cssTextarea, {
                        mode: "css",
                        lineNumbers: true,
                        theme: "default",
                        autoCloseTags: true,
                        lineWrapping: true
                    });
                }

                // Ensure CodeMirror updates the original textareas on form submit
                jQuery('form').on('submit', function() {
                    if (contentEditor) contentEditor.save();
                    if (cssEditor) cssEditor.save();
                });
            });
        </script>

        <?php
    }
}

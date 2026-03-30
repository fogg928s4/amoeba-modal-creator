<?php
if (!defined('ABSPATH')) {
    exit;
}

class Amoeba_Scripts
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    public function enqueue_admin_assets($hook)    {
        ?>

        <script>
            console.log('Admin assets loaded for hook: <?php echo $hook; ?>');
        </script>

        <?php

        if( strpos($hook, 'amoeba-settings') === false && strpos($hook, 'amoeba-dashboard') === false ) {
            return;
        }
        // Enqueue CodeMirror from CDN
        wp_enqueue_style('codemirror-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.12/codemirror.min.css');
        wp_enqueue_script('codemirror-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.12/codemirror.min.js', [], null, true);
        wp_enqueue_script('codemirror-html', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.12/mode/htmlmixed/htmlmixed.min.js', ['codemirror-js'], null, true);
        wp_enqueue_script('codemirror-css-mode', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.12/mode/css/css.min.js', ['codemirror-js'], null, true);
        wp_enqueue_script('codemirror-js-mode', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.12/mode/javascript/javascript.min.js', ['codemirror-js'], null, true);

        wp_enqueue_script('prettier', 'https://cdn.jsdelivr.net/npm/prettier@2.8.8/standalone.js', [], null, true);
        wp_enqueue_script('prettier-html', 'https://cdn.jsdelivr.net/npm/prettier@2.8.8/parser-html.js', ['prettier'], null, true);

        wp_enqueue_style('toastify-css', 'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css', array(), null);

        // Enqueue Admin Scripts & Styles
        wp_enqueue_style('amoeba-admin-css', AMOEBA_MODAL_PLUGIN_DIR . 'assets/admin.css', array(), '1.0.3');

    }

    public function enqueue_frontend_assets()
    {
        // Enqueue your frontend CSS for the plugin.
        wp_enqueue_style('amoeba-frontend-css', plugins_url( 'assets/amoeba-modal-styles.css', dirname( __FILE__ ) ), array(), '2.4.0');
        //wp_enqueue_style('amoeba-frontend-css', plugins_url( 'assets/cv-modals.css', dirname( __FILE__ ) ), array(), '1.0.0');
        wp_enqueue_script('amoeba-frontend-js', plugins_url( 'assets/frontend.js', dirname( __FILE__ ) ), array(), '1.0.4', true);
    }
}

new Amoeba_Scripts();
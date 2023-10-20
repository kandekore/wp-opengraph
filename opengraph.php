<?php
/*
Plugin Name: WP Open Graph Plugin
Description: Add custom OpenGraph tags for Facebook, Twitter, and Instagram.
Version: 1.0
Author: D.Kandekore
*/

// Add settings page
function custom_opengraph_add_settings_page() {
    add_menu_page(
        'OpenGraph Settings',
        'OpenGraph Settings',
        'manage_options',
        'custom-opengraph-settings',
        'custom_opengraph_settings_page'
    );
}
add_action('admin_menu', 'custom_opengraph_add_settings_page');

// Add meta box for posts and pages
function custom_opengraph_add_meta_box() {
    add_meta_box(
        'custom-opengraph-meta-box',
        'OpenGraph Settings',
        'custom_opengraph_meta_box_callback',
        'post', 
        'normal',
        'default'
    );
    add_meta_box(
        'custom-opengraph-meta-box',
        'OpenGraph Settings',
        'custom_opengraph_meta_box_callback',
        'page', 
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'custom_opengraph_add_meta_box');

// Define the settings page
function custom_opengraph_settings_page() {
    if (isset($_POST['opengraph_default'])) {
        update_option('opengraph_default', $_POST['opengraph_default']);
    }
    $default_data = get_option('opengraph_default', array());

    ?>
    <div class="wrap">
        <h2>Custom OpenGraph Settings</h2>
        <form method="post" action="">
            <label for="opengraph_default[title]">Default OpenGraph Title:</label>
            <input type="text" id="opengraph_default[title]" name="opengraph_default[title]" value="<?php echo esc_attr($default_data['title']); ?>" /><br/>

            <label for="opengraph_default[description]">Default OpenGraph Description:</label>
            <textarea id="opengraph_default[description]" name="opengraph_default[description]"><?php echo esc_textarea($default_data['description']); ?></textarea><br/>

            <label for="opengraph_default[image]">Default OpenGraph Image URL:</label>
            <input type="text" id="opengraph_default[image]" name="opengraph_default[image]" value="<?php echo esc_url($default_data['image']); ?>" /><br/>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Callback function for the meta box
function custom_opengraph_meta_box_callback($post) {
    $opengraph_data = get_post_meta($post->ID, 'custom_opengraph_data', true);
    $default_data = get_option('opengraph_default', array());
    $merged_data = wp_parse_args($opengraph_data, $default_data);

    ?>
    <label for="custom_opengraph_data[title]">OpenGraph Title:</label>
    <input type="text" id="custom_opengraph_data[title]" name="custom_opengraph_data[title]" value="<?php echo esc_attr($merged_data['title']); ?>" /><br/>

    <label for="custom_opengraph_data[description]">OpenGraph Description:</label>
    <textarea id="custom_opengraph_data[description]" name="custom_opengraph_data[description]"><?php echo esc_textarea($merged_data['description']); ?></textarea><br/>

    <label for="custom_opengraph_data[image]">OpenGraph Image URL:</label>
    <input type="text" id="custom_opengraph_data[image]" name="custom_opengraph_data[image]" value="<?php echo esc_url($merged_data['image']); ?>" /><br/>
    <?php
}

// Save custom OpenGraph data for posts and pages
function custom_opengraph_save_post_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $opengraph_data = $_POST['custom_opengraph_data'];

    update_post_meta($post_id, 'custom_opengraph_data', $opengraph_data);
}
add_action('save_post', 'custom_opengraph_save_post_meta');

// Output custom or default OpenGraph data in the <head> section
function custom_opengraph_output() {
    if (is_singular()) {
        $opengraph_data = get_post_meta(get_the_ID(), 'custom_opengraph_data', true);
        $default_data = get_option('opengraph_default', array());
        $merged_data = wp_parse_args($opengraph_data, $default_data);

        echo '<meta property="og:title" content="' . esc_attr($merged_data['title']) . '" />';
        echo '<meta property="og:description" content="' . esc_attr($merged_data['description']) . '" />';
        echo '<meta property="og:image" content="' . esc_url($merged_data['image']) . '" />';
    }
}
add_action('wp_head', 'custom_opengraph_output');
?>
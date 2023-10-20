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
        <h2>Default OpenGraph Settings</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Default OpenGraph Title:</th>
                    <td><input type="text" name="opengraph_default[title]" value="<?php echo esc_attr($default_data['title']); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Default OpenGraph Description:</th>
                    <td><textarea name="opengraph_default[description]"><?php echo esc_textarea($default_data['description']); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Default OpenGraph Image URL:</th>
                    <td>
					
					  <input type="text" id="custom_opengraph_image" name="opengraph_default[image]" value="<?php echo esc_url($default_data['image']); ?>" />
                <button id="custom_opengraph_image_button" class="button">Select Image</button>
					</td>
                </tr>
				 <tr><td><?php
        // Display the selected image if an image URL is set
        if (!empty($default_data['image'])) {
            echo '<img src="' . esc_url($default_data['image']) . '" style="max-width: 200px;" />';
        }
		?></td></tr>
            </table>
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
    <table class="form-table">
        <tr>
            <th scope="row">OpenGraph Title:</th>
            <td><input type="text" name="custom_opengraph_data[title]" value="<?php echo esc_attr($merged_data['title']); ?>" /></td>
        </tr>
        <tr>
            <th scope="row">OpenGraph Description:</th>
            <td><textarea name="custom_opengraph_data[description]"><?php echo esc_textarea($merged_data['description']); ?></textarea></td>
        </tr>
        <tr>
            <th scope="row">OpenGraph Image:</th>
            <td>
                <input type="text" id="custom_opengraph_image" name="custom_opengraph_data[image]" value="<?php echo esc_url($merged_data['image']); ?>" readonly />
                <button id="custom_opengraph_image_button" class="button">Select Image</button>
		
            </td>		 
        </tr>
		 <tr><td><?php
        // Display the selected image if an image URL is set
        if (!empty($merged_data['image'])) {
            echo '<img src="' . esc_url($merged_data['image']) . '" style="max-width: 200px;" />';
        }
		?></td></tr>
    </table>
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

// Add JavaScript for media library integration
function custom_opengraph_enqueue_scripts() {
    wp_enqueue_media();

    wp_register_script('custom-opengraph-admin', plugins_url('custom-opengraph-admin.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_script('custom-opengraph-admin');
}
add_action('admin_enqueue_scripts', 'custom_opengraph_enqueue_scripts');

?>

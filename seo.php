<?php
/**
 * Plugin Name: Simple SEO
 * Description: A lightweight SEO plugin for managing title tags, meta descriptions, and keyword analysis.
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add meta boxes for SEO settings
function simple_seo_add_meta_boxes() {
    add_meta_box(
        'simple_seo_meta',
        'SEO Settings',
        'simple_seo_render_meta_box',
        'post',
        'normal',
        'high'
    );

    add_meta_box(
        'simple_seo_meta',
        'SEO Settings',
        'simple_seo_render_meta_box',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'simple_seo_add_meta_boxes');

function simple_seo_render_meta_box($post) {
    // Nonce field for security
    wp_nonce_field('simple_seo_save_meta', 'simple_seo_meta_nonce');

    // Get existing values
    $meta_title = get_post_meta($post->ID, '_simple_seo_meta_title', true);
    $meta_description = get_post_meta($post->ID, '_simple_seo_meta_description', true);
    $focus_keyword = get_post_meta($post->ID, '_simple_seo_focus_keyword', true);

    echo '<label for="simple_seo_meta_title">Meta Title:</label>';
    echo '<input type="text" id="simple_seo_meta_title" name="simple_seo_meta_title" value="' . esc_attr($meta_title) . '" style="width: 100%;" />';
    
    echo '<label for="simple_seo_meta_description">Meta Description:</label>';
    echo '<textarea id="simple_seo_meta_description" name="simple_seo_meta_description" rows="4" style="width: 100%;">' . esc_textarea($meta_description) . '</textarea>';
    
    echo '<label for="simple_seo_focus_keyword">Focus Keyword:</label>';
    echo '<input type="text" id="simple_seo_focus_keyword" name="simple_seo_focus_keyword" value="' . esc_attr($focus_keyword) . '" style="width: 100%;" />';
}

// Save the meta box data
function simple_seo_save_meta($post_id) {
    if (!isset($_POST['simple_seo_meta_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['simple_seo_meta_nonce'], 'simple_seo_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Save the meta title
    if (isset($_POST['simple_seo_meta_title'])) {
        update_post_meta($post_id, '_simple_seo_meta_title', sanitize_text_field($_POST['simple_seo_meta_title']));
    }

    // Save the meta description
    if (isset($_POST['simple_seo_meta_description'])) {
        update_post_meta($post_id, '_simple_seo_meta_description', sanitize_textarea_field($_POST['simple_seo_meta_description']));
    }

    // Save the focus keyword
    if (isset($_POST['simple_seo_focus_keyword'])) {
        update_post_meta($post_id, '_simple_seo_focus_keyword', sanitize_text_field($_POST['simple_seo_focus_keyword']));
    }
}
add_action('save_post', 'simple_seo_save_meta');

// Output the meta tags in the header
function simple_seo_output_meta_tags() {
    if (is_single() || is_page()) {
        global $post;

        $meta_title = get_post_meta($post->ID, '_simple_seo_meta_title', true);
        $meta_description = get_post_meta($post->ID, '_simple_seo_meta_description', true);

        if (!empty($meta_title)) {
            echo '<title>' . esc_html($meta_title) . '</title>';
        }

        if (!empty($meta_description)) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'simple_seo_output_meta_tags');

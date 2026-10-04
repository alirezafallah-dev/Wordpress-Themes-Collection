<?php
/* =========================================================
   LEGAL PAGE - LAST UPDATED FIELD
   ========================================================= */

function theme_add_legal_last_updated_meta_box() {
    add_meta_box(
        'legal_last_updated',
        'آخرین بروزرسانی قوانین',
        'theme_render_legal_last_updated_meta_box',
        'page',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'theme_add_legal_last_updated_meta_box');

function theme_render_legal_last_updated_meta_box($post) {
    wp_nonce_field('theme_save_legal_last_updated', 'theme_legal_last_updated_nonce');

    $value = get_post_meta($post->ID, 'legal_last_updated', true);
    ?>
    <p style="margin:0 0 8px;">
        <label for="legal_last_updated">
            تاریخ آخرین بروزرسانی قوانین
        </label>
    </p>

    <input
        type="text"
        id="legal_last_updated"
        name="legal_last_updated"
        value="<?php echo esc_attr($value); ?>"
        placeholder="۱۴۰۵/۰۵/۲۲"
        style="width:100%;"
        dir="ltr"
    >

    <p style="margin:8px 0 0;color:#666;font-size:12px;">
        این تاریخ را هنگام بروزرسانی واقعی قوانین تغییر دهید.
    </p>
    <?php
}

function theme_save_legal_last_updated($post_id) {
    if (
        !isset($_POST['theme_legal_last_updated_nonce']) ||
        !wp_verify_nonce(
            $_POST['theme_legal_last_updated_nonce'],
            'theme_save_legal_last_updated'
        )
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['legal_last_updated'])) {
        return;
    }

    $value = sanitize_text_field($_POST['legal_last_updated']);

    if ($value === '') {
        delete_post_meta($post_id, 'legal_last_updated');
        return;
    }

    update_post_meta(
        $post_id,
        'legal_last_updated',
        $value
    );
}
add_action('save_post_page', 'theme_save_legal_last_updated');
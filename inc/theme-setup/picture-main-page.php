<?php
add_action('admin_menu', function () {
    add_menu_page(
        'تنظیمات قالب',
        'تنظیمات قالب',
        'manage_options',
        'theme-settings',
        'mytheme_settings_page',
        'dashicons-admin-generic',
        30
    );
});

function mytheme_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('دسترسی غیرمجاز.');
    }
    $image = get_option('home_page_image');
    ?>
    <div class="wrap mytheme-panel">
        <h1>تنظیمات صفحه اصلی</h1>
        <div class="image-uploader">
            <h3>تصویر صفحه اصلی</h3>
            <div class="image-preview">
                <?php if ($image): ?>
                    <img src="<?php echo esc_url($image); ?>" id="preview-image">
                <?php else: ?>
                    <div class="no-image" id="preview-placeholder">تصویری انتخاب نشده</div>
                <?php endif; ?>
            </div>
            <input type="hidden" id="home_image_input" name="home_image" value="<?php echo esc_attr($image); ?>">
            <div class="image-actions">
                <button class="button button-primary" id="upload-btn">
                    <?php echo $image ? 'تغییر تصویر' : 'انتخاب تصویر'; ?>
                </button>
                <?php if ($image): ?>
                    <button class="button button-secondary" id="remove-btn">حذف تصویر</button>
                <?php endif; ?>
            </div>
            <button class="button button-success" id="save-btn">ذخیره</button>
        </div>
    </div>
    <?php
}

add_action('wp_ajax_save_home_image', function () {
    // Checking capability
    if (!current_user_can('manage_options')) {
        wp_send_json_error('دسترسی ندارید', 403);
    }
    
    // Nonce verification (fixing CSRF vulnerability)
    if (!isset($_POST['nonce']) || !wp_verify_nonce(
        sanitize_text_field(wp_unslash($_POST['nonce'])),
        'save_home_image_nonce'
    )) {
        wp_send_json_error('درخواست نامعتبر است.', 403);
    }
    
    $image = isset($_POST['image']) ? esc_url_raw(wp_unslash($_POST['image'])) : '';
    update_option('home_page_image', $image);
    wp_send_json_success('ذخیره شد');
});
<?php
/**
 * =========================================================
 * Admin - Consultation Types (Professional Version)
 * =========================================================
 */

add_action('admin_menu', 'lawyer_consultation_types_admin_menu');
add_action('admin_init', 'lawyer_consultation_types_handle_actions');

/**
 * =========================================================
 * Admin Menu
 * =========================================================
 */
function lawyer_consultation_types_admin_menu() {
    add_menu_page(
        'انواع مشاوره',
        'انواع مشاوره',
        'manage_options',
        'lawyer-consultation-types',
        'lawyer_consultation_types_page',
        'dashicons-calendar-alt',
        30
    );
}

/**
 * =========================================================
 * Table Name Helper
 * =========================================================
 */
function lawyer_consultation_types_table() {
    global $wpdb;
    return $wpdb->prefix . 'lawyer_consultation_types';
}

/**
 * =========================================================
 * Admin URL Helper
 * =========================================================
 */
function lawyer_consultation_types_admin_url($args = []) {
    return add_query_arg(
        array_merge(['page' => 'lawyer-consultation-types'], $args),
        admin_url('admin.php')
    );
}

/**
 * =========================================================
 * Handle Admin Actions (Add/Update/Delete)
 * =========================================================
 */
function lawyer_consultation_types_handle_actions() {
    if (!is_admin() || !current_user_can('manage_options')) return;
    if (!isset($_GET['page']) || $_GET['page'] !== 'lawyer-consultation-types') return;

    global $wpdb;
    $table_name = lawyer_consultation_types_table();

    // ============ DELETE ============
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
        $delete_id = absint($_GET['id']);
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';

        if (!$delete_id) wp_die('شناسه نامعتبر است.');
        if (!wp_verify_nonce($nonce, 'delete_consultation_type_' . $delete_id)) wp_die('درخواست نامعتبر است.');

        $wpdb->delete($table_name, ['id' => $delete_id], ['%d']);
        wp_safe_redirect(lawyer_consultation_types_admin_url(['deleted' => 1]));
        exit;
    }

    // ============ UPDATE ============
    if (isset($_POST['lawyer_update_consultation_type'])) {
        $nonce = isset($_POST['lawyer_edit_consultation_nonce']) ? sanitize_text_field(wp_unslash($_POST['lawyer_edit_consultation_nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'lawyer_edit_consultation_type')) wp_die('درخواست نامعتبر است.');

        $edit_id           = absint($_POST['edit_id']);
        $title             = sanitize_text_field(wp_unslash($_POST['title']));
        $description       = sanitize_textarea_field(wp_unslash($_POST['description']));
        $consultation_type = sanitize_key(wp_unslash($_POST['consultation_type']));
        $duration          = absint($_POST['duration']);
        $price             = floatval($_POST['price']);
        $status            = sanitize_key(wp_unslash($_POST['status']));

        if (!$edit_id || empty($title) || $duration <= 0 || $price < 0 || !in_array($consultation_type, ['online', 'offline'], true) || !in_array($status, ['active', 'inactive'], true)) {
            wp_safe_redirect(lawyer_consultation_types_admin_url(['update_error' => 1]));
            exit;
        }

        $wpdb->update(
            $table_name,
            [
                'title'             => $title,
                'description'       => $description,
                'consultation_type' => $consultation_type,
                'duration'          => $duration,
                'price'             => $price,
                'status'            => $status,
            ],
            ['id' => $edit_id],
            ['%s', '%s', '%s', '%d', '%f', '%s'],
            ['%d']
        );

        wp_safe_redirect(lawyer_consultation_types_admin_url(['updated' => 1]));
        exit;
    }

    // ============ ADD ============
    if (isset($_POST['lawyer_add_consultation_type'])) {
        $nonce = isset($_POST['lawyer_consultation_nonce']) ? sanitize_text_field(wp_unslash($_POST['lawyer_consultation_nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'lawyer_add_consultation_type')) wp_die('درخواست نامعتبر است.');

        $title             = sanitize_text_field(wp_unslash($_POST['title']));
        $description       = sanitize_textarea_field(wp_unslash($_POST['description']));
        $consultation_type = sanitize_key(wp_unslash($_POST['consultation_type']));
        $duration          = absint($_POST['duration']);
        $price             = floatval($_POST['price']);

        if (empty($title) || $duration <= 0 || $price < 0 || !in_array($consultation_type, ['online', 'offline'], true)) {
            wp_safe_redirect(lawyer_consultation_types_admin_url(['add_error' => 1]));
            exit;
        }

        $wpdb->insert(
            $table_name,
            [
                'title'             => $title,
                'description'       => $description,
                'consultation_type' => $consultation_type,
                'duration'          => $duration,
                'price'             => $price,
                'status'            => 'active',
            ],
            ['%s', '%s', '%s', '%d', '%f', '%s']
        );

        wp_safe_redirect(lawyer_consultation_types_admin_url(['added' => 1]));
        exit;
    }
}

/**
 * =========================================================
 * Admin Page (Professional UI/UX)
 * =========================================================
 */
function lawyer_consultation_types_page() {
    global $wpdb;
    if (!current_user_can('manage_options')) wp_die('دسترسی غیرمجاز.');

    $table_name    = lawyer_consultation_types_table();
    $edit_type     = null;
    $edit_id       = 0;
    $active_tab    = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'add';

    // Get edit data if needed
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
        $edit_id    = absint($_GET['id']);
        $active_tab = 'edit';
        $edit_type  = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $edit_id));
        if (!$edit_type) wp_die('نوع مشاوره پیدا نشد.');
    }

    // Get consultation types for listing
    $filter_type   = isset($_GET['filter_type']) ? sanitize_key($_GET['filter_type']) : '';
    $where_clause  = "1=1";
    if ($filter_type && in_array($filter_type, ['online', 'offline'])) {
        $where_clause .= $wpdb->prepare(" AND consultation_type = %s", $filter_type);
    }
    $consultation_types = $wpdb->get_results("SELECT * FROM {$table_name} WHERE {$where_clause} ORDER BY consultation_type ASC, id DESC");

    // Stats
    $online_count  = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE consultation_type = 'online'");
    $offline_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE consultation_type = 'offline'");
    ?>

    <style>
        .lawyer-wrap { margin: 20px 20px 20px 0; font-family: Tahoma, sans-serif; }
        .lawyer-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .lawyer-header h1 { font-size: 24px; font-weight: 600; color: #1d2327; margin: 0; }

        .lawyer-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
        .lawyer-stat-card { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; padding: 20px; display: flex; align-items: center; gap: 15px; transition: all 0.2s; }
        .lawyer-stat-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .lawyer-stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .icon-online { background: #e6f6e6; color: #007117; }
        .icon-offline { background: #e5e5ff; color: #0a4b78; }
        .icon-total { background: #fcf0e3; color: #9a6700; }
        .lawyer-stat-info h3 { margin: 0 0 4px; font-size: 28px; font-weight: 700; color: #1d2327; }
        .lawyer-stat-info p { margin: 0; font-size: 13px; color: #646970; }

        .lawyer-tabs { display: flex; gap: 0; background: #fff; border: 1px solid #c3c4c7; border-radius: 8px 8px 0 0; border-bottom: none; overflow: hidden; }
        .lawyer-tab { padding: 14px 24px; background: #f6f7f7; cursor: pointer; font-size: 14px; font-weight: 600; color: #646970; transition: all 0.2s; border-right: 1px solid #c3c4c7; text-decoration: none; display: inline-block; }
        .lawyer-tab:last-child { border-right: none; }
        .lawyer-tab.active { background: #fff; color: #1d2327; box-shadow: inset 0 -3px 0 #2271b1; }
        .lawyer-tab:hover:not(.active) { background: #f0f0f1; }

        .lawyer-panel { background: #fff; border: 1px solid #c3c4c7; border-radius: 0 0 8px 8px; padding: 25px; }
        .lawyer-panel h2 { margin-top: 0; padding-bottom: 15px; border-bottom: 1px solid #f0f0f1; font-size: 18px; }

        .lawyer-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .lawyer-form-group { margin-bottom: 20px; }
        .lawyer-form-group.full { grid-column: 1 / -1; }
        .lawyer-form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #1d2327; }
        .lawyer-form-group .hint { display: block; margin-top: 4px; font-size: 12px; color: #646970; }
        .lawyer-form-group input, .lawyer-form-group select, .lawyer-form-group textarea { width: 100%; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        .lawyer-form-group input:focus, .lawyer-form-group select:focus { outline: none; border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }

        .consultation-type-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .consultation-type-option { position: relative; cursor: pointer; }
        .consultation-type-option input[type="radio"] { position: absolute; opacity: 0; }
        .consultation-type-option .type-box { display: flex; flex-direction: column; align-items: center; padding: 18px; border: 2px solid #c3c4c7; border-radius: 8px; text-align: center; transition: all 0.2s; background: #fff; }
        .consultation-type-option input:checked + .type-box { border-color: #2271b1; background: #f0f6fc; box-shadow: 0 0 0 1px #2271b1; }
        .consultation-type-option .type-box:hover { border-color: #2271b1; }
        .consultation-type-option .type-icon { font-size: 32px; margin-bottom: 8px; }
        .consultation-type-option .type-label { font-weight: 600; font-size: 14px; color: #1d2327; }
        .consultation-type-option .type-desc { font-size: 12px; color: #646970; margin-top: 4px; }

        .lawyer-btn { display: inline-block; padding: 10px 20px; border-radius: 4px; font-size: 14px; font-weight: 600; cursor: pointer; border: 1px solid transparent; text-decoration: none; transition: all 0.2s; }
        .lawyer-btn-primary { background: #2271b1; color: #fff; border-color: #2271b1; }
        .lawyer-btn-primary:hover { background: #135e96; border-color: #135e96; color: #fff; }
        .lawyer-btn-secondary { background: #fff; color: #1d2327; border-color: #8c8f94; }
        .lawyer-btn-secondary:hover { background: #f6f7f7; color: #1d2327; }
        .lawyer-form-actions { margin-top: 25px; padding-top: 20px; border-top: 1px solid #f0f0f1; display: flex; gap: 10px; }

        .lawyer-table-wrap { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; overflow: hidden; }
        .lawyer-table { width: 100%; border-collapse: collapse; }
        .lawyer-table th { background: #f6f7f7; padding: 12px 15px; text-align: right; font-weight: 600; border-bottom: 1px solid #c3c4c7; font-size: 13px; }
        .lawyer-table td { padding: 14px 15px; border-bottom: 1px solid #f0f0f1; vertical-align: middle; }
        .lawyer-table tr:last-child td { border-bottom: none; }
        .lawyer-table tr:hover { background: #f9f9f9; }

        .type-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .type-badge-online { background: #e6f6e6; color: #007117; }
        .type-badge-offline { background: #e5e5ff; color: #0a4b78; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-active { background: #e6f6e6; color: #007117; }
        .status-inactive { background: #fce1e1; color: #8a0000; }

        .lawyer-actions a { display: inline-block; padding: 6px 12px; border-radius: 4px; font-size: 12px; text-decoration: none; margin-left: 4px; border: 1px solid #c3c4c7; transition: all 0.2s; }
        .act-edit { background: #f0f6fc; color: #0a4b78; }
        .act-delete { background: #fce1e1; color: #8a0000; border-color: #f5c2c2; }
        .lawyer-actions a:hover { opacity: 0.85; }

        .filter-bar { display: flex; gap: 10px; align-items: center; padding: 15px 20px; background: #f6f7f7; border-bottom: 1px solid #c3c4c7; }
        .filter-bar select { padding: 6px 12px; border: 1px solid #8c8f94; border-radius: 4px; }

        .empty-state { padding: 60px 20px; text-align: center; color: #646970; }
        .empty-state-icon { font-size: 48px; margin-bottom: 15px; opacity: 0.5; }

        .notice-custom { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; border-right: 4px solid; }
        .notice-success-custom { background: #e6f6e6; border-color: #007117; color: #007117; }
        .notice-error-custom { background: #fce1e1; border-color: #8a0000; color: #8a0000; }
    </style>

    <div class="lawyer-wrap">
        <div class="lawyer-header">
            <h1>مدیریت انواع مشاوره</h1>
        </div>

        <!-- Notices -->
        <?php if (isset($_GET['added']) && $_GET['added'] === '1') : ?>
            <div class="notice-custom notice-success-custom"><strong>✓ موفق:</strong> نوع مشاوره با موفقیت اضافه شد.</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated']) && $_GET['updated'] === '1') : ?>
            <div class="notice-custom notice-success-custom"><strong>✓ موفق:</strong> نوع مشاوره با موفقیت ویرایش شد.</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] === '1') : ?>
            <div class="notice-custom notice-success-custom"><strong>✓ موفق:</strong> نوع مشاوره با موفقیت حذف شد.</div>
        <?php endif; ?>
        <?php if (isset($_GET['add_error']) && $_GET['add_error'] === '1') : ?>
            <div class="notice-custom notice-error-custom"><strong>✗ خطا:</strong> لطفاً اطلاعات را به‌درستی وارد کنید.</div>
        <?php endif; ?>
        <?php if (isset($_GET['update_error']) && $_GET['update_error'] === '1') : ?>
            <div class="notice-custom notice-error-custom"><strong>✗ خطا:</strong> ویرایش انجام نشد.</div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="lawyer-stats">
            <div class="lawyer-stat-card">
                <div class="lawyer-stat-info">
                    <h3><?php echo count($consultation_types); ?></h3>
                    <p>کل انواع مشاوره</p>
                </div>
            </div>
            <div class="lawyer-stat-card">
                <div class="lawyer-stat-info">
                    <h3><?php echo (int)$online_count; ?></h3>
                    <p>مشاوره تلفنی / آنلاین</p>
                </div>
            </div>
            <div class="lawyer-stat-card">
                <div class="lawyer-stat-info">
                    <h3><?php echo (int)$offline_count; ?></h3>
                    <p>مشاوره حضوری</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="lawyer-tabs">
            <a href="<?php echo esc_url(lawyer_consultation_types_admin_url(['tab' => 'add'])); ?>" class="lawyer-tab <?php echo $active_tab === 'add' ? 'active' : ''; ?>">افزودن نوع مشاوره جدید</a>
            <a href="<?php echo esc_url(lawyer_consultation_types_admin_url(['tab' => 'list'])); ?>" class="lawyer-tab <?php echo $active_tab === 'list' || $active_tab === 'edit' ? 'active' : ''; ?>">📋 لیست انواع مشاوره</a>
            <?php if ($edit_type) : ?>
                <span class="lawyer-tab active">ویرایش: <?php echo esc_html($edit_type->title); ?></span>
            <?php endif; ?>
        </div>

        <!-- Add / Edit Form Panel -->
        <?php if ($active_tab === 'add' || $active_tab === 'edit') : ?>
            <div class="lawyer-panel">
                <h2><?php echo $edit_type ? 'ویرایش نوع مشاوره' : 'افزودن نوع مشاوره جدید'; ?></h2>
                <form method="post" action="<?php echo esc_url(lawyer_consultation_types_admin_url()); ?>">
                    <?php
                    if ($edit_type) {
                        wp_nonce_field('lawyer_edit_consultation_type', 'lawyer_edit_consultation_nonce');
                        echo '<input type="hidden" name="edit_id" value="' . esc_attr($edit_type->id) . '">';
                    } else {
                        wp_nonce_field('lawyer_add_consultation_type', 'lawyer_consultation_nonce');
                    }
                    ?>

                    <div class="lawyer-form-group full">
                        <label>نوع مشاوره <span style="color:#b32d2e;">*</span></label>
                        <div class="consultation-type-selector">
                            <label class="consultation-type-option">
                                <input type="radio" name="consultation_type" value="online" <?php echo (!$edit_type || $edit_type->consultation_type === 'online') ? 'checked' : ''; ?> required>
                                <div class="type-box">
                                    <span class="type-label">مشاوره تلفنی / آنلاین</span>
                                    <span class="type-desc">مشاوره از طریق تماس تلفنی یا ویدئو</span>
                                </div>
                            </label>
                            <label class="consultation-type-option">
                                <input type="radio" name="consultation_type" value="offline" <?php echo ($edit_type && $edit_type->consultation_type === 'offline') ? 'checked' : ''; ?> required>
                                <div class="type-box">
                                    <span class="type-label">مشاوره حضوری</span>
                                    <span class="type-desc">مراجعه حضوری به دفتر وکالت</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="lawyer-form-grid">
                        <div class="lawyer-form-group">
                            <label for="ct-title">عنوان مشاوره <span style="color:#b32d2e;">*</span></label>
                            <input type="text" id="ct-title" name="title" value="<?php echo esc_attr($edit_type ? $edit_type->title : ''); ?>" required placeholder="مثال: مشاوره حقوقی خانواده">
                            <span class="hint">نام نمایشی که کاربر می‌بیند</span>
                        </div>

                        <div class="lawyer-form-group">
                            <label for="ct-duration">مدت مشاوره (دقیقه) <span style="color:#b32d2e;">*</span></label>
                            <input type="number" id="ct-duration" name="duration" min="1" step="1" value="<?php echo esc_attr($edit_type ? $edit_type->duration : 30); ?>" required>
                            <span class="hint">مدت زمان هر جلسه مشاوره</span>
                        </div>

                        <div class="lawyer-form-group">
                            <label for="ct-price">هزینه مشاوره (تومان) <span style="color:#b32d2e;">*</span></label>
                            <input type="number" id="ct-price" name="price" min="0" step="1" value="<?php echo esc_attr($edit_type ? $edit_type->price : 0); ?>" required>
                            <span class="hint">قیمت را به تومان وارد کنید</span>
                        </div>

                        <?php if ($edit_type) : ?>
                            <div class="lawyer-form-group">
                                <label for="ct-status">وضعیت</label>
                                <select id="ct-status" name="status">
                                    <option value="active" <?php selected($edit_type->status, 'active'); ?>>فعال</option>
                                    <option value="inactive" <?php selected($edit_type->status, 'inactive'); ?>>غیرفعال</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="lawyer-form-group full">
                            <label for="ct-desc">توضیحات (اختیاری)</label>
                            <textarea id="ct-desc" name="description" rows="4"><?php echo esc_textarea($edit_type ? $edit_type->description : ''); ?></textarea>
                        </div>
                    </div>

                    <div class="lawyer-form-actions">
                        <button type="submit" name="<?php echo $edit_type ? 'lawyer_update_consultation_type' : 'lawyer_add_consultation_type'; ?>" class="lawyer-btn lawyer-btn-primary">
                            <?php echo $edit_type ? 'ذخیره تغییرات' : 'افزودن نوع مشاوره'; ?>
                        </button>
                        <?php if ($edit_type) : ?>
                            <a href="<?php echo esc_url(lawyer_consultation_types_admin_url()); ?>" class="lawyer-btn lawyer-btn-secondary">انصراف</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- List Panel -->
        <?php if ($active_tab === 'list' || $active_tab === 'edit') : ?>
            <div class="lawyer-table-wrap" style="margin-top: 25px;">
                <form method="get" class="filter-bar">
                    <input type="hidden" name="page" value="lawyer-consultation-types">
                    <input type="hidden" name="tab" value="list">
                    <label><strong>فیلتر بر اساس نوع:</strong></label>
                    <select name="filter_type">
                        <option value="">همه انواع</option>
                        <option value="online" <?php selected($filter_type, 'online'); ?>>تلفنی / آنلاین</option>
                        <option value="offline" <?php selected($filter_type, 'offline'); ?>>حضوری</option>
                    </select>
                    <button type="submit" class="lawyer-btn lawyer-btn-secondary" style="padding: 6px 16px;">اعمال فیلتر</button>
                    <?php if ($filter_type) : ?>
                        <a href="<?php echo esc_url(lawyer_consultation_types_admin_url(['tab' => 'list'])); ?>" style="font-size: 12px;">پاک کردن فیلتر</a>
                    <?php endif; ?>
                </form>

                <table class="lawyer-table">
                    <thead>
                        <tr>
                            <th>عنوان</th>
                            <th>نوع مشاوره</th>
                            <th>مدت</th>
                            <th>قیمت</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($consultation_types)) : foreach ($consultation_types as $type) :
                            $edit_url   = lawyer_consultation_types_admin_url(['action' => 'edit', 'id' => $type->id]);
                            $delete_url = wp_nonce_url(lawyer_consultation_types_admin_url(['action' => 'delete', 'id' => $type->id]), 'delete_consultation_type_' . $type->id);
                            $type_label = $type->consultation_type === 'online' ? 'تلفنی/آنلاین' : 'حضوری';
                            $type_class = 'type-badge-' . $type->consultation_type;
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html($type->title); ?></strong></td>
                                <td><span class="type-badge <?php echo $type_class; ?>"><?php echo $type_label; ?></span></td>
                                <td><?php echo esc_html($type->duration); ?> دقیقه</td>
                                <td><strong><?php echo esc_html(number_format($type->price)); ?></strong> تومان</td>
                                <td>
                                    <span class="status-badge status-<?php echo $type->status; ?>">
                                        <?php echo $type->status === 'active' ? '✓ فعال' : '✗ غیرفعال'; ?>
                                    </span>
                                </td>
                                <td class="lawyer-actions">
                                    <a href="<?php echo esc_url($edit_url); ?>" class="act-edit">ویرایش</a>
                                    <a href="<?php echo esc_url($delete_url); ?>" class="act-delete" onclick="return confirm('آیا از حذف این نوع مشاوره مطمئن هستید؟');">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; else : ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <p>هنوز نوع مشاوره‌ای ثبت نشده است.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
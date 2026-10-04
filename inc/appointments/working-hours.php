<?php
// Changed from admin_init to after_setup_theme
add_action('after_setup_theme', 'lawyer_create_working_hours_table');

function lawyer_create_working_hours_table() {
    global $wpdb;
    $table_name    = $wpdb->prefix . 'lawyer_working_hours';
    $charset_collate = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    
    $sql = "CREATE TABLE {$table_name} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        day_of_week tinyint(1) unsigned NOT NULL,
        is_active tinyint(1) NOT NULL DEFAULT 1,
        start_time time NOT NULL,
        end_time time NOT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY day_of_week (day_of_week)
    ) {$charset_collate};";
    
    dbDelta($sql);
    
    $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    if ($count > 0) return;
    
    $default_hours = [
        ['day_of_week' => 6, 'is_active' => 1, 'start_time' => '09:00:00', 'end_time' => '17:00:00'],
        ['day_of_week' => 0, 'is_active' => 1, 'start_time' => '09:00:00', 'end_time' => '17:00:00'],
        ['day_of_week' => 1, 'is_active' => 1, 'start_time' => '09:00:00', 'end_time' => '17:00:00'],
        ['day_of_week' => 2, 'is_active' => 1, 'start_time' => '09:00:00', 'end_time' => '17:00:00'],
        ['day_of_week' => 3, 'is_active' => 1, 'start_time' => '09:00:00', 'end_time' => '17:00:00'],
        ['day_of_week' => 4, 'is_active' => 1, 'start_time' => '09:00:00', 'end_time' => '17:00:00'],
        ['day_of_week' => 5, 'is_active' => 0, 'start_time' => '09:00:00', 'end_time' => '17:00:00'],
    ];
    
    foreach ($default_hours as $hour) {
        $wpdb->insert($table_name, $hour, ['%d', '%d', '%s', '%s']);
    }
}

/**
 * =========================================================
 * Admin Menu
 * =========================================================
 */

add_action(
    'admin_menu',
    'lawyer_working_hours_admin_menu'
);

function lawyer_working_hours_admin_menu() {

    add_submenu_page(
        'lawyer-consultation-types',
        'ساعات کاری',
        'ساعات کاری',
        'manage_options',
        'lawyer-working-hours',
        'lawyer_working_hours_page'
    );
}


/**
 * =========================================================
 * Handle Save
 * =========================================================
 */

add_action(
    'admin_post_lawyer_save_working_hours',
    'lawyer_save_working_hours'
);

function lawyer_save_working_hours() {

    if (
        !current_user_can('manage_options')
    ) {
        wp_die('دسترسی غیرمجاز.');
    }

    if (
        !isset($_POST['lawyer_working_hours_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST['lawyer_working_hours_nonce']
                )
            ),
            'lawyer_save_working_hours'
        )
    ) {
        wp_die('درخواست نامعتبر است.');
    }

    global $wpdb;

    $table_name =
        $wpdb->prefix . 'lawyer_working_hours';

    $days = isset($_POST['days'])
        ? $_POST['days']
        : [];

    for ($day = 0; $day <= 6; $day++) {

        $is_active = isset(
            $days[$day]['is_active']
        ) ? 1 : 0;

        $start_time = isset(
            $days[$day]['start_time']
        )
            ? sanitize_text_field(
                wp_unslash(
                    $days[$day]['start_time']
                )
            )
            : '09:00';

        $end_time = isset(
            $days[$day]['end_time']
        )
            ? sanitize_text_field(
                wp_unslash(
                    $days[$day]['end_time']
                )
            )
            : '17:00';

        if (
            !preg_match(
                '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                $start_time
            )
        ) {
            $start_time = '09:00';
        }

        if (
            !preg_match(
                '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                $end_time
            )
        ) {
            $end_time = '17:00';
        }

        $wpdb->update(
            $table_name,
            [
                'is_active' => $is_active,
                'start_time' => $start_time . ':00',
                'end_time' => $end_time . ':00',
            ],
            [
                'day_of_week' => $day,
            ],
            [
                '%d',
                '%s',
                '%s',
            ],
            [
                '%d',
            ]
        );
    }

    wp_safe_redirect(
        add_query_arg(
            [
                'page' => 'lawyer-working-hours',
                'updated' => 1,
            ],
            admin_url('admin.php')
        )
    );

    exit;
}


/**
 * =========================================================
 * Admin Page
 * =========================================================
 */

function lawyer_working_hours_page() {

    if (
        !current_user_can('manage_options')
    ) {
        wp_die('دسترسی غیرمجاز.');
    }

    global $wpdb;

    $table_name =
        $wpdb->prefix . 'lawyer_working_hours';

    $working_hours =
        $wpdb->get_results(
            "SELECT *
            FROM {$table_name}
            ORDER BY day_of_week ASC"
        );

    $days = [
        0 => 'یکشنبه',
        1 => 'دوشنبه',
        2 => 'سه‌شنبه',
        3 => 'چهارشنبه',
        4 => 'پنجشنبه',
        5 => 'جمعه',
        6 => 'شنبه',
    ];

    ?>

    <div class="wrap">

        <h1>
            ساعات کاری
        </h1>

        <?php if (
            isset($_GET['updated']) &&
            $_GET['updated'] === '1'
        ) : ?>

            <div class="notice notice-success is-dismissible">
                <p>
                    ساعات کاری با موفقیت ذخیره شد.
                </p>
            </div>

        <?php endif; ?>

        <form
            method="post"
            action="<?php echo esc_url(
                admin_url('admin-post.php')
            ); ?>"
        >

            <input
                type="hidden"
                name="action"
                value="lawyer_save_working_hours"
            >

            <?php
            wp_nonce_field(
                'lawyer_save_working_hours',
                'lawyer_working_hours_nonce'
            );
            ?>

            <table class="widefat striped" style="margin-top:20px;">

                <thead>
                    <tr>
                        <th>روز</th>
                        <th>وضعیت</th>
                        <th>ساعت شروع</th>
                        <th>ساعت پایان</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($days as $day_id => $day_name) : ?>

                        <?php
                        $current_day = null;

                        foreach ($working_hours as $row) {
                            if ((int) $row->day_of_week === $day_id) {
                                $current_day = $row;
                                break;
                            }
                        }

                        $start_time = $current_day
                            ? substr($current_day->start_time, 0, 5)
                            : '09:00';

                        $end_time = $current_day
                            ? substr($current_day->end_time, 0, 5)
                            : '17:00';

                        $is_active = $current_day
                            ? (int) $current_day->is_active
                            : 0;
                        ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php echo esc_html($day_name); ?>
                                </strong>
                            </td>

                            <td>
                                <label>
                                    <input
                                        type="checkbox"
                                        name="days[<?php echo esc_attr($day_id); ?>][is_active]"
                                        value="1"
                                        <?php checked($is_active, 1); ?>
                                    >
                                    فعال
                                </label>
                            </td>

                            <td>
                                <input
                                    type="time"
                                    name="days[<?php echo esc_attr($day_id); ?>][start_time]"
                                    value="<?php echo esc_attr($start_time); ?>"
                                >
                            </td>

                            <td>
                                <input
                                    type="time"
                                    name="days[<?php echo esc_attr($day_id); ?>][end_time]"
                                    value="<?php echo esc_attr($end_time); ?>"
                                >
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

            <p class="submit">

                <button
                    type="submit"
                    class="button button-primary"
                >
                    ذخیره ساعات کاری
                </button>

            </p>

        </form>

    </div>

    <?php
}
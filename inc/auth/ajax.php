<?php
/**
 * =========================================================
 * LOGOUT ACTION
 * =========================================================
 */

add_action(
    'wp_ajax_auth_logout',
    'auth_logout'
);


/**
 * =========================================================
 * LOGOUT
 * =========================================================
 */

function auth_logout() {

    /*
     * -----------------------------------------------------
     * Check nonce
     * -----------------------------------------------------
     */

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['nonce'])
            ),
            'auth_logout'
        )
    ) {

        error_log(
            'AUTH LOGOUT: Invalid nonce'
        );

        wp_send_json_error(
            [
                'message' =>
                    'درخواست نامعتبر است.'
            ],
            403
        );
    }


    /*
     * -----------------------------------------------------
     * Check login status
     * -----------------------------------------------------
     */

    if (!is_user_logged_in()) {

        error_log(
            'AUTH LOGOUT: User is not logged in.'
        );

        wp_send_json_error(
            [
                'message' =>
                    'شما وارد حساب کاربری نیستید.'
            ],
            401
        );
    }


    /*
     * -----------------------------------------------------
     * Get current user
     * -----------------------------------------------------
     */

    $current_user = wp_get_current_user();


    if (
        !$current_user ||
        empty($current_user->ID)
    ) {

        error_log(
            'AUTH LOGOUT: Current user could not be retrieved.'
        );

        wp_send_json_error(
            [
                'message' =>
                    'اطلاعات کاربر دریافت نشد.'
            ],
            500
        );
    }


    error_log(
        'AUTH LOGOUT: User ID = '
        . $current_user->ID
        . ' logging out.'
    );


    /*
     * -----------------------------------------------------
     * Logout
     * -----------------------------------------------------
     */

    wp_logout();


    /*
     * -----------------------------------------------------
     * Verify logout
     * -----------------------------------------------------
     */

    if (is_user_logged_in()) {

        error_log(
            'AUTH LOGOUT: Logout FAILED.'
        );

        wp_send_json_error(
            [
                'message' =>
                    'خروج از حساب انجام نشد.'
            ],
            500
        );
    }


    error_log(
        'AUTH LOGOUT: Logout SUCCESS.'
    );


    /*
     * -----------------------------------------------------
     * Response
     * -----------------------------------------------------
     */

    wp_send_json_success(
        [
            'message' =>
                'با موفقیت از حساب کاربری خارج شدید.'
        ]
    );
}

/**
 * =========================================================
 * UPDATE ACCOUNT
 * =========================================================
 */

add_action(
    'wp_ajax_auth_update_account',
    'auth_update_account'
);


function auth_update_account() {

    /*
     * Check nonce
     */

    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['nonce'])
            ),
            'auth_update_account'
        )
    ) {

        wp_send_json_error(
            [
                'message' => 'درخواست نامعتبر است.'
            ],
            403
        );
    }


    /*
     * Check login
     */

    if (!is_user_logged_in()) {

        wp_send_json_error(
            [
                'message' => 'ابتدا وارد حساب کاربری شوید.'
            ],
            401
        );
    }


    /*
     * Get current user
     */

    $user_id = get_current_user_id();


    /*
     * Get name
     */

    $display_name = isset($_POST['display_name'])
        ? sanitize_text_field(
            wp_unslash($_POST['display_name'])
        )
        : '';


    /*
     * Validate name
     */

    if (empty($display_name)) {

        wp_send_json_error(
            [
                'message' => 'نام نمی‌تواند خالی باشد.'
            ],
            400
        );
    }


    /*
     * Update user
     */

    $result = wp_update_user(
        [
            'ID' => $user_id,
            'display_name' => $display_name,
            'first_name' => $display_name,
        ]
    );


    if (is_wp_error($result)) {

        error_log(
            'AUTH ACCOUNT UPDATE ERROR: ' .
            $result->get_error_message()
        );

        wp_send_json_error(
            [
                'message' => 'ذخیره اطلاعات انجام نشد.'
            ],
            500
        );
    }

    /*
    * 🔥 سینک نام جدید با جدول رزروها
    */
    global $wpdb;
    $bookings_table = $wpdb->prefix . 'lawyer_bookings';
    $wpdb->update(
        $bookings_table,
        ['client_name' => $display_name], // نام جدید
        ['user_id' => $user_id],          // برای تمام رزروهای این کاربر
        ['%s'],
        ['%d']
    );


    /*
     * Get updated user
     */

    $user = get_user_by(
        'id',
        $user_id
    );


    if (!$user) {

        wp_send_json_error(
            [
                'message' => 'اطلاعات کاربر دریافت نشد.'
            ],
            500
        );
    }


    /*
     * Success
     */

    wp_send_json_success(
        [
            'message' => 'تغییرات با موفقیت ذخیره شد.',

            'user' => [
                'id' => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email,
            ],
        ]
    );
}


add_action(
    'wp_ajax_save_booking_mobile',
    'lawyer_save_booking_mobile'
);

function lawyer_save_booking_mobile() {

    if (!is_user_logged_in()) {
        wp_send_json_error([
            'message' => 'کاربر وارد نشده است.'
        ]);
    }

    $mobile = isset($_POST['mobile'])
        ? sanitize_text_field(
            wp_unslash($_POST['mobile'])
        )
        : '';

    if (!preg_match('/^09\d{9}$/', $mobile)) {
        wp_send_json_error([
            'message' => 'شماره موبایل معتبر نیست.'
        ]);
    }

    $user_id = get_current_user_id();

    update_user_meta(
        $user_id,
        'auth_mobile',
        $mobile
    );

    wp_send_json_success([
        'mobile' => $mobile
    ]);
}
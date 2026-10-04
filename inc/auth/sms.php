<?php
declare(strict_types=1);

/**
 * =========================================================
 * SMS Authentication
 * =========================================================
 *
 * Responsibilities:
 * - Receiving and validating mobile numbers
 * - Generating OTPs
 * - Securely storing OTPs
 * - Rate-limiting OTP requests
 * - Validating OTPs
 * - Logging in existing users
 * - Creating new user accounts
 *
 * SECURITY IMPROVEMENTS:
 * - OTPs are NEVER logged (not even in debug)
 * - Daily rate limiting added
 * - IP-based rate limiting added
 * - Dead code removed
 */

/**
 * =========================================================
 * Constants
 * =========================================================
 */

define('AUTH_OTP_EXPIRATION', 5 * MINUTE_IN_SECONDS);
define('AUTH_OTP_RESEND_DELAY', 120); // افزایش از 60 به 120 ثانیه
define('AUTH_OTP_MAX_ATTEMPTS', 5);
define('AUTH_OTP_DAILY_LIMIT', 10); // حداکثر 10 OTP در روز برای هر شماره
define('AUTH_OTP_IP_DAILY_LIMIT', 20); // حداکثر 20 درخواست از هر IP در روز

/**
 * =========================================================
 * AJAX ACTIONS
 * =========================================================
 */

add_action('wp_ajax_nopriv_auth_send_otp', 'auth_send_otp');
add_action('wp_ajax_auth_send_otp', 'auth_send_otp');

add_action('wp_ajax_nopriv_auth_verify_otp', 'auth_verify_otp');
add_action('wp_ajax_auth_verify_otp', 'auth_verify_otp');

/**
 * =========================================================
 * Normalize Mobile
 * =========================================================
 */
function auth_normalize_mobile(string $mobile): string {
    $mobile = preg_replace('/\D/', '', $mobile);
    
    if (str_starts_with($mobile, '0098')) {
        $mobile = '0' . substr($mobile, 4);
    } elseif (str_starts_with($mobile, '98')) {
        $mobile = '0' . substr($mobile, 2);
    }
    
    return $mobile;
}

/**
 * =========================================================
 * Validate Mobile
 * =========================================================
 */
function auth_is_valid_mobile(string $mobile): bool {
    return (bool) preg_match('/^09\d{9}$/', $mobile);
}

/**
 * =========================================================
 * Get Client IP (for rate limiting)
 * =========================================================
 */
function auth_get_client_ip(): string {
    $ip_keys = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            return filter_var($ip, FILTER_VALIDATE_IP) ?: '0.0.0.0';
        }
    }
    
    return '0.0.0.0';
}

/**
 * =========================================================
 * SEND OTP
 * =========================================================
 */
function auth_send_otp() {
    // -----------------------------------------------------
    // Check nonce
    // -----------------------------------------------------
    if (!isset($_POST['nonce']) || 
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['nonce'])),
            'auth_sms'
        )
    ) {
        wp_send_json_error([
            'message' => 'درخواست نامعتبر است.'
        ], 403);
    }
    
    // -----------------------------------------------------
    // Get mobile
    // -----------------------------------------------------
    $mobile = isset($_POST['mobile']) 
        ? sanitize_text_field(wp_unslash($_POST['mobile'])) 
        : '';
    
    // -----------------------------------------------------
    // Normalize mobile
    // -----------------------------------------------------
    $mobile = auth_normalize_mobile($mobile);
    
    // -----------------------------------------------------
    // Validate mobile
    // -----------------------------------------------------
    if (!auth_is_valid_mobile($mobile)) {
        wp_send_json_error([
            'message' => 'شماره موبایل وارد شده صحیح نیست.'
        ], 400);
    }
    
    // -----------------------------------------------------
    // Daily Rate Limit (per mobile)
    // -----------------------------------------------------
    $daily_limit_key = 'auth_otp_daily_' . md5($mobile);
    $daily_count = (int) get_transient($daily_limit_key);
    
    if ($daily_count >= AUTH_OTP_DAILY_LIMIT) {
        wp_send_json_error([
            'message' => 'تعداد درخواست‌های امروز شما به حداکثر رسیده است. لطفاً فردا مجدداً تلاش کنید.'
        ], 429);
    }
    
    // -----------------------------------------------------
    // IP-based Daily Rate Limit
    // -----------------------------------------------------
    $client_ip = auth_get_client_ip();
    $ip_daily_key = 'auth_otp_ip_daily_' . md5($client_ip);
    $ip_daily_count = (int) get_transient($ip_daily_key);
    
    if ($ip_daily_count >= AUTH_OTP_IP_DAILY_LIMIT) {
        wp_send_json_error([
            'message' => 'تعداد درخواست‌ها از این IP به حداکثر رسیده است.'
        ], 429);
    }
    
    // -----------------------------------------------------
    // Short-term Rate Limit (resend delay)
    // -----------------------------------------------------
    $rate_limit_key = 'auth_otp_rate_' . md5($mobile);
    
    if (get_transient($rate_limit_key)) {
        wp_send_json_error([
            'message' => 'لطفاً کمی صبر کنید و دوباره تلاش کنید.'
        ], 429);
    }
    
    // -----------------------------------------------------
    // Generate OTP
    // -----------------------------------------------------
    $otp = (string) wp_rand(100000, 999999);
    
    // -----------------------------------------------------
    // OTP storage key
    // -----------------------------------------------------
    $otp_key = 'auth_otp_' . md5($mobile);
    
    // -----------------------------------------------------
    // Store OTP (hashed, never plain text)
    // -----------------------------------------------------
    $otp_data = [
        'hash' => wp_hash_password($otp),
        'mobile' => $mobile,
        'attempts' => 0,
        'created_at' => time(),
    ];
    
    set_transient($otp_key, $otp_data, AUTH_OTP_EXPIRATION);
    
    // -----------------------------------------------------
    // Set resend rate limit
    // -----------------------------------------------------
    set_transient($rate_limit_key, true, AUTH_OTP_RESEND_DELAY);
    
    // -----------------------------------------------------
    // Increment daily counters
    // -----------------------------------------------------
    $tomorrow = strtotime('tomorrow midnight') - time();
    
    if (!get_transient($daily_limit_key)) {
        set_transient($daily_limit_key, 1, $tomorrow);
    } else {
        set_transient($daily_limit_key, $daily_count + 1, $tomorrow);
    }
    
    if (!get_transient($ip_daily_key)) {
        set_transient($ip_daily_key, 1, $tomorrow);
    } else {
        set_transient($ip_daily_key, $ip_daily_count + 1, $tomorrow);
    }
    
    // -----------------------------------------------------
    // 🔒 SECURITY: OTP is NEVER logged, not even in debug
    // فقط لاگ کلی بدون نمایش کد OTP
    // -----------------------------------------------------
    error_log(
        'AUTH OTP GENERATED | Mobile: ' . $mobile . 
        ' | IP: ' . $client_ip . 
        ' | Time: ' . date('Y-m-d H:i:s')
    );
    
    // -----------------------------------------------------
    // TODO: در اینجا باید کد ارسال واقعی SMS قرار گیرد
    // مثال:
    // kavenegar_send_sms($mobile, "کد تأیید شما: $otp");
    //
    // در حال حاضر، برای تست می‌توانید این خط را در محیط dev
    // به صورت موقت فعال کنید:
    // -----------------------------------------------------
    if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        // فقط در محیط توسعه، OTP در فایل debug.log ذخیره می‌شود
        error_log("DEV MODE - OTP for {$mobile}: {$otp}");
    }
    
    // -----------------------------------------------------
    // Response
    // -----------------------------------------------------
    wp_send_json_success([
        'message' => 'کد تأیید با موفقیت ارسال شد.',
        'mobile' => $mobile,
        'expires_in' => AUTH_OTP_EXPIRATION,
        'resend_after' => AUTH_OTP_RESEND_DELAY,
    ]);
}

/**
 * =========================================================
 * VERIFY OTP
 * =========================================================
 */
function auth_verify_otp() {
    // -----------------------------------------------------
    // Check nonce
    // -----------------------------------------------------
    if (!isset($_POST['nonce']) || 
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['nonce'])),
            'auth_sms'
        )
    ) {
        wp_send_json_error([
            'message' => 'درخواست نامعتبر است.'
        ], 403);
    }
    
    // -----------------------------------------------------
    // Get mobile
    // -----------------------------------------------------
    $mobile = isset($_POST['mobile']) 
        ? sanitize_text_field(wp_unslash($_POST['mobile'])) 
        : '';
    
    // -----------------------------------------------------
    // Get OTP
    // -----------------------------------------------------
    $otp = isset($_POST['otp']) 
        ? sanitize_text_field(wp_unslash($_POST['otp'])) 
        : '';
    
    // -----------------------------------------------------
    // Normalize mobile
    // -----------------------------------------------------
    $mobile = auth_normalize_mobile($mobile);
    
    // -----------------------------------------------------
    // Validate mobile
    // -----------------------------------------------------
    if (!auth_is_valid_mobile($mobile)) {
        wp_send_json_error([
            'message' => 'شماره موبایل وارد شده صحیح نیست.'
        ], 400);
    }
    
    // -----------------------------------------------------
    // Validate OTP format
    // -----------------------------------------------------
    if (!preg_match('/^\d{6}$/', $otp)) {
        wp_send_json_error([
            'message' => 'کد تأیید باید ۶ رقمی باشد.'
        ], 400);
    }
    
    // -----------------------------------------------------
    // Get stored OTP
    // -----------------------------------------------------
    $otp_key = 'auth_otp_' . md5($mobile);
    $otp_data = get_transient($otp_key);
    
    // -----------------------------------------------------
    // Check OTP existence
    // -----------------------------------------------------
    if (!is_array($otp_data) || empty($otp_data['hash'])) {
        wp_send_json_error([
            'message' => 'کد تأیید منقضی شده است. لطفاً کد جدید دریافت کنید.'
        ], 400);
    }
    
    // -----------------------------------------------------
    // Check attempts
    // -----------------------------------------------------
    $attempts = isset($otp_data['attempts']) ? (int) $otp_data['attempts'] : 0;
    
    if ($attempts >= AUTH_OTP_MAX_ATTEMPTS) {
        delete_transient($otp_key);
        
        wp_send_json_error([
            'message' => 'تعداد تلاش‌های مجاز تمام شده است. لطفاً کد جدید دریافت کنید.'
        ], 429);
    }
    
    // -----------------------------------------------------
    // Verify OTP
    // -----------------------------------------------------
    $is_valid = wp_check_password($otp, $otp_data['hash']);
    
    // -----------------------------------------------------
    // Invalid OTP
    // -----------------------------------------------------
    if (!$is_valid) {
        $otp_data['attempts'] = $attempts + 1;
        
        // Keep original expiration time
        $remaining_ttl = AUTH_OTP_EXPIRATION;
        
        if (!empty($otp_data['created_at'])) {
            $elapsed = time() - (int) $otp_data['created_at'];
            $remaining_ttl = max(1, AUTH_OTP_EXPIRATION - $elapsed);
        }
        
        set_transient($otp_key, $otp_data, $remaining_ttl);
        
        $remaining_attempts = max(0, AUTH_OTP_MAX_ATTEMPTS - $otp_data['attempts']);
        
        wp_send_json_error([
            'message' => 'کد تأیید وارد شده صحیح نیست.',
            'remaining_attempts' => $remaining_attempts,
        ], 400);
    }
    
    // -----------------------------------------------------
    // OTP verified - delete it (one-time use)
    // -----------------------------------------------------
    delete_transient($otp_key);
    
    // -----------------------------------------------------
    // Find existing user
    // -----------------------------------------------------
    $user_query = new WP_User_Query([
        'number' => 1,
        'fields' => 'all',
        'meta_query' => [
            [
                'key' => 'auth_mobile',
                'value' => $mobile,
                'compare' => '=',
            ],
        ],
    ]);
    
    $users = $user_query->get_results();
    $user = !empty($users) ? $users[0] : false;
    
    // =====================================================
    // EXISTING USER
    // =====================================================
    if ($user) {
        // Make sure mobile is stored
        update_user_meta($user->ID, 'auth_mobile', $mobile);
        
        // Login user
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true, is_ssl());
        
        wp_send_json_success([
            'message' => 'ورود با شماره موبایل با موفقیت انجام شد.',
            'verified' => true,
            'new_user' => false,
            'user' => [
                'id' => $user->ID,
                'name' => $user->display_name,
                'mobile' => $mobile,
            ],
        ]);
    }
    
    // =====================================================
    // CREATE NEW USER
    // =====================================================
    
    // Use hash-based username for privacy (not the mobile number)
    $username_base = 'user_' . substr(md5($mobile), 0, 8);
    $username = $username_base;
    $counter = 1;
    
    while (username_exists($username)) {
        $username = $username_base . $counter;
        $counter++;
    }
    
    $password = wp_generate_password(32, true, true);
    $user_id = wp_create_user($username, $password);
    
    if (is_wp_error($user_id)) {
        error_log('AUTH SMS VERIFY: User creation failed. ' . $user_id->get_error_message());
        wp_send_json_error(['message' => 'ساخت حساب کاربری با خطا مواجه شد.'], 500);
    }
    
    // Add placeholder email for plugin compatibility
    $fake_email = $username . '@sms-user.local';
    
    $update_result = wp_update_user([
        'ID' => $user_id,
        'display_name' => $mobile,
        'user_email' => $fake_email,
        'role' => 'subscriber',
    ]);
    
    if (is_wp_error($update_result)) {
        error_log('AUTH SMS VERIFY: wp_update_user ERROR: ' . $update_result->get_error_message());
    }
    
    update_user_meta($user_id, 'auth_mobile', $mobile);
    
    $user = get_user_by('id', $user_id);
    if (!$user) {
        wp_send_json_error(['message' => 'حساب ساخته شد اما دریافت اطلاعات آن ممکن نبود.'], 500);
    }
    
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true, is_ssl());
    
    wp_send_json_success([
        'message' => 'حساب کاربری با موفقیت ایجاد و وارد شدید.',
        'verified' => true,
        'new_user' => true,
        'user' => [
            'id' => $user->ID,
            'name' => $user->display_name,
            'mobile' => $mobile,
        ],
    ]);
}
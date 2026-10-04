<?php
if (post_password_required()) {
    return;
}
?>

<div class="comments-section">

    <div class="comments-header">
        <h4>
            <?php pll_e('دیدگاه‌ها'); ?>
            <svg xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 9 10"
                fill="none">
                <rect
                    x="4.24219"
                    y="0.757324"
                    width="6"
                    height="6"
                    transform="rotate(45 4.24219 0.757324)"
                    fill="#2c5a8a"/>
            </svg>
        </h4>
        <span class="comments-count">
            <?php echo get_comments_number(); ?>
        </span>
    </div>

    <?php if (have_comments()) : ?>
        <div class="comments-list">
            <?php
            wp_list_comments([
                'style'       => 'ul',
                'avatar_size' => 60,
                'callback'    => 'my_comment_callback',
            ]);
            ?>
        </div>
        <?php the_comments_navigation(); ?>
        <?php else : ?>
        <div class="no-comments mb-4">
            <?php pll_e('هنوز دیدگاهی ثبت نشده است.'); ?>
        </div>
    <?php endif; ?>

    <div class="comment-form-wrapper">
        <?php
        comment_form([
            'label_submit' => pll__('ثبت دیدگاه'),
            'logged_in_as' => '
                <p class="comment-logged-in">
                    ' . pll__('شما وارد حساب کاربری خود شده‌اید و می‌توانید دیدگاهتان را ثبت کنید.') . '
                </p>',

            'comment_notes_before' => '
                <p class="comment-notes">
                    ' . pll__('نظر، تجربه یا پرسش خود را بنویسید. دیدگاه شما پس از بررسی منتشر خواهد شد.') . '
                </p>',

            'comment_notes_after' => '
                <p class="comment-notes-after">
                    ' . pll__('از اینکه زمان گذاشتید و دیدگاه خود را با ما به اشتراک می‌گذارید، سپاسگزاریم.') . '
                </p>',

            'submit_button' => '
                <button name="%1$s" type="submit" id="%2$s" class="%3$s">
                    ' . pll__('ثبت دیدگاه') . '
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><g opacity="0.4"><path d="M12.3203 9.32031L9.76031 11.8803L12.3203 14.4403" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M20 11.8809L9.83 11.8809" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></g><path d="M11.8199 20C7.39995 20 3.81995 17 3.81995 12C3.81995 7 7.39995 4 11.8199 4" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></svg>                        
                </button>',

            'class_submit' => 'comment-submit',
            'fields' => [
                'author' => '
                    <p class="form-group comment-form-author">
                        <input
                            id="author"
                            name="author"
                            type="text"
                            placeholder=" "
                            required>

                        <label for="author">
                        ' . pll__('نام و نام خانوادگی') . '
                        </label>

                        <small class="form-error"></small>
                    </p>
                ',

                'phone' => '
                    <p class="form-group comment-form-phone">
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            placeholder=" "
                            required>

                        <label for="phone">
                        ' . pll__('شماره تماس') . '
                        </label>

                        <small class="form-error"></small>
                    </p>
                ',

                'email' => '
                    <input
                        type="hidden"
                        id="email"
                        name="email"
                        value="">
                ',

                'url' => '',

                'cookies' => '
                    <p class="comment-form-cookies-consent">
                        <input
                            id="wp-comment-cookies-consent"
                            name="wp-comment-cookies-consent"
                            type="checkbox"
                            value="yes">

                        <label for="wp-comment-cookies-consent">
                            ' . pll__('اطلاعات من برای ارسال دیدگاه‌های بعدی ذخیره شود.') . '
                        </label>
                    </p>
                ',
            ],
            'comment_field' => '
                <p class="form-group comment-form-comment">
                    <textarea
                        id="comment"
                        name="comment"
                        rows="6"
                        placeholder=" "
                        required></textarea>

                    <label for="comment">' . pll__('متن دیدگاه...') . '</label>
                </p>
            '
        ]);
        ?>

    </div>

</div>
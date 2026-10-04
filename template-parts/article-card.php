<div class="article-card">
    <div class="article-thumb">
        <a href="<?php the_permalink(); ?>">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail('medium');
            }
            ?>
        </a>
    </div>

    <h4 class="article-title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h4>

    <p class="article-excerpt">
        <?php echo wp_trim_words(get_the_excerpt(), 6, '...'); ?>
    </p>

    <div class="tm-rd-3"></div>

    <a href="<?php the_permalink(); ?>" class="article-btn">
        <?php pll_e('ادامه مطلب'); ?>
    </a>

    <div class="article-date">
        <svg width="14" height="14" viewBox="0 0 24 24">
            <path d="M7 2v2M17 2v2M3 8h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"
                fill="none"
                stroke="currentColor"
                stroke-width="2"/>
        </svg>

        <span>
            <?php
            $lang = function_exists('pll_current_language')
                ? pll_current_language()
                : 'fa';
            if ($lang === 'fa') {
                if (function_exists('jdate')) {
                    echo jdate('j F Y', get_post_timestamp());
                } else {
                    echo get_the_date('Y/m/d');
                }
            } else {
                echo get_the_date('F j, Y');
            }
            ?>
        </span>
    </div>
</div>
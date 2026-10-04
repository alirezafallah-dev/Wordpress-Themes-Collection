<aside class="sidebar">

    <!-- Latest Posts -->
    <section class="sidebar-card">
        <div class="sidebar-heading">
            <div class="tm-rd-ps-2">
                <h3><?php pll_e('جدیدترین مقالات'); ?></h3>
            </div>
        </div>
        <div class="sidebar-posts">
            <?php
            $latest = new WP_Query([
                'posts_per_page' => 6,
                'post_status'    => 'publish'
            ]);

            while ($latest->have_posts()) :
                $latest->the_post();
            ?>
                <a class="sidebar-post" href="<?php echo esc_url(get_permalink()); ?>">
                    <div class="sidebar-post-thumb">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('thumbnail');
                        } else {
                        ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/default-post.webp'); ?>" 
                                 alt="<?php echo esc_attr(get_the_title()); ?>">
                        <?php } ?>
                    </div>
                    <div class="sidebar-post-content">
                        <h4><?php the_title(); ?></h4>
                        <span>
                            <?php
                            if (function_exists('jdate') && pll_current_language() == 'fa') {
                                echo esc_html(jdate('j F Y', get_post_timestamp()));
                            } else {
                                echo esc_html(get_the_date('F j, Y'));
                            }
                            ?>
                        </span>
                    </div>
                </a>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    </section>

    <!-- Categories -->
    <section class="sidebar-card">
        <div class="sidebar-heading">
            <div class="tm-rd-ps-2">
                <h3><?php pll_e('دسته‌بندی‌ها'); ?></h3>
            </div>
        </div>

        <ul class="sidebar-category-list">
            <?php
            foreach (get_categories() as $cat) :
            ?>
                <li>
                    <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                        <span><?php echo esc_html($cat->name); ?></span>
                        <span class="category-count">
                            <?php echo (int) $cat->count; ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- Tags -->
    <section class="sidebar-card">
        <div class="sidebar-heading">
            <div class="tm-rd-ps-2">
                <h3><?php pll_e('برچسب‌ها'); ?></h3>
            </div>
        </div>

        <div class="sidebar-tags">
            <?php
            wp_tag_cloud([
                'smallest' => 14,
                'largest'  => 14,
                'unit'     => 'px'
            ]);
            ?>
        </div>
    </section>

</aside>
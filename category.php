<?php
get_header();

$current_category = get_queried_object();

$categories = get_categories([
    'hide_empty' => true,
    'parent'     => $current_category->term_id
]);
?>
<main class="archive-page">
    <div class="container">
        <div class="archive-wrapper">

            <aside class="archive-sidebar">
                <div class="sidebar-box">
                    <h3><?php pll_e('جستجو','theme'); ?></h3>
                    <form id="archive-search" onsubmit="return false;">
                        <input type="text" id="search-input" placeholder="<?php pll_e('جستجو...','theme'); ?>">
                    </form>
                </div>

                <div class="sidebar-box">
                    <h3><?php pll_e('دسته بندی ها','theme'); ?></h3>
                    <ul class="archive-categories">
                        <li class="<?php echo is_home()||is_archive()&&!is_category()?'active':''; ?>" data-category="all">
                            <span><?php pll_e('همه','theme'); ?></span>
                            <span class="count"><?php echo $GLOBALS['wp_query']->found_posts; ?></span>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li data-category="<?php echo esc_attr($cat->term_id); ?>">
                                <span><?php echo esc_html($cat->name); ?></span>
                                <span class="count"><?php echo esc_html($cat->count); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>

            <section class="archive-content">

                <div class="archive-toolbar">
                    <div class="posts-count">
                        <?php printf(esc_html__('%d'), $wp_query->found_posts); ?> <?php pll_e('پست','theme'); ?>
                    </div>

                    <div class="posts-sort">
                        <select id="archive-sort">
                            <option value="date_desc"><?php pll_e('جدیدترین','theme'); ?></option>
                            <option value="date_asc"><?php pll_e('قدیمی ترین','theme'); ?></option>
                            <option value="title"><?php pll_e('حروف الفبا','theme'); ?></option>
                            <option value="comment"><?php pll_e('بیشترین نظر','theme'); ?></option>
                        </select>
                    </div>
                </div>

                <div id="posts-container" class="posts-grid">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <?php get_template_part('template-parts/article-card'); ?>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div class="no-posts">
                            <h2><?php pll_e('هیچ مطلبی پیدا نشد.'); ?></h2>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div id="pagination" class="archive-pagination">
                    <?php
                    echo paginate_links([
                        'current'=>max(1,get_query_var('paged')),
                        'total'=>$wp_query->max_num_pages,
                        'mid_size'=>2,
                        'prev_text'=>'«',
                        'next_text'=>'»'
                    ]);
                    ?>
                </div>

            </section>

        </div>
    </div>
</main>
<?php get_footer(); ?>
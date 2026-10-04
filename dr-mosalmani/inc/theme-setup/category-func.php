<?php
/*--------------------------------------------------------------
# Archive Ajax
--------------------------------------------------------------*/

add_action('wp_enqueue_scripts', function () {

    if (!is_archive() && !is_home() && !is_category()) {
        return;
    }

    wp_enqueue_script(
        'archive-js',
        get_template_directory_uri() . '/assets/js/archive.js',
        [],
        '1.0',
        true
    );

    wp_localize_script('archive-js', 'archive_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('archive_nonce')
    ]);

});

add_action('wp_ajax_archive_filter', 'theme_archive_filter');
add_action('wp_ajax_nopriv_archive_filter', 'theme_archive_filter');

function theme_archive_filter()
{

    check_ajax_referer('archive_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

    $category = sanitize_text_field($_POST['category'] ?? 'all');

    $search = sanitize_text_field($_POST['search'] ?? '');

    $sort = sanitize_text_field($_POST['sort'] ?? 'date_desc');

    $args = [

        'post_type' => 'post',

        'post_status' => 'publish',

        'posts_per_page' => 20,

        'paged' => $page,

        's' => $search

    ];

    if ($category != 'all') {

        $args['cat'] = intval($category);

    }

    switch ($sort) {

        case 'date_asc':

            $args['orderby'] = 'date';
            $args['order'] = 'ASC';

            break;

        case 'title':

            $args['orderby'] = 'title';
            $args['order'] = 'ASC';

            break;

        case 'comment':

            $args['orderby'] = 'comment_count';

            break;

        default:

            $args['orderby'] = 'date';
            $args['order'] = 'DESC';

    }

    if (function_exists('pll_current_language')) {

        $args['lang'] = pll_current_language();

    }

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) :

        while ($query->have_posts()) :

            $query->the_post();


            get_template_part('template-parts/article-card');


        endwhile;

    else :

?>

<div class="no-posts">

    پستی پیدا نشد.

</div>

<?php

    endif;

    $posts = ob_get_clean();

    ob_start();

    echo paginate_links([

        'total' => $query->max_num_pages,

        'current' => $page,

        'mid_size' => 2,

        'prev_text' => '«',

        'next_text' => '»'

    ]);

    $pagination = ob_get_clean();

    wp_reset_postdata();

    wp_send_json([

        'posts' => $posts,

        'pagination' => $pagination,

        'count' => $query->found_posts

    ]);

}
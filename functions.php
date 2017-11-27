add_action('wp_enqueue_scripts', 'misha_script_and_styles');

function misha_script_and_styles()
{
    // absolutely need it, because we will get $wp_query->query_vars and $wp_query->max_num_pages from it.
    global $wp_query;

    // when you use wp_localize_script(), do not enqueue the target script immediately
    wp_register_script('misha_scripts', get_stylesheet_directory_uri() . '/script.js', array('jquery'));

    // passing parameters here
    // actually the <script> tag will be created and the object "misha_loadmore_params" will be inside it
    wp_localize_script('misha_scripts', 'misha_loadmore_params', array(
        'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
        'posts' => json_encode($wp_query->query_vars), // everything about your loop is here
        'current_page' => $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1,
        'max_page' => $wp_query->max_num_pages
    ));

    wp_enqueue_script('misha_scripts');
}


add_action('wp_ajax_loadmorebutton', 'misha_loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmorebutton', 'misha_loadmore_ajax_handler');

function misha_loadmore_ajax_handler()
{

    // prepare our arguments for the query
    $params = json_decode(stripslashes($_POST['query']), true); // query_posts() takes care of the necessary sanitization
    //var_dump($params);
    $params['paged'] = $_POST['page'] + 1; // we need next page to be loaded
    $params['post_status'] = 'publish';
    $params['category_name']  = 'long-term-care';
    //$params['post_type']  = 'post';

    //var_dump($_POST['query']);echo'<br>';
    //var_dump($params['paged']);
    // it is always better to use WP_Query but not here
    query_posts($params);

    if (have_posts()) :

        // run the loop
        while (have_posts()): the_post();

            // look into your theme code how the posts are inserted, but you can use your own HTML of course
            // do you remember? - my example is adapted for Twenty Seventeen theme
            //get_template_part( 'template-parts/post/content', get_post_format() );
            // for the test purposes comment the line above and uncomment the below one
             the_title();
             echo "<br>";


    endwhile;
    endif;
    die; // here we exit the script and even no wp_reset_query() required!
}



add_action('wp_ajax_mishafilter', 'misha_filter_function');
add_action('wp_ajax_nopriv_mishafilter', 'misha_filter_function');

function misha_filter_function()
{

    // example: date-ASC
    $order = explode('-', $_POST['misha_order_by']);

    $params = array(
        'posts_per_page' => $_POST['misha_number_of_results'], // when set to -1, it shows all posts
        'orderby' => $order[0], // example: date
        'order' => $order[1], // example: ASC
        'category_name'  => 'long-term-care'
        //'post_type' => 'post'
    );


    query_posts($params);

    global $wp_query;

    if (have_posts()) :

        ob_start(); // start buffering because we do not need to print the posts now

        while (have_posts()): the_post();
        the_title();
            // adapted for Twenty Seventeen theme
            get_template_part('template-parts/post/content', get_post_format());
            echo "<br>";
    endwhile;

    $posts_html = ob_get_contents(); // we pass the posts to variable
        ob_end_clean(); // clear the buffer
    else:
        $posts_html = '<p>Nothing found for your criteria.</p>';
    endif;

    // no wp_reset_query() required

    echo json_encode(array(
        'posts' => serialize($wp_query->query_vars),
        'max_page' => $wp_query->max_num_pages,
        'found_posts' => $wp_query->found_posts,
        'content' => $posts_html
    ));

    die();
}

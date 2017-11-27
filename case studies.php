<?php
/**
 * Template Name: Case Studies
 */

get_header();
?>

<div id="primary" class="content-area">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php
                /*while ( have_posts() ) : the_post();
                endwhile; // End of the loop.*/
                ?>

				<!-- Filters will be here -->
				<form id="misha_filters" action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST">
    				<label for="misha_posts_per_page">Per page</label>
    				<select name="misha_posts_per_page" id="misha_posts_per_page">
    					<option><?php echo get_option( 'posts_per_page' ) ?></option><!-- default value from Settings->Reading -->
    					<option>5</option>
    					<option>10</option>
    					<option value="-1">All</option>
    				</select>

    				<label for="misha_order_by">Order</label>
    				<select name="misha_order_by" id="misha_order_by">
    					<option value="date-DESC">Date &darr;</option><!-- I will explode these values by "-" symbol later -->
    					<option value="date-ASC">Date &uarr;</option>
    					<option value="comment_count-DESC">Comments &darr;</option>
    					<option value="comment_count-ASC">Comments &uarr;</option>
    				</select>

    				<!-- required hidden field for admin-ajax.php -->
    				<input type="hidden" name="action" value="mishafilter" />

    				<button>Apply Filters</button>
    			</form>
				<?php
				//'post_type=case_studies' array('category_name'  => 'case-studies')
                $args = array('category_name'  => 'long-term-care');
				query_posts($args);
			    if ( have_posts() ) :
				?>
				<div id="misha_posts_wrap">
				<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post();
                ?>
					<div class="post_details">
            			<h2 class="post-title-alt"><a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?></a></h2>
                	</div>
            	<?php the_excerpt();
                echo "<br>";
				endwhile;
				?>
				</div>
				<!-- Load more button will be here -->
				<?php

				//var_dump($wp_query);
				if (  $wp_query->max_num_pages > 1 ) :
					echo '<div id="misha_loadmore">More posts</div>'; // you can use <a> as well
				endif;
				endif;
			    ?>

			</div>

		</div>
	</div>
</div><!-- #primary -->


<?php

get_footer();

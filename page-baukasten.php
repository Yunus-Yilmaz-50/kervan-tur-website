<?php
/*
Template Name: Kervan Baukasten
Template Post Type: page
*/
get_header();
?>

<main class="kv-page">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php get_footer(); ?>

<?php get_header(); ?>

<style>
.kervan-page-wrap{ max-width:760px; margin:0 auto; padding:60px 24px 90px; }
.kervan-page-wrap h1{ font-family:'Fraunces',serif; margin-bottom:24px; }
.kervan-page-wrap h2{ font-family:'Fraunces',serif; font-size:1.3rem; margin-top:40px; margin-bottom:10px; }
.kervan-page-wrap h3{ font-size:1.05rem; margin-top:26px; margin-bottom:8px; }
.kervan-page-wrap p, .kervan-page-wrap li{ line-height:1.75; font-size:.95rem; }
.kervan-page-wrap a{ color:var(--clay-dark,#9C462C); }
</style>

<main class="kervan-page-wrap">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article>
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</article>
	<?php endwhile; else : ?>
		<p>İçerik bulunamadı.</p>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
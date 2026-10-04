<?php get_header(); ?>

<style>
.video-hero{ position:relative; height:100vh; overflow:hidden; background:linear-gradient(135deg, var(--ink) 0%, #2A2038 55%, var(--clay-dark) 130%); display:flex; align-items:flex-end; justify-content:center; padding-bottom:64px; margin-top:-64px; }
@media(max-width:640px){ .video-hero{ margin-top:-116px; } }
@media(max-width:640px){
	.video-hero.video-hero--wide{ height:auto; display:block; padding:116px 0 0; }
	.video-hero.video-hero--wide video{ position:relative; inset:auto; display:block; width:100%; height:auto; aspect-ratio:16/9; object-fit:cover; }
	.video-hero.video-hero--wide .btn-wa{ position:absolute; left:50%; bottom:16px; transform:translateX(-50%); font-size:.95rem; padding:13px 26px; white-space:nowrap; }
}
.video-hero video{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
.video-hero::after{ content:''; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,.2) 0%, rgba(0,0,0,.05) 50%, rgba(27,36,56,.55) 100%); }
.video-hero.video-hero--cinema{ background:#000; }
.video-hero.video-hero--cinema video{ object-fit:contain; }
.video-hero.video-hero--cinema::after{ background:linear-gradient(180deg, rgba(0,0,0,0) 70%, rgba(0,0,0,.55) 100%); }
.video-hero .btn-wa{ position:relative; z-index:2; font-size:1.1rem; padding:18px 38px; box-shadow:0 14px 34px -10px rgba(0,0,0,.5); }

.farki-grid{ display:grid; grid-template-columns:1fr 1fr; gap:12px 20px; max-width:900px; margin:20px auto 0; padding:0 20px; }
.farki-item{ display:flex; align-items:center; gap:14px; background:linear-gradient(135deg, var(--ink), var(--ink-2)); color:var(--cream); padding:14px 20px; border-radius:100px; font-size:.86rem; }
@media(max-width:700px){ .farki-grid{ grid-template-columns:1fr; } }
.farki-section{ padding:70px 20px; max-width:1180px; margin:0 auto; position:relative; }
.farki-section::before{
	content:''; position:absolute; top:0; left:50%; width:100vw; height:100%; transform:translateX(-50%); z-index:-1;
	background-image: radial-gradient(rgba(43,32,22,.06) 1.4px, transparent 1.4px);
	background-size: 26px 26px;
	-webkit-mask-image: linear-gradient(180deg, black, transparent 92%);
	mask-image: linear-gradient(180deg, black, transparent 92%);
}
.eyebrow{ font-family:'IBM Plex Mono',monospace; text-transform:uppercase; letter-spacing:.16em; font-size:.7rem; color:var(--gold); }

.hero{ position:relative; background:linear-gradient(135deg, var(--ink) 0%, #2A2038 55%, var(--clay-dark) 130%); padding:70px 20px 60px; text-align:center; color:var(--cream); }
.hero h1{ font-size:clamp(2rem,4.6vw,3.6rem); line-height:1.1; margin:14px 0 16px; }
.hero p{ color:#D9D3C6; max-width:46ch; margin:0 auto 26px; font-size:1rem; line-height:1.6; }
.hero-tagline{ font-family:'Fraunces',serif; font-style:italic; font-size:1.15rem; color:#D9D3C6!important; margin:6px auto 28px!important; }
.btn-wa{ display:inline-flex; align-items:center; gap:7px; background:linear-gradient(135deg, var(--ink) 0%, #2A2038 55%, var(--clay-dark) 130%); color:var(--cream)!important; padding:12px 22px; border-radius:100px; text-decoration:none; font-weight:600; }
.facts-grid{ display:grid; grid-template-columns:repeat(auto-fit, minmax(120px,1fr)); gap:20px; max-width:900px; margin:54px auto 0; padding-top:38px; border-top:1px solid rgba(251,246,214,.15); }
.fact b{ display:block; font-family:'Fraunces',serif; font-size:1.7rem; }
.fact span{ font-size:.76rem; opacity:.75; }
</style>

<?php
$video_url        = kervan_get_option( 'kervan_hero_video' );
$video_url_mobile = kervan_get_option( 'kervan_hero_video_mobile' );
if ( $video_url || $video_url_mobile ) :
	$desktop_src = $video_url ? $video_url : $video_url_mobile;
	$mobile_src  = $video_url_mobile ? $video_url_mobile : $video_url;
?>
<?php $hero_cinema = ( kervan_get_option( 'kervan_hero_mode' ) === 'cinema' ); ?>
<section class="video-hero<?php echo $hero_cinema ? ' video-hero--cinema' : ( ( $desktop_src === $mobile_src ) ? ' video-hero--wide' : '' ); ?>">
	<?php if ( $desktop_src === $mobile_src ) : ?>
	<video autoplay muted loop playsinline><source src="<?php echo esc_url( $desktop_src ); ?>" type="video/mp4"></video>
	<?php else : ?>
	<video id="kervanHeroVideo" autoplay muted loop playsinline></video>
	<script>
	(function(){
		var v = document.getElementById('kervanHeroVideo');
		var mq = window.matchMedia('(max-width:640px)');
		var desktop = <?php echo wp_json_encode( esc_url_raw( $desktop_src ) ); ?>;
		var mobile = <?php echo wp_json_encode( esc_url_raw( $mobile_src ) ); ?>;
		function pick(){
			var src = mq.matches ? mobile : desktop;
			if (v.getAttribute('src') === src) { return; }
			v.setAttribute('src', src);
			v.load();
			var p = v.play();
			if (p && p.catch) { p.catch(function(){}); }
		}
		pick();
		if (mq.addEventListener) { mq.addEventListener('change', pick); } else if (mq.addListener) { mq.addListener(pick); }
	})();
	</script>
	<?php endif; ?>
	<?php if ( kervan_get_option( 'kervan_hero_button_show' ) !== 'hide' ) : ?>
	<a class="btn-wa" href="<?php echo esc_url( get_post_type_archive_link( 'tur' ) ); ?>"><?php echo esc_html( kervan_get_option( 'kervan_hero_button' ) ); ?></a>
	<?php endif; ?>
</section>
<?php endif; ?>

<section class="hero">
	<div class="eyebrow"><?php echo esc_html( kervan_get_option( 'kervan_hero_eyebrow' ) ); ?></div>
	<h1><?php echo esc_html( kervan_get_option( 'kervan_hero_title' ) ); ?></h1>
	<p class="hero-tagline"><?php echo esc_html( kervan_get_option( 'kervan_hero_subtitle' ) ); ?></p>
	<?php if ( ! $video_url && ! $video_url_mobile ) : ?>
	<a class="btn-wa" href="<?php echo esc_url( get_post_type_archive_link( 'tur' ) ); ?>"><?php echo esc_html( kervan_get_option( 'kervan_hero_button' ) ); ?></a>
	<?php endif; ?>

	<div class="facts-grid">
		<?php foreach ( kervan_lines_to_array( kervan_get_option( 'kervan_facts_list' ) ) as $line ) :
			$parts = explode( '|', $line, 2 );
			if ( count( $parts ) < 2 ) continue;
		?>
			<div class="fact">
				<b><?php echo esc_html( trim( $parts[0] ) ); ?></b>
				<span><?php echo esc_html( trim( $parts[1] ) ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="farki-section">
	<div style="text-align:center; margin-bottom:20px;">
		<div class="eyebrow" style="color:var(--clay-dark);"><?php echo esc_html( 'KERVAN KÜLTÜR TURLARI FARKI' ); ?></div>
		<h2 style="font-family:'Fraunces',serif;"><?php echo esc_html( 'Bizi farklı kılan neler?' ); ?></h2>
	</div>
	<div class="farki-grid">
		<?php foreach ( kervan_lines_to_array( kervan_get_option( 'kervan_farki_items' ) ) as $item ) : ?>
			<div class="farki-item"><?php echo esc_html( $item ); ?></div>
		<?php endforeach; ?>
	</div>
</section>

<?php
$extra_page_id = kervan_get_option( 'kervan_extra_page_id' );
if ( $extra_page_id ) :
	$extra_page = get_post( $extra_page_id );
	if ( $extra_page && $extra_page->post_status === 'publish' ) :
?>
<section class="wrap" style="padding:60px 20px; max-width:1180px; margin:0 auto;">
	<?php echo apply_filters( 'the_content', $extra_page->post_content ); ?>
</section>
<?php
	endif;
endif;
?>

<?php get_footer(); ?>

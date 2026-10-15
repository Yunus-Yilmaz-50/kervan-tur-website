<?php get_header(); ?>

<main style="max-width:560px; margin:0 auto; padding:70px 24px 90px; text-align:center;">
	<div style="font-size:3rem; margin-bottom:6px;">🧭</div>
	<h1 style="font-family:var(--font-head); margin:0 0 12px;"><?php echo esc_html( kervan_t( 'Aradığınız sayfa bulunamadı' ) ); ?></h1>
	<p style="line-height:1.7; opacity:.8; margin:0 0 28px;"><?php echo esc_html( kervan_t( 'Bu bağlantı eski web sitemize ait olabilir. Aşağıdaki bağlantılardan devam edebilirsiniz.' ) ); ?></p>
	<div style="display:flex; flex-wrap:wrap; gap:12px; justify-content:center;">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="background:var(--ink,#1B2438); color:#fff; padding:13px 26px; border-radius:100px; text-decoration:none; font-weight:700; font-size:.92rem;"><?php echo esc_html( kervan_t( 'Ana Sayfa' ) ); ?></a>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'tur' ) ); ?>" style="background:var(--clay,#BD5B3B); color:#fff; padding:13px 26px; border-radius:100px; text-decoration:none; font-weight:700; font-size:.92rem;"><?php echo esc_html( kervan_t( 'Turlar' ) ); ?></a>
		<a href="<?php echo esc_url( home_url( '/afisler/' ) ); ?>" style="background:var(--clay,#BD5B3B); color:#fff; padding:13px 26px; border-radius:100px; text-decoration:none; font-weight:700; font-size:.92rem;"><?php echo esc_html( kervan_t( 'Afişler' ) ); ?></a>
		<a href="<?php echo esc_url( home_url( '/iletisim/' ) ); ?>" style="background:var(--clay,#BD5B3B); color:#fff; padding:13px 26px; border-radius:100px; text-decoration:none; font-weight:700; font-size:.92rem;"><?php echo esc_html( kervan_t( 'İletişim' ) ); ?></a>
	</div>
</main>

<?php get_footer(); ?>

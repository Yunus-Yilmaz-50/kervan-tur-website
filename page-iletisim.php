<?php
/* Template Name: İletişim */
get_header();
?>

<div class="wrap" style="max-width:820px; margin:0 auto; padding:60px 28px 90px; text-align:center;">
	<div class="eyebrow" style="font-family:'IBM Plex Mono',monospace; text-transform:uppercase; letter-spacing:.16em; font-size:.72rem; color:var(--clay-dark);"><?php echo esc_html( kervan_get_option( 'kervan_iletisim_eyebrow' ) ); ?></div>
	<h1 style="font-family:var(--font-head);"><?php echo esc_html( kervan_get_option( 'kervan_iletisim_baslik' ) ); ?></h1>
	<p style="font-family:var(--font-head); font-style:italic; color:var(--clay-dark); max-width:52ch; margin:0 auto 34px;"><?php echo esc_html( kervan_get_option( 'kervan_hizmet_text' ) ); ?></p>

	<?php echo kervan_render_info_boxes( kervan_get_option( 'kervan_iletisim_kutular' ) ); ?>

	<div style="margin-top:44px; padding-top:28px; border-top:1px solid var(--line); text-align:left;">
		<h4 style="font-family:var(--font-head); font-size:.95rem; opacity:.8; margin-bottom:8px;"><?php echo esc_html( kervan_t( 'Web sitesiyle ilgili bir sorun mu var?' ) ); ?></h4>
		<?php if ( isset( $_GET['bildirim'] ) && $_GET['bildirim'] === 'ok' ) : ?>
			<p style="color:var(--teal); font-size:.88rem;"><?php echo esc_html( kervan_t( 'Teşekkürler, bildiriminiz alındı.' ) ); ?></p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:520px;">
			<input type="hidden" name="action" value="kervan_report_issue">
			<?php wp_nonce_field( 'kervan_report_issue', 'kervan_report_nonce' ); ?>
			<input type="text" name="kervan_hp" style="position:absolute; left:-9999px;" tabindex="-1" autocomplete="off">
			<textarea name="kervan_report_message" required rows="3" placeholder="<?php echo esc_attr( kervan_t( 'Karşılaştığınız sorunu kısaca açıklayın…' ) ); ?>" style="width:100%; padding:10px 14px; border-radius:10px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; background:var(--cream);"></textarea>
			<button type="submit" style="margin-top:10px; background:var(--ink); color:#fff; border:none; padding:9px 20px; border-radius:100px; font-weight:600; font-size:.84rem; cursor:pointer;"><?php echo esc_html( kervan_t( 'Gönder' ) ); ?></button>
		</form>
	</div>
</div>

<?php get_footer(); ?>

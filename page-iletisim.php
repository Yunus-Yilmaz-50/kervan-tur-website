<?php
/* Template Name: İletişim */
get_header();
?>

<div class="wrap" style="max-width:820px; margin:0 auto; padding:60px 28px 90px; text-align:center;">
	<div class="eyebrow" style="font-family:'IBM Plex Mono',monospace; text-transform:uppercase; letter-spacing:.16em; font-size:.72rem; color:var(--clay-dark);"><?php echo esc_html( 'İLETİŞİM' ); ?></div>
	<h1 style="font-family:'Fraunces',serif;"><?php echo esc_html( 'Bize Ulaşın' ); ?></h1>
	<p style="font-family:'Fraunces',serif; font-style:italic; color:var(--clay-dark); max-width:52ch; margin:0 auto 34px;"><?php echo esc_html( kervan_get_option( 'kervan_hizmet_text' ) ); ?></p>

	<div style="background:var(--sand); border-radius:16px; padding:34px; margin-bottom:24px; text-align:left;">
		<h3>Kervan Kültür Turlari</h3>
		<p style="line-height:2;">
			<?php echo esc_html( 'E-posta:' ); ?> <?php echo esc_html( kervan_get_option( 'kervan_email' ) ); ?><br>
			Instagram: <a href="<?php echo esc_url( kervan_get_option( 'kervan_instagram' ) ); ?>" target="_blank"><?php echo esc_html( kervan_get_option( 'kervan_instagram_handle' ) ); ?></a><br>
			Facebook: <a href="<?php echo esc_url( kervan_get_option( 'kervan_facebook' ) ); ?>" target="_blank"><?php echo esc_html( kervan_get_option( 'kervan_facebook_name' ) ); ?></a><br>
			Google: <a href="<?php echo esc_url( kervan_get_option( 'kervan_google_url' ) ); ?>" target="_blank">Kervan Kültür Turlari</a><br>
			WhatsApp-Kanal: <a href="<?php echo esc_url( kervan_get_option( 'kervan_wa_channel' ) ); ?>" target="_blank">Kervan Kültür Turlari</a><br>
			<?php echo esc_html( 'Avrupa Turları:' ); ?> <a href="<?php echo esc_url( kervan_get_option( 'kervan_avrupatur_url' ) ); ?>" target="_blank">avrupatur.de</a>
		</p>
	</div>

	<div style="background:var(--sand); border-radius:16px; padding:34px; text-align:left;">
		<h3 style="font-weight:400;">Marti Turizm</h3>
		<p style="line-height:2;">
			<?php echo esc_html( 'Sahibinin Adı:' ); ?> Marti Havacilik Seyahat Turizm Organizasyon ve Eğitim Hizmetleri San. Tic. Ltd. Şti.<br>
			<?php echo esc_html( 'Adresi:' ); ?> Hunat Mahallesi Sivas Blv. No:2 B, Melikgazi / Kayseri<br>
			<?php echo esc_html( 'Merkezi:' ); ?> Kayseri / Türkiye<br>
			TÜRSAB <?php echo 'Belge No:'; ?> 7483<br>
			<?php echo esc_html( 'Markamız:' ); ?> Kervan Kültür Turlari<br>
			<?php echo esc_html( 'Telefon:' ); ?> <?php echo esc_html( kervan_get_option( 'kervan_phone' ) ); ?>
		</p>
	</div>

	<div style="margin-top:44px; padding-top:28px; border-top:1px solid var(--line); text-align:left;">
		<h4 style="font-family:'Fraunces',serif; font-size:.95rem; opacity:.8; margin-bottom:8px;">Web sitesiyle ilgili bir sorun mu var?</h4>
		<?php if ( isset( $_GET['bildirim'] ) && $_GET['bildirim'] === 'ok' ) : ?>
			<p style="color:var(--teal); font-size:.88rem;">Teşekkürler, bildiriminiz alındı.</p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:520px;">
			<input type="hidden" name="action" value="kervan_report_issue">
			<?php wp_nonce_field( 'kervan_report_issue', 'kervan_report_nonce' ); ?>
			<input type="text" name="kervan_hp" style="position:absolute; left:-9999px;" tabindex="-1" autocomplete="off">
			<textarea name="kervan_report_message" required rows="3" placeholder="Karşılaştığınız sorunu kısaca açıklayın…" style="width:100%; padding:10px 14px; border-radius:10px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; background:var(--cream);"></textarea>
			<button type="submit" style="margin-top:10px; background:var(--ink); color:#fff; border:none; padding:9px 20px; border-radius:100px; font-weight:600; font-size:.84rem; cursor:pointer;">Gönder</button>
		</form>
	</div>
</div>

<?php get_footer(); ?>

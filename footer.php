	<style>
		.kervan-footer{ background:linear-gradient(135deg, var(--ink,#1B2438) 0%, #2A2038 55%, var(--clay-dark,#9C462C) 130%); color:#fff; padding:34px 20px; margin-top:0; }
		.kervan-footer .wrap{ max-width:1180px; margin:0 auto; }
		.kervan-footer-row{ display:flex; flex-wrap:wrap; gap:10px 26px; justify-content:center; font-size:.86rem; margin-bottom:18px; }
		.kervan-footer-row a{ color:#fff; text-decoration:none; }
		.kervan-footer-row a:hover{ text-decoration:underline; }
		.kervan-footer-bottom{ display:flex; justify-content:center; flex-wrap:wrap; gap:10px; padding-top:16px; border-top:1px solid rgba(255,255,255,.12); font-size:.76rem; opacity:.8; text-align:center; }
		@media(max-width:640px){ .kervan-footer-bottom{ flex-direction:column; } }
	</style>

	<footer class="kervan-footer">
		<div class="wrap">
			<div class="kervan-footer-row">
				<span><?php echo esc_html( kervan_get_option( 'kervan_email' ) ); ?></span>
				<a href="<?php echo esc_url( kervan_get_option( 'kervan_instagram' ) ); ?>" target="_blank">Instagram</a>
				<a href="<?php echo esc_url( kervan_get_option( 'kervan_facebook' ) ); ?>" target="_blank">Facebook</a>
				<a href="<?php echo esc_url( kervan_get_option( 'kervan_wa_channel' ) ); ?>" target="_blank">WhatsApp-Kanal</a>
				<span><?php echo esc_html( 'Avrupa Turları:' ); ?> <a href="<?php echo esc_url( kervan_get_option( 'kervan_avrupatur_url' ) ); ?>" target="_blank">avrupatur.de</a></span>
			</div>

			<?php
			$kervan_legal_pages = array(
				'gizlilik-politikasi' => 'Gizlilik Politikası',
				'hizmet-sartlari'     => 'Hizmet Şartları',
			);
			$kervan_legal_links = array();
			foreach ( $kervan_legal_pages as $slug => $label ) {
				$p = get_page_by_path( $slug );
				if ( $p && $p->post_status === 'publish' ) {
					$kervan_legal_links[] = '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $label ) . '</a>';
				}
			}
			if ( $kervan_legal_links ) :
			?>
			<div class="kervan-footer-row" style="margin-bottom:8px; font-size:.78rem; opacity:.85;">
				<?php echo implode( '<span style="opacity:.4;">·</span>', $kervan_legal_links ); ?>
			</div>
			<?php endif; ?>

			<div class="kervan-footer-bottom">
				<span>© <?php echo esc_html( date( 'Y' ) ); ?> Kervan Kültür Turlari</span>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>

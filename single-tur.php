<?php get_header(); ?>

<style>
.detail-wrap{ max-width:900px; margin:0 auto; padding:50px 20px 70px; }
.back-link{ display:inline-flex; align-items:center; gap:6px; text-decoration:none; color:var(--charcoal); opacity:.7; font-size:.85rem; font-weight:600; margin-bottom:18px; padding:8px 16px; border:1px solid var(--line); border-radius:100px; }
.back-link:hover{ opacity:1; border-color:var(--clay); color:var(--clay); }

.hero-slider{ position:relative; aspect-ratio:16/7; border-radius:14px; overflow:hidden; margin-bottom:0; background:linear-gradient(135deg, var(--clay), var(--clay-dark)); cursor:pointer; }
.hero-slider::before{ content:'⤢'; position:absolute; top:14px; right:14px; z-index:3; width:34px; height:34px; border-radius:50%; background:rgba(0,0,0,.4); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1rem; pointer-events:none; opacity:.85; }
.hero-slide{ position:absolute; inset:0; background-size:cover; background-position:center; opacity:0; transition:opacity .4s ease; }
.hero-slide.active{ opacity:1; }
.hero-arrow{ position:absolute; bottom:14px; width:34px; height:34px; border-radius:50%; background:rgba(0,0,0,.45); color:#fff; border:none; cursor:pointer; font-size:1rem; display:flex; align-items:center; justify-content:center; }
.hero-arrow.prev{ right:56px; }
.hero-arrow.next{ right:14px; }
.hero-dots{ position:absolute; bottom:20px; left:16px; display:flex; gap:5px; }
.hero-dots span{ width:6px; height:6px; border-radius:50%; background:rgba(255,255,255,.5); }
.hero-dots span.active{ background:#fff; }

.content-inner{ padding:0 12px; }
.top-wash{ background:linear-gradient(180deg, var(--sand) 0%, rgba(243,232,214,0) 100%); padding-top:18px; padding-bottom:4px; margin-bottom:4px; }
.top-wash .content-inner{ padding-top:0; padding-bottom:0; }

.title-row{ display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:nowrap; }
.title-row > div:first-child{ flex:1; min-width:0; }
.title-row .price-block{ flex-shrink:0; }
.title-row h1{ font-family:'Fraunces',serif; margin:0; }
.pins-days{ opacity:.75; font-size:.88rem; margin-top:6px; }
.place-block{ margin-top:14px; }
.place-block h4{ font-family:'Fraunces',serif; font-size:.98rem; font-weight:600; margin:0 0 6px; }
.place-list{ list-style:none; margin:0 0 4px; padding:0 0 0 14px; font-size:.9rem; line-height:1.85; }
.place-list li{ position:relative; padding-left:24px; }
.place-list li::before{ content:'–'; position:absolute; left:0; color:var(--gold); font-weight:700; }
.place-list li.place-note{ font-size:.82rem; font-style:italic; opacity:.7; line-height:1.6; margin-top:2px; }
.place-list li.place-note::before{ content:none; }
.price-block{ text-align:right; }
.days-label{ font-family:'Fraunces',serif; font-size:1.15rem; font-weight:600; color:var(--charcoal); margin-bottom:2px; }
.price-block .price{ font-family:'Fraunces',serif; font-size:1.7rem; font-weight:700; display:block; }
.price-line{ display:flex; align-items:baseline; justify-content:flex-end; gap:6px; }
.price-line small{ font-size:.78rem; font-weight:400; opacity:.65; }
.price-block .badge{ margin-top:4px; text-align:right; font-size:.8rem; font-weight:700; color:#E00000; }

.section-eyebrow{ font-family:'IBM Plex Mono',monospace; text-transform:uppercase; letter-spacing:.1em; font-size:.8rem; font-weight:700; color:var(--clay-dark); margin-bottom:8px; display:block; }

.inc-section{ margin-top:26px; position:relative; }
.inc-section::before{ content:''; position:absolute; top:-14px; left:-12%; width:124%; height:1px; background:linear-gradient(90deg, transparent, rgba(43,32,22,.06) 15%, rgba(43,32,22,.06) 85%, transparent); }
.inc-columns{ display:grid; grid-template-columns:1fr 1fr; gap:0; margin-top:12px; }
.inc-columns > div{ padding:0 22px; }
.inc-columns > div:first-child{ padding-left:0; }
.inc-columns h4{ font-family:'IBM Plex Mono',monospace; text-transform:uppercase; letter-spacing:.1em; font-size:.8rem; font-weight:700; color:var(--clay-dark); margin-bottom:10px; }
.inc-columns ul{ list-style:none; margin:0; padding:0; font-size:.87rem; line-height:1.9; }
.inc-columns li::before{ content:'✓'; color:var(--teal); font-weight:700; margin-right:8px; }
.inc-columns ul.not li::before{ content:'✕'; color:#C0392B; font-weight:700; margin-right:8px; }
@media(max-width:640px){ .inc-columns{grid-template-columns:1fr;} .inc-columns > div:first-child{padding-bottom:16px; margin-bottom:10px;} .inc-columns > div{padding-left:0;} }
@media(max-width:480px){
	.title-row{ flex-wrap:wrap; }
	.title-row .price-block{ flex-basis:100%; text-align:left; margin-top:10px; }
	.title-row .price-block .badge{ text-align:left; }
	.title-row .price-block .price-line{ justify-content:flex-start; }
}

.notes-callout{ margin-top:32px; border-left:3px solid var(--gold); padding:4px 20px; font-size:.86rem; line-height:1.8; position:relative; }
.notes-callout::before{ content:''; position:absolute; top:-16px; left:-12%; width:124%; height:1px; background:linear-gradient(90deg, transparent, rgba(43,32,22,.035) 15%, rgba(43,32,22,.035) 85%, transparent); }
.notes-callout h4{ font-family:'Fraunces',serif; font-size:.9rem; margin:0 0 6px; }

.res-card{ margin-top:40px; background:var(--ink); color:var(--cream); border-radius:16px; padding:30px; }
.res-card h4{ font-family:'Fraunces',serif; font-size:1.1rem; margin-bottom:18px; }
.res-contact{ display:flex; align-items:center; gap:22px; padding:10px 0; border-bottom:1px solid rgba(255,255,255,.12); font-size:.92rem; flex-wrap:wrap; }
@media(max-width:480px){ .res-contact{ font-size:.84rem; gap:10px 16px; } }
.res-contact:last-of-type{ border-bottom:none; }
.res-contact a{ color:var(--gold); text-decoration:none; font-weight:600; }
.res-howto-link{ color:#C2664A!important; text-decoration:none!important; font-weight:600!important; font-size:.84rem; }
.res-links{ margin-top:16px; display:flex; gap:18px; flex-wrap:wrap; font-size:.86rem; }
.res-links a{ color:var(--cream); opacity:.9; text-decoration:underline; }
.res-links a:hover{ opacity:1; }
.res-bottom-row{ margin-top:20px; display:flex; gap:14px; flex-wrap:wrap; }
.res-bottom-row a{ display:inline-block; padding:9px 18px; border-radius:100px; text-decoration:none; font-weight:600; font-size:.84rem; }
.res-flyer{ background:var(--gold); color:var(--ink)!important; }
.res-review{ border:1px solid rgba(255,255,255,.3); color:var(--cream)!important; }

.related-wrap{ position:relative; margin-top:36px; padding-top:22px; }
.related-wrap::before{ content:''; position:absolute; top:0; left:-12%; width:124%; height:1px; background:linear-gradient(90deg, transparent, rgba(43,32,22,.06) 15%, rgba(43,32,22,.06) 85%, transparent); }
.related-scroll{ display:flex; gap:16px; overflow-x:auto; padding-bottom:10px; scroll-snap-type:x proximity; -webkit-overflow-scrolling:touch; scroll-behavior:smooth; }
.related-scroll::-webkit-scrollbar{ display:none; }
.related-scroll .ticket{ scroll-snap-align:start; flex:0 0 260px; }
.related-arrow{ position:absolute; top:50%; transform:translateY(-10px); width:36px; height:36px; border-radius:50%; background:var(--cream); border:1px solid var(--line); cursor:pointer; box-shadow:0 6px 16px rgba(0,0,0,.12); z-index:2; }
.related-arrow.prev{ left:-14px; }
.related-arrow.next{ right:-14px; }

/* Tourkarten identisch zur Turlar-Übersicht */
.ticket{ background:var(--cream); border-radius:10px; overflow:hidden; box-shadow:0 18px 40px -20px rgba(27,36,56,.35); display:flex; flex-direction:column; text-decoration:none; color:inherit; }
.ticket-top{ padding:22px 20px 32px; color:#fff; position:relative; min-height:150px; display:flex; flex-direction:column; justify-content:flex-end; background-size:cover; background-position:center; }
.ticket-top::after{ content:''; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,0) 30%, rgba(0,0,0,.65) 100%); z-index:0; }
.ticket-top > *{ position:relative; z-index:1; }
.ticket-top h3{ font-size:1.3rem; font-weight:700; margin:0; text-shadow:0 2px 6px rgba(0,0,0,.5); color:#fff; }
.ticket-body{ padding:16px 18px 18px; display:flex; flex-direction:column; gap:10px; flex:1; }
.ticket-dates{ font-size:.78rem; opacity:.75; }
.ticket-pins{ font-size:.78rem; opacity:.75; }
.pins-extra{ opacity:.55; margin-left:2px; }
.ticket-foot{ display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid var(--line); gap:10px; margin-top:auto; }
.price-inline{ display:flex; align-items:baseline; gap:8px; }
.price-inline .price{ font-family:'Fraunces',serif; font-weight:700; font-size:1.15rem; }
.price-inline .status{ font-size:.74rem; font-weight:700; color:#E00000; }
.days-badge{ background:var(--clay); color:#fff; font-size:.76rem; font-weight:600; padding:8px 14px; border-radius:100px; white-space:nowrap; }
.btn-wa-sm{ background:var(--clay); color:#fff!important; padding:8px 14px; border-radius:100px; text-decoration:none; font-size:.78rem; font-weight:600; white-space:nowrap; }
</style>

<?php while ( have_posts() ) : the_post();
	$price       = get_post_meta( get_the_ID(), 'kervan_price', true );
	$days        = get_post_meta( get_the_ID(), 'kervan_days', true );
	$badge       = get_post_meta( get_the_ID(), 'kervan_badge', true );
	$pins        = kervan_places_pins( get_the_ID() );
	$dates       = kervan_lines_to_array( get_post_meta( get_the_ID(), 'kervan_dates', true ) );
	$included    = kervan_lines_to_array( get_post_meta( get_the_ID(), 'kervan_included', true ) );
	$notincluded = kervan_lines_to_array( get_post_meta( get_the_ID(), 'kervan_not_included', true ) );
	$notes       = kervan_lines_to_array( get_post_meta( get_the_ID(), 'kervan_notes', true ) );
	$whatsapp    = kervan_get_option( 'kervan_whatsapp' );
	$wa_text     = rawurlencode( 'Merhaba, "' . get_the_title() . '" turu hakkında bilgi istiyorum.' );

	$gallery_ids = get_post_meta( get_the_ID(), '_kervan_gallery', true );
	$slide_ids = $gallery_ids ? explode( ',', $gallery_ids ) : array();
	if ( has_post_thumbnail() && ! in_array( get_post_thumbnail_id(), $slide_ids ) ) {
		array_unshift( $slide_ids, get_post_thumbnail_id() );
	}
?>
<div class="detail-wrap">
	<?php
	$tour_years = array_filter( array_map( 'trim', explode( ',', get_post_meta( get_the_ID(), 'kervan_year', true ) ) ) );
	$back_year = $tour_years ? $tour_years[0] : '';
	?>
	<a href="<?php echo esc_url( get_post_type_archive_link( 'tur' ) ); ?>#yil-<?php echo esc_attr( $back_year ); ?>" class="back-link">← <?php echo esc_html( 'Turlara Dön' ); ?></a>

	<div class="hero-slider" id="heroSlider" onclick="kervanOpenLightbox()">
		<?php if ( $slide_ids ) : foreach ( $slide_ids as $i => $sid ) :
			$url = wp_get_attachment_image_url( $sid, 'large' );
		?>
			<div class="hero-slide <?php echo $i===0?'active':''; ?>" style="background-image:url(<?php echo esc_url( $url ); ?>)"></div>
		<?php endforeach; endif; ?>
		<?php if ( count( $slide_ids ) > 1 ) : ?>
			<button class="hero-arrow prev" onclick="event.stopPropagation(); heroMove(-1)">‹</button>
			<button class="hero-arrow next" onclick="event.stopPropagation(); heroMove(1)">›</button>
			<div class="hero-dots">
				<?php foreach ( $slide_ids as $i => $sid ) : ?><span class="<?php echo $i===0?'active':''; ?>"></span><?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $slide_ids ) : ?>
	<div id="kervanLightbox" onclick="if(event.target===this) kervanCloseLightbox();" style="display:none; position:fixed; inset:0; z-index:300; background:rgba(0,0,0,.92); align-items:center; justify-content:center;">
		<button onclick="kervanCloseLightbox()" style="position:absolute; top:18px; right:20px; background:none; border:none; color:#fff; font-size:2rem; cursor:pointer; line-height:1;">✕</button>
		<button onclick="kervanLightboxMove(-1)" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:none; color:#fff; font-size:1.6rem; width:44px; height:44px; border-radius:50%; cursor:pointer;">‹</button>
		<img id="kervanLightboxImg" style="max-width:92vw; max-height:85vh; object-fit:contain; border-radius:6px;">
		<button onclick="kervanLightboxMove(1)" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:none; color:#fff; font-size:1.6rem; width:44px; height:44px; border-radius:50%; cursor:pointer;">›</button>
	</div>
	<script>
	const kervanHeroImages = [<?php foreach ( $slide_ids as $sid ) { echo '"' . esc_url( wp_get_attachment_image_url( $sid, 'full' ) ) . '",'; } ?>];
	function kervanOpenLightbox(){
		document.getElementById('kervanLightboxImg').src = kervanHeroImages[heroIdx];
		document.getElementById('kervanLightbox').style.display = 'flex';
	}
	function kervanCloseLightbox(){ document.getElementById('kervanLightbox').style.display = 'none'; }
	function kervanLightboxMove(dir){
		heroMove(dir);
		document.getElementById('kervanLightboxImg').src = kervanHeroImages[heroIdx];
	}
	</script>
	<?php endif; ?>

	<div class="top-wash">
	<div class="content-inner">
		<div class="title-row">
			<div>
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="price-block">
				<?php if ( $days ) : ?><div class="days-label"><?php echo esc_html( $days ) . ' ' . esc_html( 'GÜN' ); ?></div><?php endif; ?>
				<div class="price-line"><span class="price"><?php echo esc_html( $price ); ?></span> <small><?php echo esc_html( 'kişi başı' ); ?></small></div>
				<?php if ( $badge ) : ?><div class="badge<?php echo kervan_badge_is_full( $badge ) ? ' badge-full' : ''; ?>"><?php echo esc_html( $badge ); ?></div><?php endif; ?>
			</div>
		</div>

		<?php if ( $dates ) : ?>
		<span class="section-eyebrow" style="margin-top:0px;"><?php echo esc_html( 'Tarihler' ); ?></span>
		<p style="margin-top:0;"><?php echo implode( '<br>', array_map( 'esc_html', $dates ) ); ?></p>
		<?php endif; ?>

		<?php
		$places = kervan_parse_places( get_post_meta( get_the_ID(), 'kervan_places', true ) );
		if ( $places ) :
		?>
		<span class="section-eyebrow" style="margin-top:38px;">Gezilecek Yerler</span>
		<?php foreach ( $places as $place_name => $items ) : ?>
			<div class="place-block">
				<h4>📍 <?php echo esc_html( $place_name ); ?></h4>
				<?php if ( $items ) : ?>
				<ul class="place-list">
					<?php foreach ( $items as $item ) : ?>
						<?php if ( is_array( $item ) ) : ?><li class="place-note"><?php echo esc_html( $item['note'] ); ?></li>
						<?php else : ?><li><?php echo esc_html( $item ); ?></li><?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
	</div>

	<?php echo apply_filters( 'the_content', get_the_content() ); ?>

	<?php if ( $included || $notincluded ) : ?>
	<div class="inc-section">
	<div class="content-inner">
		<div class="inc-columns">
			<?php if ( $included ) : ?><div><h4><?php echo esc_html( 'Fiyata Dahil' ); ?></h4><ul><?php foreach ( $included as $i ) echo '<li>' . esc_html( $i ) . '</li>'; ?></ul></div><?php endif; ?>
			<?php if ( $notincluded ) : ?><div><h4><?php echo esc_html( 'Dahil Olmayan' ); ?></h4><ul class="not"><?php foreach ( $notincluded as $i ) echo '<li>' . esc_html( $i ) . '</li>'; ?></ul></div><?php endif; ?>
		</div>
	</div>
	</div>
	<?php endif; ?>

	<?php if ( $notes ) : ?>
	<div class="notes-callout">
		<h4>🚨 <?php echo 'Not'; ?></h4>
		<?php foreach ( $notes as $n ) echo '<div>• ' . esc_html( $n ) . '</div>'; ?>
	</div>
	<?php endif; ?>

	<?php
	$group = get_post_meta( get_the_ID(), 'kervan_group', true );
	$group_list = $group ? explode( ',', $group ) : array( 'turkiye' );
	$contacts = array();
	if ( in_array( 'turkiye', $group_list, true ) ) {
		$contacts[] = array( kervan_get_option( 'kervan_contact1_name' ), kervan_get_option( 'kervan_contact1_phone' ) );
	}
	if ( in_array( 'dunya', $group_list, true ) ) {
		$contacts[] = array( kervan_get_option( 'kervan_contact2_name' ), kervan_get_option( 'kervan_contact2_phone' ) );
		$contacts[] = array( kervan_get_option( 'kervan_contact3_name' ), kervan_get_option( 'kervan_contact3_phone' ) );
	}
	$flyer_id = get_post_meta( get_the_ID(), '_kervan_flyer', true );
	$howto_key = in_array( 'dunya', $group_list, true ) ? 'kervan_kayit_bilgi_image_dunya' : 'kervan_kayit_bilgi_image_tr';
	$howto_img = kervan_get_option( $howto_key );
	?>
	<div class="res-card">
		<h4><?php echo 'Rezervasyon & Bilgi'; ?></h4>
		<?php foreach ( $contacts as $c ) :
			$digits = preg_replace( '/\D/', '', $c[1] );
		?>
			<div class="res-contact">
				<span><?php echo esc_html( $c[0] ); ?>&nbsp;&nbsp;<?php echo esc_html( $c[1] ); ?></span>
				<span style="display:flex; align-items:center; gap:14px;">
					<?php if ( $howto_img ) : ?>
					<a href="#" onclick="document.getElementById('kervanHowtoModal').style.display='flex'; return false;">Kayıt Formu</a>
					<?php endif; ?>
					<a href="#" class="res-howto-link" onclick="kervanOpenKayitForm('<?php echo esc_js( $digits ); ?>'); return false;">Kayıt Ol</a>
				</span>
			</div>
		<?php endforeach; ?>
		<div class="res-links">
			<?php if ( kervan_get_option( 'kervan_instagram' ) ) : ?><a href="<?php echo esc_url( kervan_get_option( 'kervan_instagram' ) ); ?>" target="_blank">Instagram</a><?php endif; ?>
			<?php if ( kervan_get_option( 'kervan_facebook' ) ) : ?><a href="<?php echo esc_url( kervan_get_option( 'kervan_facebook' ) ); ?>" target="_blank">Facebook</a><?php endif; ?>
			<?php if ( kervan_get_option( 'kervan_wa_channel' ) ) : ?><a href="<?php echo esc_url( kervan_get_option( 'kervan_wa_channel' ) ); ?>" target="_blank">WhatsApp-Kanal</a><?php endif; ?>
		</div>
		<div class="res-bottom-row">
			<?php if ( $flyer_id ) : ?><a class="res-flyer" href="<?php echo esc_url( wp_get_attachment_url( $flyer_id ) ); ?>" target="_blank">📄 <?php echo 'Tur Afişi İndir'; ?></a><?php endif; ?>
			<a class="res-review" href="<?php echo esc_url( kervan_get_option( 'kervan_google_url' ) ); ?>" target="_blank"><?php echo 'Yorum Yap'; ?></a>
		</div>
	</div>

	<?php
	$same_group_terms = wp_get_post_terms( get_the_ID(), 'kita', array( 'fields' => 'ids' ) );
	$related = array();
	if ( $same_group_terms ) {
		$related = get_posts( array(
			'post_type'      => 'tur',
			'posts_per_page' => -1,
			'post__not_in'   => array( get_the_ID() ),
			'tax_query'      => array( array(
				'taxonomy' => 'kita',
				'field'    => 'term_id',
				'terms'    => $same_group_terms,
			) ),
		) );
	}
	// Restliche Touren anhängen, damit am Ende alle verfügbaren Touren gezeigt werden
	$exclude_ids = array_merge( array( get_the_ID() ), wp_list_pluck( $related, 'ID' ) );
	$fill = get_posts( array(
		'post_type'      => 'tur',
		'posts_per_page' => -1,
		'post__not_in'   => $exclude_ids,
	) );
	$related = array_merge( $related, $fill );
	// Nur Touren desselben Jahres, und nie Touren mit Rozet DOLU/DOLDU
	$cur_years = array_filter( array_map( 'trim', explode( ',', get_post_meta( get_the_ID(), 'kervan_year', true ) ) ) );
	$related = array_values( array_filter( $related, function( $rp ) use ( $cur_years ) {
		$rb = get_post_meta( $rp->ID, 'kervan_badge', true );
		if ( kervan_badge_is_full( $rb ) || kervan_badge_is_past( $rb ) ) return false;
		if ( $cur_years ) {
			$ry = array_filter( array_map( 'trim', explode( ',', get_post_meta( $rp->ID, 'kervan_year', true ) ) ) );
			if ( ! array_intersect( $cur_years, $ry ) ) return false;
		}
		return true;
	} ) );
	if ( $related ) :
	?>
	<div class="related-wrap">
		<span class="section-eyebrow"><?php echo esc_html( 'İlgili Turlar' ); ?></span>
		<div class="related-scroll" id="relatedScroll">
			<?php foreach ( $related as $r ) :
				$r_id = $r->ID;
				$r_img = has_post_thumbnail( $r_id ) ? get_the_post_thumbnail_url( $r_id, 'medium_large' ) : '';
				$r_bg = $r_img ? 'background-image:url(' . esc_url( $r_img ) . ')' : 'background:linear-gradient(135deg, var(--clay), var(--clay-dark))';
				$r_days = get_post_meta( $r_id, 'kervan_days', true );
				$r_price = get_post_meta( $r_id, 'kervan_price', true );
				$r_badge = get_post_meta( $r_id, 'kervan_badge', true );
				$r_pins = kervan_places_pins( $r_id );
				$r_dates = kervan_lines_to_array( get_post_meta( $r_id, 'kervan_dates', true ) );
				$pins_list = array_filter( array_map( 'trim', preg_split( '/[·,]/', $r_pins ) ) );
				$pins_txt = implode( ' · ', array_slice( $pins_list, 0, 4 ) ) . ( count( $pins_list ) > 4 ? ' +' . ( count( $pins_list ) - 4 ) : '' );
			?>
			<a href="<?php echo esc_url( get_permalink( $r_id ) ); ?>" class="ticket" style="flex:0 0 260px; scroll-snap-align:start;">
				<div class="ticket-top" style="<?php echo $r_bg; ?>">
					<h3><?php echo esc_html( get_the_title( $r_id ) ); ?></h3>
				</div>
				<div class="ticket-body">
					<?php if ( $r_dates ) : ?><div class="ticket-dates">📅 <?php echo esc_html( $r_dates[0] ); ?><?php if ( count( $r_dates ) > 1 ) echo ' <span class="pins-extra">+' . ( count( $r_dates ) - 1 ) . '</span>'; ?></div><?php endif; ?>
					<?php if ( $pins_txt ) : ?><div class="ticket-pins">📍 <?php echo esc_html( $pins_txt ); ?></div><?php endif; ?>
					<div class="ticket-foot">
						<div class="price-inline"><span class="price"><?php echo esc_html( $r_price ); ?></span><?php if ( $r_badge ) : ?><span class="status"><?php echo esc_html( $r_badge ); ?></span><?php endif; ?></div>
						<?php if ( $r_days ) : ?><span class="days-badge"><?php echo esc_html( $r_days ); ?> <?php echo esc_html( 'GÜN' ); ?></span><?php endif; ?>
					</div>
				</div>
			</a>
			<?php endforeach; ?>
		</div>
		<?php if ( count( $related ) > 2 ) : ?>
			<button class="related-arrow prev" onclick="document.getElementById('relatedScroll').scrollBy({left:-280,behavior:'smooth'})">‹</button>
			<button class="related-arrow next" onclick="document.getElementById('relatedScroll').scrollBy({left:280,behavior:'smooth'})">›</button>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>
<?php endwhile; ?>

<script>
let heroIdx = 0;
function heroMove(dir){
	const slides = document.querySelectorAll('#heroSlider .hero-slide');
	const dots = document.querySelectorAll('#heroSlider .hero-dots span');
	if(!slides.length) return;
	slides[heroIdx].classList.remove('active');
	if(dots[heroIdx]) dots[heroIdx].classList.remove('active');
	heroIdx = (heroIdx + dir + slides.length) % slides.length;
	slides[heroIdx].classList.add('active');
	if(dots[heroIdx]) dots[heroIdx].classList.add('active');
}
</script>

<?php if ( $howto_img ) : ?>
<div id="kervanHowtoModal" onclick="if(event.target===this) this.style.display='none';" style="display:none; position:fixed; inset:0; z-index:260; background:rgba(27,36,56,.75); align-items:center; justify-content:center; padding:24px;">
	<div style="background:var(--cream); border-radius:16px; padding:24px; max-width:480px; width:100%; max-height:85vh; overflow-y:auto; position:relative;">
		<button onclick="document.getElementById('kervanHowtoModal').style.display='none';" style="position:absolute; top:12px; right:14px; background:none; border:none; font-size:1.3rem; cursor:pointer; line-height:1;">✕</button>
		<a href="<?php echo esc_url( wp_get_attachment_url( $howto_img ) ); ?>" download style="display:inline-block; background:var(--ink); color:#fff; padding:9px 18px; border-radius:100px; text-decoration:none; font-weight:600; font-size:.84rem; margin-bottom:14px;">⬇ İndir</a>
		<img src="<?php echo esc_url( wp_get_attachment_url( $howto_img ) ); ?>" alt="Nasıl kayıt olurum?" style="width:100%; border-radius:10px; display:block;">
	</div>
</div>
<?php endif; ?>

<div id="kervanKayitModal" onclick="if(event.target===this) kervanCloseKayitForm();" style="display:none; position:fixed; inset:0; z-index:220; background:rgba(27,36,56,.8); align-items:center; justify-content:center; padding:16px;">
	<div style="background:var(--cream,#FBF6EC); border-radius:16px; padding:26px 22px; max-width:480px; width:100%; max-height:88vh; overflow-y:auto; position:relative;">
		<button onclick="kervanCloseKayitForm();" style="position:absolute; top:12px; right:14px; background:none; border:none; font-size:1.3rem; cursor:pointer; line-height:1;">✕</button>
		<h3 style="font-family:'Fraunces',serif; margin:0 0 4px;">Kayıt Formu</h3>
		<p style="font-size:.82rem; opacity:.7; margin:0 0 4px;"><?php the_title(); ?></p>
		<p style="font-size:.82rem; opacity:.7; margin:0 0 16px;">Bilgilerinizi doldurun, WhatsApp'ta size hazır bir mesaj açılacak ve sadece göndermeniz yeterli (göndermeden önce değişiklik yapabilirsiniz). Ardından pasaport belgenizi yollamayı unutmayınız.</p>

		<?php if ( $dates ) : ?>
		<label style="display:block; font-size:.82rem; font-weight:600; margin-bottom:6px;">Tercih ettiğiniz tarih</label>
		<select id="kervanKayitDate" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; margin-bottom:14px;">
			<option value="">Seçiniz…</option>
			<?php
			foreach ( $dates as $d ) {
				$is_full = strpos( strtolower( $d ), 'dolu' ) !== false || strpos( strtolower( $d ), 'doldu' ) !== false;
				echo '<option' . ( $is_full ? ' disabled' : '' ) . '>' . esc_html( $d ) . '</option>';
			}
			?>
			<option>Emin değilim, konuşalım</option>
		</select>
		<?php endif; ?>

		<label style="display:block; font-size:.82rem; font-weight:600; margin-bottom:6px;">Havalimanı</label>
		<p style="font-size:.72rem; opacity:.55; margin:0 0 6px;">Size uygun havalimanlarını öncelik sırasına göre yazabilirsiniz.</p>
		<input type="text" id="kervanKayitAirport" placeholder="Örn. Köln, Düsseldorf" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; box-sizing:border-box; margin-bottom:14px;">

		<label style="display:block; font-size:.82rem; font-weight:600; margin-bottom:6px;">Adınız Soyadınız (formu dolduran kişi)</label>
		<input type="text" id="kervanKayitOwnName" placeholder="Ad Soyad" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; box-sizing:border-box; margin-bottom:14px;">

		<label style="display:block; font-size:.82rem; font-weight:600; margin-bottom:6px;">Kimin için kayıt yapıyorsunuz?</label>
		<div style="display:flex; gap:16px; margin-bottom:14px; font-size:.86rem;">
			<label style="display:flex; align-items:center; gap:6px;"><input type="checkbox" id="kervanKayitKendim" onchange="kervanUpdatePersonAddButton()"> Kendim de katılıyorum</label>
			<label style="display:flex; align-items:center; gap:6px;"><input type="checkbox" id="kervanKayitBaska" onchange="kervanOnBaskaToggle()"> Başka kişi(ler)</label>
		</div>

		<?php if ( in_array( 'dunya', $group_list, true ) ) : ?>
		<p style="font-size:.72rem; opacity:.55; margin:0 0 6px;">Dünya Turları için bir e-posta ve adres bilgisine ihtiyacımız var (herhangi birinizden yeterlidir).</p>
		<input type="text" id="kervanKayitEmail" placeholder="E-posta" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; box-sizing:border-box; margin-bottom:8px;">
		<input type="text" id="kervanKayitAddress" placeholder="Ev adresi (sokak, posta kodu, şehir)" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; box-sizing:border-box; margin-bottom:14px;">
		<?php endif; ?>


		<div id="kervanKayitPersons"></div>
		<button type="button" id="kervanKayitAddPerson" onclick="kervanAddKayitPerson()" style="display:none; background:none; border:1px dashed var(--clay,#BD5B3B); color:var(--clay-dark,#9C462C); padding:8px 14px; border-radius:100px; font-size:.8rem; font-weight:600; cursor:pointer; margin-bottom:14px;">+ Kişi Ekle</button>

		<label style="display:block; font-size:.82rem; font-weight:600; margin:14px 0 6px;">Oda Tipi</label>
		<div id="kervanKayitRooms"></div>
		<button type="button" onclick="kervanAddKayitRoom()" style="background:none; border:1px dashed var(--clay,#BD5B3B); color:var(--clay-dark,#9C462C); padding:8px 14px; border-radius:100px; font-size:.8rem; font-weight:600; cursor:pointer; margin-bottom:14px;">+ Oda Ekle</button>

		<label style="display:block; font-size:.82rem; font-weight:600; margin:14px 0 6px;">Eklemek istediğiniz not</label>
		<textarea id="kervanKayitNote" rows="2" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; box-sizing:border-box; resize:vertical;"></textarea>

		<?php if ( $howto_img ) : ?>
		<div onclick="document.getElementById('kervanHowtoModal').style.display='flex';" style="display:flex; align-items:center; gap:10px; margin:14px 0 12px; padding:10px 12px; background:rgba(43,110,255,.08); border-radius:8px; cursor:pointer;">
			<span style="flex:0 0 auto; width:26px; height:26px; border-radius:6px; background:#2B6EFF; color:#fff; font-weight:800; font-size:1rem; display:flex; align-items:center; justify-content:center;">i</span>
			<span style="font-size:.86rem; font-weight:700; color:#2B6EFF; text-decoration:underline;">Önemli bilgiler</span>
		</div>
		<?php endif; ?>

		<div style="text-align:center; margin-bottom:14px;">
			<a href="#" onclick="kervanCopyKayitMessage(event)" style="color:#888; text-decoration:underline; font-size:.72rem;">Mesajı panoya kopyala (kendiniz göndermek isterseniz)</a>
		</div>

		<button type="button" onclick="kervanSubmitKayitForm()" style="width:100%; background:#4CAF50; color:#fff; border:none; padding:13px; border-radius:100px; font-weight:700; font-size:.9rem; cursor:pointer;">WhatsApp'a Devam Et</button>
	</div>
</div>

<script>
var kervanKayitTargetDigits = '';
var kervanKayitPersonSeq = 0;
var kervanKayitRoomSeq = 0;
var kervanIsDunya = <?php echo in_array( 'dunya', $group_list, true ) ? 'true' : 'false'; ?>;
var kervanRoomTypeChoices = ['Single (Tek kişilik, fiyat farkı)', 'Double (İki kişilik, büyük yatak)', 'Twin (İki kişilik, iki ayrı yatak)', 'Triple (Üç kişilik, otele göre farklı)'];

function kervanOpenKayitForm(digits){
	kervanKayitTargetDigits = digits;
	document.getElementById('kervanKayitModal').style.display = 'flex';
	if(document.getElementById('kervanKayitRooms').children.length === 0){
		kervanAddKayitRoom();
	}
	kervanUpdatePersonAddButton();
}

function kervanCloseKayitForm(){
	document.getElementById('kervanKayitModal').style.display = 'none';
}

function kervanOnBaskaToggle(){
	var baskaChecked = document.getElementById('kervanKayitBaska').checked;
	var wrap = document.getElementById('kervanKayitPersons');
	document.getElementById('kervanKayitAddPerson').style.display = baskaChecked ? 'inline-block' : 'none';
	if(baskaChecked && wrap.children.length === 0){
		kervanAddKayitPerson();
	}
}

function kervanUpdatePersonAddButton(){
	kervanRenumberPersons();
}

function kervanAddKayitPerson(){
	kervanKayitPersonSeq++;
	var rowId = 'kervanPersonRow' + kervanKayitPersonSeq;
	var wrap = document.getElementById('kervanKayitPersons');
	var row = document.createElement('div');
	row.id = rowId;
	row.className = 'kervan-kayit-person-row';
	row.style.cssText = 'margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid var(--line);';

	var numberLabel = document.createElement('div');
	numberLabel.className = 'kervan-kayit-person-number';
	numberLabel.style.cssText = 'font-size:.72rem; font-weight:700; opacity:.55; margin-bottom:4px;';
	row.appendChild(numberLabel);

	var topRow = document.createElement('div');
	topRow.style.cssText = 'display:flex; gap:8px;';

	var nameInput = document.createElement('input');
	nameInput.type = 'text';
	nameInput.className = 'kervan-kayit-person';
	nameInput.placeholder = 'Ad Soyad';
	nameInput.style.cssText = 'flex:1; min-width:0; padding:10px 12px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; box-sizing:border-box;';

	var delBtn = document.createElement('button');
	delBtn.type = 'button';
	delBtn.innerHTML = '&times;';
	delBtn.title = 'Bu kişiyi kaldır';
	delBtn.style.cssText = 'flex:0 0 auto; width:34px; border-radius:8px; border:1px solid var(--line); background:#fff; color:#B3261E; font-size:1.1rem; cursor:pointer;';
	delBtn.onclick = function(){
		var el = document.getElementById(rowId);
		if(el){ el.remove(); }
		kervanRenumberPersons();
	};

	topRow.appendChild(nameInput);
	topRow.appendChild(delBtn);
	row.appendChild(topRow);

	wrap.appendChild(row);
	kervanRenumberPersons();
}

function kervanRenumberPersons(){
	var kendimChecked = document.getElementById('kervanKayitKendim').checked;
	var offset = kendimChecked ? 1 : 0;
	var rows = document.querySelectorAll('.kervan-kayit-person-row');
	for(var i=0; i<rows.length; i++){
		var label = rows[i].querySelector('.kervan-kayit-person-number');
		if(label){ label.textContent = (i + 1 + offset) + '. Kişi'; }
	}
}

function kervanAddKayitRoom(){
	kervanKayitRoomSeq++;
	var rowId = 'kervanRoomRow' + kervanKayitRoomSeq;
	var wrap = document.getElementById('kervanKayitRooms');
	var row = document.createElement('div');
	row.id = rowId;
	row.style.cssText = 'display:flex; gap:8px; margin-bottom:8px;';

	var comboWrap = document.createElement('div');
	comboWrap.style.cssText = 'position:relative; flex:1.6; min-width:0;';

	var typeInput = document.createElement('input');
	typeInput.type = 'text';
	typeInput.className = 'kervan-kayit-room-type';
	typeInput.placeholder = 'Oda tipi seçin veya yazın';
	typeInput.style.cssText = 'width:100%; padding:10px 32px 10px 12px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; box-sizing:border-box;';

	var toggleBtn = document.createElement('button');
	toggleBtn.type = 'button';
	toggleBtn.innerHTML = '▾';
	toggleBtn.style.cssText = 'position:absolute; right:5px; top:5px; bottom:5px; width:26px; border:none; background:rgba(0,0,0,.06); border-radius:6px; cursor:pointer; font-size:1.1rem; opacity:1; color:var(--charcoal,#2B2016);';

	var dropdown = document.createElement('div');
	dropdown.style.cssText = 'display:none; position:absolute; top:100%; left:0; right:0; margin-top:4px; background:var(--cream,#FBF6EC); border:1px solid var(--line); border-radius:8px; box-shadow:0 8px 20px -8px rgba(27,36,56,.35); z-index:5; max-height:180px; overflow-y:auto;';
	kervanRoomTypeChoices.forEach(function(opt){
		var item = document.createElement('div');
		item.textContent = opt;
		item.style.cssText = 'padding:9px 12px; font-size:.84rem; cursor:pointer;';
		item.onmouseenter = function(){ this.style.background = 'rgba(0,0,0,.06)'; };
		item.onmouseleave = function(){ this.style.background = 'none'; };
		item.onclick = function(){
			typeInput.value = opt;
			dropdown.style.display = 'none';
		};
		dropdown.appendChild(item);
	});
	toggleBtn.onclick = function(){
		var isOpen = dropdown.style.display === 'block';
		document.querySelectorAll('.kervan-room-dropdown-open').forEach(function(d){ d.style.display = 'none'; });
		dropdown.style.display = isOpen ? 'none' : 'block';
	};
	dropdown.className = 'kervan-room-dropdown-open';
	document.addEventListener('click', function(e){
		if(!comboWrap.contains(e.target)){ dropdown.style.display = 'none'; }
	});

	comboWrap.appendChild(typeInput);
	comboWrap.appendChild(toggleBtn);
	comboWrap.appendChild(dropdown);

	var countInput = document.createElement('input');
	countInput.type = 'number';
	countInput.className = 'kervan-kayit-room-count';
	countInput.min = '1';
	countInput.value = '1';
	countInput.title = 'Kaç oda?';
	countInput.style.cssText = 'width:64px; padding:10px 8px; border-radius:8px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; box-sizing:border-box;';

	var delBtn = document.createElement('button');
	delBtn.type = 'button';
	delBtn.innerHTML = '&times;';
	delBtn.title = 'Bu odayı kaldır';
	delBtn.style.cssText = 'flex:0 0 auto; width:34px; border-radius:8px; border:1px solid var(--line); background:#fff; color:#B3261E; font-size:1.1rem; cursor:pointer;';
	delBtn.onclick = function(){
		var el = document.getElementById(rowId);
		if(el){ el.remove(); }
	};

	row.appendChild(comboWrap);
	row.appendChild(countInput);
	row.appendChild(delBtn);
	wrap.appendChild(row);
}

function kervanBuildKayitLines(){
	var tourTitleEl = document.querySelector('.title-row h1');
	var tourTitle = tourTitleEl ? tourTitleEl.textContent.trim() : document.title;
	var date = '';
	var dateSelect = document.getElementById('kervanKayitDate');
	if(dateSelect){ date = dateSelect.value; }
	var airport = document.getElementById('kervanKayitAirport').value.trim();
	var ownName = document.getElementById('kervanKayitOwnName').value.trim();
	var kendim = document.getElementById('kervanKayitKendim').checked;
	var baska = document.getElementById('kervanKayitBaska').checked;
	var note = document.getElementById('kervanKayitNote').value.trim();

	var emailEl = document.getElementById('kervanKayitEmail');
	var email = emailEl ? emailEl.value.trim() : '';
	var addressEl = document.getElementById('kervanKayitAddress');
	var address = addressEl ? addressEl.value.trim() : '';

	var lines = [];
	lines.push('Merhaba, "' + tourTitle + '" turu için kayıt olmak istiyorum.');

	var today = new Date();
	var dd = String(today.getDate()).padStart(2, '0');
	var mm = String(today.getMonth() + 1).padStart(2, '0');
	var yyyy = today.getFullYear();
	var submitLabel = ownName ? ownName : 'Ben';
	lines.push('*Kayıt tarihi: ' + submitLabel + ', ' + dd + '.' + mm + '.' + yyyy + '*');
	lines.push('');

	if(date){ lines.push('*Tarih: ' + date + '*'); }
	if(airport){ lines.push('*Havalimanı: ' + airport + '*'); }
	if(date || airport){ lines.push(''); }

	if(email){ lines.push('*E-posta: ' + email + '*'); }
	if(address){ lines.push('*Adres: ' + address + '*'); }
	if(email || address){ lines.push(''); }

	if(ownName){ lines.push('*Formu dolduran: ' + ownName + '*'); }
	if(kendim || baska){
		if(kendim && !baska){
			lines.push('*Kayıt: Kendim için*');
		} else if(!kendim && baska){
			lines.push('*Kayıt: Başkası/başka kişiler için*');
		} else {
			lines.push('*Kayıt: Kendim ve başka kişi(ler) için*');
		}
		var personNum = 1;
		if(kendim){
			var selfLabel = ownName ? ownName : 'Ben';
			lines.push(personNum + '. ' + selfLabel + ' (kaydı yapan kişi)');
			personNum++;
		}
		var personInputs = document.querySelectorAll('.kervan-kayit-person');
		for(var p=0; p<personInputs.length; p++){
			var v = personInputs[p].value.trim();
			if(!v){ continue; }
			lines.push(personNum + '. ' + v);
			personNum++;
		}
		lines.push('');
	} else if(ownName){
		lines.push('');
	}

	var roomTypes = document.querySelectorAll('.kervan-kayit-room-type');
	var roomCounts = document.querySelectorAll('.kervan-kayit-room-count');
	var roomLines = [];
	for(var r=0; r<roomTypes.length; r++){
		var rt = roomTypes[r].value.trim();
		if(!rt){ continue; }
		var rc = roomCounts[r] ? roomCounts[r].value.trim() : '';
		if(!rc){ rc = '1'; }
		roomLines.push('-' + rc + 'x ' + rt);
	}
	if(roomLines.length){
		lines.push('*Oda tipi:*');
		for(var rl=0; rl<roomLines.length; rl++){
			lines.push(roomLines[rl]);
		}
		lines.push('');
	}

	lines.push('*Not:*' + (note ? ' ' + note : ''));

	return lines.join(String.fromCharCode(10));
}

function kervanSubmitKayitForm(){
	var msgText = kervanBuildKayitLines();
	var url = 'https://wa.me/' + kervanKayitTargetDigits + '?text=' + encodeURIComponent(msgText);
	window.open(url, '_blank');
	kervanCloseKayitForm();
}

function kervanCopyKayitMessage(e){
	e.preventDefault();
	var msgText = kervanBuildKayitLines();
	if(navigator.clipboard && navigator.clipboard.writeText){
		navigator.clipboard.writeText(msgText).then(function(){
			alert('Mesaj panoya kopyalandı.');
		});
	} else {
		var ta = document.createElement('textarea');
		ta.value = msgText;
		document.body.appendChild(ta);
		ta.select();
		document.execCommand('copy');
		document.body.removeChild(ta);
		alert('Mesaj panoya kopyalandı.');
	}
}
</script>

<?php get_footer(); ?>

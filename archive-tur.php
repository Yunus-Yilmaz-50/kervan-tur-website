<?php get_header(); ?>

<script>window.__kervanDe = <?php echo kervan_is_de() ? 'true' : 'false'; ?>;</script>

<?php

function kervan_get_tour_data( $id ) {
	$years = array_filter( array_map( 'trim', explode( ',', get_post_meta( $id, 'kervan_year', true ) ) ) );
	if ( ! $years ) $years = array( date( 'Y' ) );
	$dates_raw = kervan_lines_to_array( get_post_meta( $id, 'kervan_dates', true ) );
	$dates = array();
	foreach ( $dates_raw as $d ) {
		$full = ( stripos( $d, '(dolu)' ) !== false || stripos( $d, '(full)' ) !== false );
		$dates[] = array( 'd' => trim( str_ireplace( array( '(dolu)', '(full)' ), '', $d ) ), 'full' => $full );
	}
	$first_date = $dates_raw ? $dates_raw[0] : '';
	preg_match( '/(\d{2})[.\-](\d{2})[.\-]?(\d{4})?/', $first_date, $m );
	$month = isset( $m[2] ) ? intval( $m[2] ) : 1;
	$sort_day = isset( $m[1] ) ? intval( $m[1] ) : 1;
	$sort_year = ( isset( $m[3] ) && $m[3] ) ? intval( $m[3] ) : intval( date( 'Y' ) );

	$last_date = $dates_raw ? end( $dates_raw ) : '';
	preg_match( '/(\d{2})[.\-](\d{2})[.\-]?(\d{4})?/', $last_date, $lm );
	$last_month = isset( $lm[2] ) ? intval( $lm[2] ) : $month;
	$last_day = isset( $lm[1] ) ? intval( $lm[1] ) : $sort_day;

	// Alle Monate sammeln, in denen ein Termin dieser Tour liegt (für den Zeitraum-Filter)
	$all_months = array();
	foreach ( $dates_raw as $d ) {
		if ( preg_match( '/(\d{2})[.\-](\d{2})/', $d, $dm ) ) {
			$all_months[] = intval( $dm[2] );
		}
	}
	$all_months = array_unique( $all_months );
	if ( ! $all_months ) $all_months = array( $month );

	// Monate pro Jahr (Start-Monat jedes Termins), damit der Zeitraum-Filter je Jahr nur echte Monate anbietet
	$months_by_year = array();
	$fallback_year = $years ? (string) reset( $years ) : (string) date( 'Y' );
	foreach ( $dates_raw as $d ) {
		if ( preg_match_all( '/(\d{2})[.\-](\d{2})(?:[.\-](\d{4}))?/', $d, $mm, PREG_SET_ORDER ) && $mm ) {
			$start_m = $mm[0];
			$end_m = end( $mm );
			$y_of = ! empty( $start_m[3] ) ? $start_m[3] : ( ! empty( $end_m[3] ) ? $end_m[3] : $fallback_year );
			$months_by_year[ $y_of ][] = intval( $start_m[2] );
		}
	}
	foreach ( $months_by_year as $y_of => $arr_m ) {
		$months_by_year[ $y_of ] = array_values( array_unique( $arr_m ) );
	}
	$last_year = ( isset( $lm[3] ) && $lm[3] ) ? intval( $lm[3] ) : $sort_year;
	$price_raw = get_post_meta( $id, 'kervan_price', true );
	$group = get_post_meta( $id, 'kervan_group', true );
	if ( ! $group ) $group = 'turkiye';
	$highlight = strtolower( trim( get_post_meta( $id, 'kervan_highlight', true ) ) ) === 'evet';
	$until = get_post_meta( $id, 'kervan_highlight_until', true );
	$active_highlight = $highlight && ( ! $until || strtotime( 'today' ) <= strtotime( $until ) );
	$conts = wp_get_post_terms( $id, 'kita', array( 'fields' => 'slugs' ) );
	$badge_val = get_post_meta( $id, 'kervan_badge', true );
	$badge_full = kervan_badge_is_full( $badge_val );
	$badge_past = kervan_badge_is_past( $badge_val );
	$is_full = false;
	if ( $dates ) {
		$is_full = true;
		foreach ( $dates as $d ) {
			if ( ! $d['full'] ) { $is_full = false; break; }
		}
	}
	return array(
		'id' => $id,
		'years' => $years,
		'month' => $month,
		'allMonths' => $all_months,
		'monthsByYear' => $months_by_year,
		'sortDate' => $sort_year * 10000 + $month * 100 + $sort_day,
		'lastSortDate' => $last_year * 10000 + $last_month * 100 + $last_day,
		'priceNum' => intval( preg_replace( '/\D/', '', $price_raw ) ),
		'price' => $price_raw,
		'group' => $group,
		'cont' => $conts ? $conts[0] : '',
		'days' => get_post_meta( $id, 'kervan_days', true ),
		'pins' => kervan_places_pins( $id ),
		'badge' => get_post_meta( $id, 'kervan_badge', true ),
		'dates' => $dates,
		'activeHighlight' => ( $active_highlight && ! $badge_past ),
		'badgePast' => $badge_past,
		'isFull' => ( $is_full || $badge_full ) ? 1 : 0,
		'badgeFull' => $badge_full,
	);
}

function kervan_render_ticket( $t, $post, $featured = false ) {
	$img = has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail_url( $post->ID, 'medium_large' ) : '';
	$bg = $img ? 'background-image:url(' . esc_url( $img ) . ')' : 'background:linear-gradient(135deg, var(--clay), var(--clay-dark))';
	$pins_list = array_filter( array_map( 'trim', preg_split( '/[·,]/', $t['pins'] ) ) );
	$static_card = $t['badgeFull'] || $t['badgePast'];
	?>
	<<?php echo $static_card ? 'div' : 'a href="' . esc_url( get_permalink( $post->ID ) ) . '"'; ?> class="ticket<?php echo $featured ? ' featured' : ''; ?><?php echo $static_card ? ' ticket-full' : ''; ?><?php echo $t['badgePast'] ? ' ticket-past' : ''; ?>"
		data-id="<?php echo esc_attr( $t['id'] ); ?>"
		data-years="<?php echo esc_attr( implode( ',', $t['years'] ) ); ?>"
		data-month="<?php echo esc_attr( $t['month'] ); ?>"
		data-months="<?php echo esc_attr( implode( ',', $t['allMonths'] ) ); ?>"
		data-months-by-year="<?php echo esc_attr( wp_json_encode( (object) $t['monthsByYear'] ) ); ?>"
		data-cont="<?php echo esc_attr( $t['cont'] ); ?>"
		data-price="<?php echo esc_attr( $t['priceNum'] ); ?>"
		data-sortdate="<?php echo esc_attr( $t['sortDate'] ); ?>"
		data-lastsortdate="<?php echo esc_attr( $t['lastSortDate'] ); ?>"
		data-group="<?php echo esc_attr( $t['group'] ); ?>"
		data-name="<?php echo esc_attr( kervan_normalize_tr( $post->post_title . ' ' . $t['pins'] ) ); ?>"
		data-highlight="<?php echo $t['activeHighlight'] ? '1' : '0'; ?>"
		data-full="<?php echo $t['isFull']; ?>"
		data-past="<?php echo $t['badgePast'] ? '1' : '0'; ?>">
		<div class="ticket-top" style="<?php echo $bg; ?>">
			<span class="fav-since" data-fav-since></span>
			<?php if ( ! $t['badgePast'] ) : ?><span class="fav-heart" data-fav-heart="<?php echo esc_attr( $t['id'] ); ?>" onclick="kervanToggleFav(event, this)" role="button" aria-label="Favorilere ekle" tabindex="0">🤍</span><?php endif; ?>
			<h3><?php echo esc_html( $post->post_title ); ?></h3>
		</div>
		<div class="ticket-body">
			<?php if ( $t['dates'] ) :
				$shown_dates = array_slice( $t['dates'], 0, 2 );
			?>
			<div class="ticket-dates">📅
				<?php foreach ( $shown_dates as $i => $d ) : ?><?php echo $i > 0 ? ' <span style="opacity:.4;">|</span> ' : ''; ?><?php echo esc_html( $d['d'] ); ?><?php if ( $d['full'] ) echo ' <span style="color:var(--clay-dark);font-weight:700;">(DOLU)</span>'; ?><?php endforeach; ?>
				<?php if ( count( $t['dates'] ) > 2 ) : ?><span class="pins-extra">+<?php echo count( $t['dates'] ) - 2; ?></span><?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ( $pins_list ) : ?>
			<div class="ticket-pins">📍 <?php echo esc_html( implode( ' · ', $pins_list ) ); ?></div>
			<?php endif; ?>
			<div class="ticket-foot">
				<div class="price-inline"><span class="price"><?php echo esc_html( $t['price'] ); ?></span><?php if ( $t['badge'] ) : ?><span class="status<?php echo $t['badgeFull'] ? ' status-full' : ''; ?><?php echo $t['badgePast'] ? ' status-past' : ''; ?>"><?php echo esc_html( $t['badge'] ); ?></span><?php endif; ?></div>
				<?php if ( $t['days'] ) : ?><span class="days-badge"><?php echo esc_html( $t['days'] ); ?> GÜN</span><?php endif; ?>
			</div>
		</div>
	</<?php echo $static_card ? 'div' : 'a'; ?>>
	<?php
}

function kervan_normalize_tr( $str ) {
	$map = array( 'İ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ğ' => 'g', 'ğ' => 'g', 'Ü' => 'u', 'ü' => 'u', 'Ş' => 's', 'ş' => 's', 'Ö' => 'o', 'ö' => 'o', 'Ç' => 'c', 'ç' => 'c' );
	$str = strtr( $str, $map );
	return mb_strtolower( $str, 'UTF-8' );
}
?>

<style>
.tours-wrap{ max-width:1180px; margin:0 auto; padding:20px 20px 70px; position:relative; z-index:1; }
body.post-type-archive-tur{ background:linear-gradient(135deg, #F6EFDF 0%, var(--cream) 40%, var(--cream) 60%, #F6EFDF 100%); }
.year-heading{ display:flex; align-items:baseline; gap:14px; margin:6px 0 24px; }
.year-word{ font-family:'Fraunces',serif; font-weight:700; border:none; background:none; cursor:pointer; padding:0; color:var(--charcoal); font-size:clamp(1.8rem,3.6vw,2.6rem); opacity:.32; transition:all .25s ease; }
.year-word.active{ opacity:1; color:var(--clay); font-size:clamp(2.1rem,4.2vw,3rem); }
.year-sep{ font-size:1.4rem; opacity:.3; font-weight:300; }

.search-row{ display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:14px; }
.filter-toggle{ display:inline-flex; align-items:center; gap:8px; border:none; background:linear-gradient(135deg, #D9C39E, #C7AD80); color:var(--charcoal); padding:12px 22px; border-radius:100px; font-weight:700; font-size:.86rem; cursor:pointer; box-shadow:0 10px 24px -14px rgba(27,36,56,.35); }
.filter-toggle .count{ background:#C2664A; color:#fff; border-radius:100px; padding:1px 8px; font-size:.72rem; }
.search-box{ position:relative; flex:1; min-width:220px; }
.search-box input{ width:100%; padding:13px 16px 13px 40px; border-radius:100px; border:1px solid var(--line); font-family:inherit; font-size:.9rem; background:var(--cream); box-shadow:0 6px 18px -14px rgba(27,36,56,.4); }
.search-box .ic{ position:absolute; left:15px; top:50%; transform:translateY(-50%); opacity:.5; }
.filter-panel{ max-height:0; overflow:hidden; transition:max-height .3s ease; }
.filter-panel.open{ max-height:320px; }
.filter-panel-inner{ background:linear-gradient(150deg, var(--sand) 0%, #F6ECD9 60%, var(--sand) 100%); border-radius:16px; padding:20px 22px; margin-bottom:20px; display:flex; align-items:center; gap:18px; flex-wrap:wrap; box-shadow:0 16px 34px -22px rgba(27,36,56,.45), inset 0 1px 0 rgba(255,255,255,.5); }
.filter-group-label{ font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; opacity:.75; margin-right:8px; }
.chip-row{ display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
.chip{ border:none; background:#E8D9BC; padding:8px 15px; border-radius:100px; font-size:.78rem; font-weight:500; cursor:pointer; opacity:.85; box-shadow:0 4px 10px -6px rgba(27,36,56,.25); transition:all .15s ease; }
.chip:hover{ opacity:1; transform:translateY(-1px); }
.chip.active{ background:linear-gradient(135deg, var(--ink), #2A2038); color:#fff; opacity:1; box-shadow:0 6px 16px -8px rgba(27,36,56,.6); }
.fav-toggle{ border:none; background:#E8D9BC; color:var(--charcoal); padding:8px 16px; border-radius:100px; font-size:.8rem; font-weight:600; cursor:pointer; box-shadow:0 4px 10px -6px rgba(27,36,56,.25); transition:all .15s ease; }
.fav-toggle.active{ background:linear-gradient(135deg, #C2664A, #9C462C); color:#fff; box-shadow:0 6px 16px -8px rgba(194,102,74,.55); }
.filter-row-inline{ display:flex; align-items:center; gap:8px; font-size:.82rem; }
.filter-row-inline select{ font-family:inherit; padding:8px 14px; border-radius:100px; border:none; background-color:#E8D9BC; color:var(--charcoal); font-size:.8rem; box-shadow:0 4px 10px -6px rgba(27,36,56,.25); appearance:none; -webkit-appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%232B2016' stroke-width='1.4' fill='none'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-size:10px 7px; padding-right:30px; }
.filter-row-inline select option{ color:var(--charcoal); background:var(--cream); }
.filter-row-inline select option:disabled{ color:#B5AB9A; }
.clear-link{ font-size:.76rem; text-decoration:underline; opacity:.6; cursor:pointer; background:none; border:none; margin-left:auto; }

.highlight-strip{ margin:8px 0 40px; padding:22px; background:linear-gradient(135deg, var(--sand), var(--cream)); border:1px solid var(--line); border-radius:16px; }
.highlight-strip .eyebrow{ font-weight:700; font-size:.85rem; letter-spacing:.12em; }
.highlight-grid, .tour-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:22px; }
@media(max-width:980px){ .highlight-grid, .tour-grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:640px){ .highlight-grid, .tour-grid{grid-template-columns:1fr;} }

.ticket{ background:var(--cream); border-radius:10px; overflow:hidden; box-shadow:0 18px 40px -20px rgba(27,36,56,.35); display:flex; flex-direction:column; text-decoration:none; color:inherit; }
.fav-heart{ position:absolute!important; top:14px; right:14px; z-index:2; font-size:1.15rem; width:30px; height:30px; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:transform .15s ease; filter:drop-shadow(0 1px 3px rgba(0,0,0,.6)); }
.fav-heart.pop{ animation:favPop .35s ease; }
@keyframes favPop{ 0%{transform:scale(1);} 40%{transform:scale(1.35);} 100%{transform:scale(1);} }
.fav-since{ position:absolute!important; top:14px; left:14px; z-index:2; background:rgba(0,0,0,.4); color:#fff; font-size:.66rem; font-weight:600; padding:5px 10px; border-radius:100px; display:none; }
.ticket.featured{ box-shadow:0 22px 46px -18px rgba(189,91,59,.45); border:1.5px solid var(--gold); }
.ticket-top{ padding:22px 20px 32px; color:#fff; position:relative; min-height:150px; display:flex; flex-direction:column; justify-content:flex-end; background-size:cover; background-position:center; }
.ticket-top::after{ content:''; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,0) 30%, rgba(0,0,0,.65) 100%); z-index:0; }
.ticket-top > *{ position:relative; z-index:1; }
.ticket-top h3{ font-size:1.4rem; font-weight:700; margin:0; text-shadow:0 2px 6px rgba(0,0,0,.5); color:#fff; }
.ticket-body{ padding:16px 18px 18px; display:flex; flex-direction:column; gap:10px; flex:1; }
.ticket-dates{ font-size:.78rem; opacity:.75; }
.ticket-pins{ font-size:.78rem; opacity:.75; }
.pins-extra{ opacity:.55; margin-left:2px; }
.ticket-foot{ display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid var(--line); gap:10px; margin-top:auto; }
.price-inline{ display:flex; align-items:baseline; gap:8px; }
.price-inline .price{ font-family:'Fraunces',serif; font-weight:700; font-size:1.15rem; }
.price-inline .status{ font-size:.74rem; font-weight:700; color:#E00000; }
.ticket-full{ cursor:default; }
.ticket-past .ticket-body{ opacity:.7; }
.days-badge{ background:var(--clay); color:#fff; font-size:.76rem; font-weight:600; padding:8px 14px; border-radius:100px; white-space:nowrap; }
.faq-section{ margin-top:60px; max-width:760px; }
.faq-item{ border-bottom:1px solid var(--line); padding:14px 0; }
.faq-item summary{ cursor:pointer; font-weight:600; font-family:'Fraunces',serif; font-size:1.05rem; list-style:none; display:flex; justify-content:space-between; align-items:center; }
.faq-item summary::-webkit-details-marker{ display:none; }
.faq-item summary::after{ content:'+'; font-size:1.3rem; color:var(--clay); }
.faq-item[open] summary::after{ content:'–'; }
.faq-item p{ margin:10px 0 0; opacity:.85; line-height:1.6; font-size:.92rem; }
</style>

<?php
$tour_posts = get_posts( array( 'post_type' => 'tur', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
$tour_data = array();
$all_years = array();
foreach ( $tour_posts as $p ) {
	$d = kervan_get_tour_data( $p->ID );
	$tour_data[ $p->ID ] = $d;
	foreach ( $d['years'] as $y ) $all_years[ $y ] = true;
}
$all_years = array_keys( $all_years );
sort( $all_years );
$requested_year = isset( $_GET['yil'] ) ? sanitize_text_field( $_GET['yil'] ) : '';
$default_year = ( $requested_year && in_array( $requested_year, $all_years, true ) ) ? $requested_year : ( $all_years ? end( $all_years ) : date( 'Y' ) );
$highlighted_posts = array_filter( $tour_posts, function( $p ) use ( $tour_data ) { return $tour_data[ $p->ID ]['activeHighlight']; } );
?>

<div class="tours-wrap">
	<div class="eyebrow" style="font-family:'IBM Plex Mono',monospace; text-transform:uppercase; letter-spacing:.14em; font-size:.85rem; font-weight:700; color:var(--clay-dark);">TURLAR</div>
	<h1 class="year-heading" id="yearHeading">
		<?php foreach ( $all_years as $i => $y ) : ?>
			<?php if ( $i > 0 ) echo '<span class="year-sep">/</span>'; ?>
			<button class="year-word<?php echo $y === $default_year ? ' active' : ''; ?>" data-year="<?php echo esc_attr( $y ); ?>"><?php echo esc_html( $y ); ?></button>
		<?php endforeach; ?>
		<?php if ( ! $all_years ) echo '<span style="font-family:\'Fraunces\',serif;font-size:1.8rem;">Turlarımız</span>'; ?>
	</h1>

	<div class="search-row">
		<button class="filter-toggle" id="filterToggle">⚙️ <span id="filterLabel"><?php echo kervan_ft( 'Filtrele' ); ?></span> <span class="count" id="filterCount" style="display:none;">0</span></button>
		<div class="search-box"><span class="ic">🔍</span><input id="searchInput" placeholder="<?php echo kervan_ft( 'Tur ara…' ); ?>"></div>
	</div>

	<div class="filter-panel" id="filterPanel">
		<div class="filter-panel-inner" data-no-translation>
			<span class="filter-group-label"><?php echo kervan_ft( 'Kıta' ); ?></span>
			<div class="chip-row" id="chipRow">
				<?php foreach ( get_terms( array( 'taxonomy' => 'kita', 'hide_empty' => false ) ) as $kita ) : ?>
					<button class="chip" data-cont="<?php echo esc_attr( $kita->slug ); ?>"><?php echo esc_html( kervan_ft( $kita->name ) ); ?></button>
				<?php endforeach; ?>
			</div>
			<div class="filter-row-inline">
				<span class="filter-group-label" style="margin:0;"><?php echo kervan_ft( 'Tarih Aralığı' ); ?></span>
				<select id="fromMonth"></select> – <select id="toMonth"></select>
			</div>
			<div class="filter-row-inline">
				<span class="filter-group-label" style="margin:0;"><?php echo kervan_ft( 'Sırala' ); ?></span>
				<select id="sortSelect">
					<option value="default"><?php echo kervan_ft( 'Varsayılan' ); ?></option>
					<option value="price_asc"><?php echo kervan_ft( 'Fiyat: Düşükten Yükseğe' ); ?></option>
					<option value="price_desc"><?php echo kervan_ft( 'Fiyat: Yüksekten Düşüğe' ); ?></option>
					<option value="date_asc"><?php echo kervan_ft( 'Tarih: Yakından Uzağa' ); ?></option>
					<option value="date_desc"><?php echo kervan_ft( 'Tarih: Uzaktan Yakına' ); ?></option>
					<option value="group_tr"><?php echo kervan_ft( 'Önce: Türkiye Turları' ); ?></option>
					<option value="group_world"><?php echo kervan_ft( 'Önce: Dünya Turları' ); ?></option>
					<option value="cont"><?php echo kervan_ft( 'Kıtaya Göre (A-Z)' ); ?></option>
				</select>
			</div>
			<button type="button" id="favOnlyToggle" class="fav-toggle">❤️ <span><?php echo kervan_ft( 'Favoriler' ); ?></span></button>
			<button class="clear-link" id="clearFilters"><?php echo kervan_ft( 'Filtreleri Temizle' ); ?></button>
		</div>
	</div>

	<?php if ( $highlighted_posts ) : ?>
	<div class="highlight-strip" id="highlightWrap">
		<span class="eyebrow" style="color:var(--clay-dark); display:block; margin-bottom:12px;">ÖNE ÇIKANLAR</span>
		<div class="highlight-grid">
			<?php foreach ( $highlighted_posts as $p ) : kervan_render_ticket( $tour_data[ $p->ID ], $p, true ); endforeach; ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="tour-grid" id="tourGrid">
		<?php foreach ( $tour_posts as $p ) : kervan_render_ticket( $tour_data[ $p->ID ], $p, false ); endforeach; ?>
	</div>
	<p id="emptyMsg" style="display:none; opacity:.6;"><?php echo kervan_ft( 'Bu filtreye uygun tur yok.' ); ?></p>

	<div id="kervanDunya2027Notice" style="display:none; margin-top:24px; background:linear-gradient(135deg, var(--ink), #2A2038); color:#fff; border-radius:14px; padding:20px 24px; text-align:center; font-size:.92rem;">
		🌍 <strong><?php echo kervan_ft( 'Dünya Turları 2027 son aşamada!' ); ?></strong> <?php echo kervan_ft( 'Türkiye Turları\'na ek olarak, Dünya Turları da yakında burada yayında olacak.' ); ?>
	</div>
</div>

<script>
const MONTHS = [<?php foreach ( array( 'Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık' ) as $ay ) { echo '"' . esc_js( kervan_ft( $ay ) ) . '",'; } ?>];

function normalize(str){
	return str.replaceAll('İ','i').replaceAll('I','i').replaceAll('ı','i').replaceAll('Ğ','g').replaceAll('ğ','g')
		.replaceAll('Ü','u').replaceAll('ü','u').replaceAll('Ş','s').replaceAll('ş','s').replaceAll('Ö','o').replaceAll('ö','o')
		.replaceAll('Ç','c').replaceAll('ç','c').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
}

const fromSel=document.getElementById('fromMonth'), toSel=document.getElementById('toMonth');
MONTHS.forEach((m,i)=>{ fromSel.innerHTML+=`<option value="${i+1}">${m}</option>`; toSel.innerHTML+=`<option value="${i+1}">${m}</option>`; });
fromSel.value=1; toSel.value=12;

let year = '<?php echo esc_js( $default_year ); ?>';
(function(){
	var savedYear = localStorage.getItem('kervanSelectedYear');
	if (savedYear && document.querySelector('.year-word[data-year="' + savedYear + '"]')) {
		year = savedYear;
	}
	var m = location.hash.match(/^#yil-(\d{4})$/);
	if (m && document.querySelector('.year-word[data-year="' + m[1] + '"]')) {
		year = m[1];
		localStorage.setItem('kervanSelectedYear', year);
	}
	document.querySelectorAll('.year-word').forEach(x=>x.classList.remove('active'));
	document.querySelector('.year-word[data-year="' + year + '"]')?.classList.add('active');
})();
let selectedConts = [];

document.querySelectorAll('.year-word').forEach(b=>b.addEventListener('click', ()=>{
	document.querySelectorAll('.year-word').forEach(x=>x.classList.remove('active'));
	b.classList.add('active');
	year = b.dataset.year;
	localStorage.setItem('kervanSelectedYear', year);
	render();
}));

const allCards = [...document.querySelectorAll('#tourGrid .ticket')];
const highlightCards = [...document.querySelectorAll('#highlightWrap .ticket')];
const tourGrid = document.getElementById('tourGrid');

function cardMatches(el, from, to, q){
	const years = el.dataset.years.split(',');
	if(!kervanFavOnly && !years.includes(year)) return false;
	if(selectedConts.length>0 && !selectedConts.includes(el.dataset.cont)) return false;
	// Vergangene Touren (Gerçekleşti) werden vom Zeitraum-Filter nicht betroffen
	if(el.dataset.past !== '1'){
		let months = null;
		if(!kervanFavOnly){
			try { const map = JSON.parse(el.dataset.monthsByYear || '{}'); if(map[year] && map[year].length) months = map[year]; } catch(e){}
		}
		if(!months) months = el.dataset.months.split(',').map(Number);
		if(!months.some(m => m>=from && m<=to)) return false;
	}
	if(q && !el.dataset.name.includes(q)) return false;
	if(kervanFavOnly){
		const heart = el.querySelector('[data-fav-heart]');
		const favs = kervanGetFavs();
		if(!heart || !favs[heart.dataset.favHeart]) return false;
	}
	return true;
}

let availMin = 1, availMax = 12;
// Nur Monate auswählbar machen, in denen im gewählten Jahr überhaupt Touren starten (Rest ausgegraut)
function updateMonthOptions(){
	const avail = new Set();
	if(kervanFavOnly){
		for(let m=1;m<=12;m++) avail.add(m);
	} else {
		allCards.forEach(el=>{
			if(el.dataset.past === '1') return; // vergangene Touren zählen nicht für die verfügbaren Monate
			try { const map = JSON.parse(el.dataset.monthsByYear || '{}'); (map[year] || []).forEach(m=>avail.add(+m)); } catch(e){}
		});
	}
	if(avail.size===0){ for(let m=1;m<=12;m++) avail.add(m); }
	const arr = [...avail];
	const newMin = Math.min(...arr), newMax = Math.max(...arr);
	const wasFull = (+fromSel.value===availMin && +toSel.value===availMax);
	[fromSel, toSel].forEach(sel=>{ [...sel.options].forEach(o=>{ o.disabled = !avail.has(+o.value); }); });
	if(wasFull || !avail.has(+fromSel.value)) fromSel.value = newMin;
	if(wasFull || !avail.has(+toSel.value)) toSel.value = newMax;
	if(+fromSel.value > +toSel.value){ fromSel.value = newMin; toSel.value = newMax; }
	availMin = newMin; availMax = newMax;
}

function activeFilterCount(){
	let n=0; if(selectedConts.length>0) n++; if(+fromSel.value!==availMin || +toSel.value!==availMax) n++; return n;
}

function render(){
	updateMonthOptions();
	const from=+fromSel.value, to=+toSel.value;
	const q = normalize(document.getElementById('searchInput').value.trim());
	const sortVal = document.getElementById('sortSelect').value;

	const dunyaNotice = document.getElementById('kervanDunya2027Notice');
	if (dunyaNotice) dunyaNotice.style.display = (year === '2027') ? 'block' : 'none';

	let visibleCount = 0;
	allCards.forEach(el=>{
		const match = cardMatches(el, from, to, q);
		el.style.display = match ? '' : 'none';
		if(match) visibleCount++;
	});
	highlightCards.forEach(el=>{
		el.style.display = cardMatches(el, from, to, q) ? '' : 'none';
	});
	document.getElementById('highlightWrap')?.style && (document.getElementById('highlightWrap').style.display = highlightCards.some(el=>el.style.display!=='none') ? '' : 'none');

	const sorters = {
		price_asc: (a,b)=> (+a.dataset.price) - (+b.dataset.price),
		price_desc: (a,b)=> (+b.dataset.price) - (+a.dataset.price),
		date_asc: (a,b)=> (+a.dataset.sortdate) - (+b.dataset.sortdate),
		date_desc: (a,b)=> (+b.dataset.lastsortdate) - (+a.dataset.lastsortdate),
		group_tr: (a,b)=> (a.dataset.group==='turkiye'?0:1) - (b.dataset.group==='turkiye'?0:1),
		group_world: (a,b)=> (a.dataset.group==='dunya'?0:1) - (b.dataset.group==='dunya'?0:1),
		cont: (a,b)=> a.dataset.cont.localeCompare(b.dataset.cont),
	};
	let ordered = [...allCards];
	if (sorters[sortVal]) ordered.sort(sorters[sortVal]);
	// Immer am Ende: erst hervorgehobene (Duplikate), dann DOLU, ganz zuletzt Gerçekleşti
	const rank = el => (+el.dataset.past) ? 3 : (+el.dataset.full) ? 2 : (+el.dataset.highlight) ? 1 : 0;
	ordered.sort((a,b)=> rank(a) - rank(b));
	ordered.forEach(el=> tourGrid.appendChild(el));

	document.getElementById('emptyMsg').style.display = (visibleCount===0) ? 'block' : 'none';

	const fc = activeFilterCount();
	const badge = document.getElementById('filterCount');
	badge.style.display = fc>0 ? 'inline' : 'none';
	badge.textContent = fc;
}

document.querySelectorAll('.chip').forEach(b=>b.addEventListener('click',()=>{
	b.classList.toggle('active');
	selectedConts = [...document.querySelectorAll('.chip.active')].map(x=>x.dataset.cont);
	render();
}));
fromSel.addEventListener('change', render);
toSel.addEventListener('change', render);
document.getElementById('sortSelect').addEventListener('change', render);
document.getElementById('searchInput').addEventListener('input', render);
document.getElementById('filterToggle').addEventListener('click', ()=> document.getElementById('filterPanel').classList.toggle('open'));
document.getElementById('clearFilters').addEventListener('click', ()=>{
	selectedConts=[]; document.querySelectorAll('.chip').forEach(x=>x.classList.remove('active'));
	fromSel.value=1; toSel.value=12; document.getElementById('searchInput').value=''; kervanFavOnly=false; document.getElementById('favOnlyToggle').classList.remove('active'); render();
});

function kervanGetFavs(){
	try { return JSON.parse(localStorage.getItem('kervanFavs') || '{}'); } catch(e){ return {}; }
}
function kervanSaveFavs(favs){ localStorage.setItem('kervanFavs', JSON.stringify(favs)); }

function kervanToggleFav(e, el){
	e.preventDefault(); e.stopPropagation();
	const id = el.dataset.favHeart;
	const favs = kervanGetFavs();
	if (favs[id]) {
		delete favs[id];
		el.textContent = '🤍';
	} else {
		favs[id] = Date.now();
		el.textContent = '❤️';
	}
	el.classList.remove('pop'); void el.offsetWidth; el.classList.add('pop');
	kervanSaveFavs(favs);
	kervanRenderFavState();
	kervanUpdateNavHeart();
}

function kervanRenderFavState(){
	const favs = kervanGetFavs();
	document.querySelectorAll('[data-fav-heart]').forEach(el=>{
		const id = el.dataset.favHeart;
		el.textContent = favs[id] ? '❤️' : '🤍';
		const sinceEl = el.closest('.ticket-top').querySelector('[data-fav-since]');
		if (favs[id]) {
			const days = Math.floor((Date.now() - favs[id]) / 86400000);
			if (days >= 1) {
				sinceEl.textContent = days === 1 ? (window.__kervanDe ? 'Seit gestern gemerkt' : 'Dün eklendi') : (window.__kervanDe ? ('Seit ' + days + ' Tagen gemerkt') : (days + ' gün önce eklendi'));
				sinceEl.style.display = 'block';
			} else {
				sinceEl.style.display = 'none';
			}
		} else {
			sinceEl.style.display = 'none';
		}
	});
}

function kervanUpdateNavHeart(){
	const count = Object.keys(kervanGetFavs()).length;
	const badge = document.getElementById('kervanNavFavCount');
	const wrap = document.getElementById('kervanNavFav');
	if (!wrap) return;
	if (count > 0) {
		wrap.style.display = 'inline-flex';
		badge.textContent = count;
	} else {
		wrap.style.display = 'none';
	}
}

// "Nur Favoriten"-Umschalter (eigenständig, kein Kıta-Chip)
var kervanFavOnly = false;
const favToggle = document.getElementById('favOnlyToggle');
favToggle.addEventListener('click', ()=>{
	favToggle.classList.toggle('active');
	kervanFavOnly = favToggle.classList.contains('active');
	render();
});

kervanRenderFavState();
kervanUpdateNavHeart();

// Über die URL (?favoriler=1) direkt den Favoriten-Filter öffnen und aktivieren
if (new URLSearchParams(location.search).get('favoriler') === '1') {
	document.getElementById('filterPanel').classList.add('open');
	favToggle.classList.add('active');
	kervanFavOnly = true;
}

render();
</script>

<div class="tour-wish" style="max-width:640px; margin:30px auto 0; padding:32px 28px; background:var(--sand); border-radius:16px; text-align:center;">
	<p style="font-family:'Fraunces',serif; font-style:italic; font-size:1.05rem; margin:0 0 16px;">"Keşke bu tur olsa da Kervan'la gitsem" dediğiniz bir yer var mı?</p>
	<?php if ( isset( $_GET['tur_istegi'] ) && $_GET['tur_istegi'] === 'ok' ) : ?>
		<p style="color:var(--teal); font-size:.88rem;">Teşekkürler, öneriniz bize ulaştı!</p>
	<?php endif; ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex; gap:10px; flex-wrap:wrap; justify-content:center;">
		<input type="hidden" name="action" value="kervan_tour_wish">
		<?php wp_nonce_field( 'kervan_tour_wish', 'kervan_wish_nonce' ); ?>
		<input type="text" name="kervan_hp2" style="position:absolute; left:-9999px;" tabindex="-1" autocomplete="off">
		<input type="text" name="kervan_wish_place" required placeholder="Örn. Yeni Zelanda, İtalya, Norveç…" style="flex:1; min-width:220px; padding:12px 16px; border-radius:100px; border:1px solid var(--line); font-family:inherit; font-size:.86rem; background:var(--cream);">
		<button type="submit" style="background:var(--ink); color:#fff; border:none; padding:12px 24px; border-radius:100px; font-weight:600; font-size:.86rem; cursor:pointer;">Gönder</button>
	</form>
</div>

<?php get_footer(); ?>

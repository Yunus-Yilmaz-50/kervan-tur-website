<?php
/* Template Name: Afişler */
get_header();

function kervan_render_afis_group_by_term( $term ) {
	$afisler = get_posts( array(
		'post_type'      => 'afis',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'tax_query'      => array( array(
			'taxonomy' => 'afis_grubu',
			'field'    => 'term_id',
			'terms'    => $term->term_id,
		) ),
	) );
	if ( ! $afisler ) return;

	$label    = $term->name;
	$tour_map = kervan_afis_tour_map();

	echo '<h2 style="font-family:var(--font-head); margin:40px 0 16px;">' . esc_html( $label ) . '</h2>';
	foreach ( $afisler as $a ) {
		$file_id = get_post_meta( $a->ID, '_kervan_afis_file', true );
		if ( ! $file_id ) continue;
		$url = wp_get_attachment_url( $file_id );
		$is_pdf = wp_attachment_is( 'pdf', $file_id );
		$tour_id    = isset( $tour_map[ $a->ID ] ) ? (int) $tour_map[ $a->ID ] : 0;
		$tour_badge = $tour_id ? (string) get_post_meta( $tour_id, 'kervan_badge', true ) : '';
		$is_past    = $tour_id && kervan_badge_is_past( $tour_badge );
		$is_full    = $tour_id && ! $is_past && kervan_badge_is_full( $tour_badge );
		$bookable   = $tour_id && ! $is_past && ! $is_full; // nur dann "Tura göz at" und "Kayıt Ol"
		echo '<details class="afis-item" id="afis-' . esc_attr( $a->post_name ) . '">';
		echo '<summary><span class="afis-head"><span>' . esc_html( get_the_title( $a ) ) . '</span>';
		if ( $is_past || $is_full ) {
			echo '<span class="afis-badge ' . ( $is_past ? 'afis-badge-past' : 'afis-badge-full' ) . '">' . esc_html( $tour_badge ) . '</span>';
		}
		echo '</span></summary>';
		echo '<div class="afis-body">';
		if ( $is_pdf ) {
			echo '<embed src="' . esc_url( $url ) . '" type="application/pdf" style="width:100%; height:600px; border-radius:8px;">';
		} else {
			echo '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_the_title( $a ) ) . '" style="max-width:100%; border-radius:8px; display:block;">';
		}
		$share_url = get_permalink( $a ); // gleiche Adresse wie im Admin: /afis/<name>/ leitet auf die Afişler-Seite und klappt die Afiş auf
		echo '<div class="afis-actions' . ( $bookable ? ' has-tour has-reg' : '' ) . '">';
		echo '<a class="afis-btn afis-download" href="' . esc_url( $url ) . '" download target="_blank" rel="noopener" aria-label="' . esc_attr( kervan_t( 'İndir' ) ) . '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg><span class="afis-lbl">' . esc_html( kervan_t( 'İndir' ) ) . '</span></a>';
		if ( $bookable ) {
			echo '<a class="afis-btn afis-tour" href="' . esc_url( get_permalink( $tour_id ) ) . '">' . esc_html( kervan_t( 'Tura göz at' ) ) . '</a>';
			echo '<a class="afis-btn afis-reg" href="' . esc_url( get_permalink( $tour_id ) . '#kayit' ) . '">' . esc_html( kervan_t( 'Kayıt Ol' ) ) . '</a>';
		}
		echo '<button type="button" class="afis-btn afis-share" onclick="kervanShare(this)" data-url="' . esc_url( $share_url ) . '" data-title="' . esc_attr( get_the_title( $a ) ) . '" data-copied="' . esc_attr( kervan_t( 'Link kopyalandı' ) ) . '" data-prompt="' . esc_attr( kervan_t( 'Linki kopyalayın' ) ) . '" aria-label="' . esc_attr( kervan_t( 'Paylaş' ) ) . '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg><span class="afis-lbl">' . esc_html( kervan_t( 'Paylaş' ) ) . '</span></button>';
		echo '</div>';
		echo '</div></details>';
	}
}
?>

<style>
.afisler-wrap{ max-width:900px; margin:0 auto; padding:50px 20px 90px; }
.afis-item{ scroll-margin-top:90px; border:1px solid var(--line); border-radius:12px; padding:18px 20px; margin-bottom:16px; background:var(--cream); }
.afis-item summary{ cursor:pointer; font-weight:600; font-family:var(--font-head); font-size:1.1rem; list-style:none; display:flex; justify-content:space-between; align-items:center; }
.afis-item summary::-webkit-details-marker{ display:none; }
.afis-head{ display:flex; align-items:center; flex-wrap:wrap; gap:10px; min-width:0; }
.afis-badge{ font-family:var(--font-body); font-size:.68rem; font-weight:700; letter-spacing:.04em; line-height:1; padding:5px 10px; border-radius:100px; white-space:nowrap; }
.afis-badge-full{ background:var(--red); color:#fff; }
.afis-badge-past{ background:rgba(var(--charcoal-rgb),.12); color:var(--charcoal); }
.afis-item summary::after{ content:'+'; font-size:1.3rem; color:var(--clay); margin-left:14px; }
.afis-item[open] summary::after{ content:'−'; }
.afis-body{ margin-top:16px; }
.afis-actions{ display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:8px; margin-top:16px; }
.afis-btn{ display:inline-flex; align-items:center; justify-content:center; gap:7px; flex:0 0 auto; padding:10px 15px; border-radius:100px; font-weight:600; font-size:.84rem; line-height:1; text-decoration:none; cursor:pointer; white-space:nowrap; font-family:inherit; border:1px solid transparent; }
.afis-btn svg{ flex-shrink:0; }
.afis-download{ background:var(--ink); color:#fff; }
.afis-tour{ background:transparent; color:var(--clay); border-color:var(--clay); }
.afis-reg{ background:var(--clay); color:#fff; }
.afis-share{ background:transparent; color:var(--ink); border-color:var(--line); }
.afis-share:hover{ border-color:var(--ink); }
.afis-actions.compact .afis-lbl{ display:none; }
.afis-actions.compact .afis-download, .afis-actions.compact .afis-share{ padding:11px 13px; }
.afis-actions.tight{ gap:6px; }
.afis-actions.tight .afis-tour, .afis-actions.tight .afis-reg{ padding:10px 11px; font-size:.8rem; }
</style>

<div class="afisler-wrap">
	<div class="eyebrow" style="font-family:'IBM Plex Mono',monospace; text-transform:uppercase; letter-spacing:.12em; font-size:.85rem; font-weight:700; color:var(--clay-dark);"><?php echo esc_html( kervan_t( 'AFİŞLER' ) ); ?></div>
	<h1 style="font-family:var(--font-head);"><?php echo esc_html( kervan_t( 'Tüm Afişlerimiz' ) ); ?></h1>

	<?php
	$terms = get_terms( array( 'taxonomy' => 'afis_grubu', 'hide_empty' => false, 'meta_key' => 'grubu_sira', 'orderby' => 'meta_value_num', 'order' => 'ASC' ) );
	foreach ( $terms as $term ) {
		kervan_render_afis_group_by_term( $term );
	}
	?>
</div>

<?php kervan_share_assets(); ?>
<script>
(function(){
	function openFromHash(){
		var raw = location.hash.slice(1);
		if(!raw){ return; }
		var el = document.getElementById(raw);
		if(!el){ try { el = document.getElementById(decodeURIComponent(raw)); } catch(e) {} }
		if(el && el.tagName === 'DETAILS'){
			el.open = true;
			el.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	}
	openFromHash();
	window.addEventListener('hashchange', openFromHash);
})();
</script>

<script>
(function(){
	function wrapped(box){
		var kids = box.children;
		if (kids.length < 2) { return false; }
		var top = kids[0].offsetTop;
		for (var i = 1; i < kids.length; i++) { if (kids[i].offsetTop > top + 4) { return true; } }
		return false;
	}
	function fit(box){
		box.classList.remove('compact', 'tight');
		if (!wrapped(box)) { return; }
		box.classList.add('compact');
		if (!wrapped(box)) { return; }
		box.classList.add('tight');
	}
	function fitAll(){
		var boxes = document.querySelectorAll('details.afis-item[open] .afis-actions');
		for (var i = 0; i < boxes.length; i++) { fit(boxes[i]); }
	}
	document.addEventListener('toggle', function(e){
		var d = e.target;
		if (d && d.classList && d.classList.contains('afis-item') && d.open) {
			var b = d.querySelector('.afis-actions');
			if (b) { fit(b); }
		}
	}, true);
	window.addEventListener('resize', fitAll);
	window.addEventListener('load', fitAll);
	if (document.fonts && document.fonts.ready) { document.fonts.ready.then(fitAll); }
})();
</script>

<?php get_footer(); ?>

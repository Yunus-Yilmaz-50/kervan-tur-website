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

	$label = $term->name;

	echo '<h2 style="font-family:\'Fraunces\',serif; margin:40px 0 16px;">' . esc_html( $label ) . '</h2>';
	foreach ( $afisler as $a ) {
		$file_id = get_post_meta( $a->ID, '_kervan_afis_file', true );
		if ( ! $file_id ) continue;
		$url = wp_get_attachment_url( $file_id );
		$is_pdf = wp_attachment_is( 'pdf', $a );
		echo '<details class="afis-item">';
		echo '<summary><span>' . esc_html( get_the_title( $a ) ) . '</span></summary>';
		echo '<div class="afis-body">';
		if ( $is_pdf ) {
			echo '<embed src="' . esc_url( $url ) . '" type="application/pdf" style="width:100%; height:600px; border-radius:8px;">';
		} else {
			echo '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_the_title( $a ) ) . '" style="max-width:100%; border-radius:8px; display:block;">';
		}
		echo '<a class="afis-download" href="' . esc_url( $url ) . '" download target="_blank">⬇ ' . ( 'İndir' ) . '</a>';
		echo '</div></details>';
	}
}
?>

<style>
.afisler-wrap{ max-width:900px; margin:0 auto; padding:50px 20px 90px; }
.afis-item{ border:1px solid var(--line); border-radius:12px; padding:18px 20px; margin-bottom:16px; background:var(--cream); }
.afis-item summary{ cursor:pointer; font-weight:600; font-family:'Fraunces',serif; font-size:1.1rem; list-style:none; display:flex; justify-content:space-between; align-items:center; }
.afis-item summary::-webkit-details-marker{ display:none; }
.afis-item summary::after{ content:'+'; font-size:1.3rem; color:var(--clay); margin-left:14px; }
.afis-item[open] summary::after{ content:'−'; }
.afis-body{ margin-top:16px; }
.afis-download{ display:inline-block; margin-top:14px; background:var(--ink); color:#fff; padding:9px 18px; border-radius:100px; text-decoration:none; font-weight:600; font-size:.84rem; }
</style>

<div class="afisler-wrap">
	<div class="eyebrow" style="font-family:'IBM Plex Mono',monospace; text-transform:uppercase; letter-spacing:.12em; font-size:.85rem; font-weight:700; color:var(--clay-dark);"><?php echo 'AFİŞLER'; ?></div>
	<h1 style="font-family:'Fraunces',serif;"><?php echo 'Tüm Afişlerimiz'; ?></h1>

	<?php
	$terms = get_terms( array( 'taxonomy' => 'afis_grubu', 'hide_empty' => false, 'meta_key' => 'grubu_sira', 'orderby' => 'meta_value_num', 'order' => 'ASC' ) );
	foreach ( $terms as $term ) {
		kervan_render_afis_group_by_term( $term );
	}
	?>
</div>

<?php get_footer(); ?>

<?php
/**
 * Kervan Tur – Theme-Funktionen
 * Enthält: Grundeinrichtung, den Touren-Beitragstyp, die Kıta-Kategorie (Kontinent)
 * und alle Eingabefelder für Touren – bewusst OHNE ACF oder andere Plugins,
 * damit keine jährlichen Lizenzkosten entstehen.
 */

// ---------- Grundeinrichtung ----------
function kervan_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' ); // Titelbild pro Tour
	add_theme_support( 'custom-logo', array( 'height' => 60, 'flex-height' => true, 'flex-width' => true ) ); // eigenes Logo hochladbar unter Design > Website-Details
	register_nav_menus( array(
		'primary' => 'Hauptnavigation',
	) );
}
add_action( 'after_setup_theme', 'kervan_setup' );

// Beim ersten Aktivieren automatisch ein Menü mit Anasayfa/Turlar/İletişim anlegen,
// damit die Navigation nicht leer bleibt, bis jemand manuell eins baut.
function kervan_create_default_menu() {
	if ( wp_get_nav_menu_object( 'Kervan Menü' ) ) {
		return; // schon vorhanden, nichts doppelt anlegen
	}
	$menu_id = wp_create_nav_menu( 'Kervan Menü' );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => 'Anasayfa',
		'menu-item-url'    => home_url( '/' ),
		'menu-item-status' => 'publish',
	) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => 'Turlar',
		'menu-item-url'    => home_url( '/turlar/' ),
		'menu-item-status' => 'publish',
	) );
	set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu_id ) );
}
add_action( 'after_switch_theme', 'kervan_create_default_menu' );

function kervan_enqueue_assets() {
	wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Work+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap', array(), null );
	wp_enqueue_style( 'kervan-style', get_stylesheet_uri(), array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'kervan_enqueue_assets' );

// ---------- Touren als eigener Beitragstyp ----------
function kervan_register_tour_cpt() {
	register_post_type( 'tur', array(
		'labels' => array(
			'name'          => 'Turlar',
			'singular_name' => 'Tur',
			'add_new_item'  => 'Yeni Tur Ekle',
			'edit_item'     => 'Turu Düzenle',
			'all_items'     => 'Tüm Turlar',
		),
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'turlar' ),
		'menu_icon'    => 'dashicons-palmtree',
		'menu_position' => 4,
		'supports'     => array( 'title', 'thumbnail', 'page-attributes' ), // "thumbnail" = Hauptbild, weitere Bilder unten als Galerie-Feld
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'kervan_register_tour_cpt' );

// ---------- Afişler (Flyer-Übersicht) als eigener Beitragstyp ----------
function kervan_register_afis_cpt() {
	register_post_type( 'afis', array(
		'labels' => array(
			'name'          => 'Afişler',
			'singular_name' => 'Afiş',
			'add_new_item'  => 'Yeni Afiş Ekle',
			'edit_item'     => 'Afişi Düzenle',
			'all_items'     => 'Tüm Afişler',
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'afis' ),
		'menu_icon'    => 'dashicons-media-document',
		'menu_position' => 5,
		'supports'     => array( 'title', 'page-attributes' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'kervan_register_afis_cpt' );

function kervan_register_afis_grubu_taxonomy() {
	register_taxonomy( 'afis_grubu', 'afis', array(
		'labels' => array(
			'name'          => 'Afiş Grupları',
			'singular_name' => 'Grup',
		),
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
	) );
}
add_action( 'init', 'kervan_register_afis_grubu_taxonomy' );

// Manuelle Sortierung der Afiş-Gruppen
function kervan_afis_grubu_sira_add_field() {
	echo '<div class="form-field"><label for="grubu_sira">Sıra</label><input type="number" name="grubu_sira" id="grubu_sira" value="0"><p>Küçük sayı önce gösterilir (0, 1, 2 ...).</p></div>';
}
add_action( 'afis_grubu_add_form_fields', 'kervan_afis_grubu_sira_add_field' );

function kervan_afis_grubu_sira_edit_field( $term ) {
	$value = get_term_meta( $term->term_id, 'grubu_sira', true );
	echo '<tr class="form-field"><th><label for="grubu_sira">Sıra</label></th><td><input type="number" name="grubu_sira" id="grubu_sira" value="' . esc_attr( $value ?: 0 ) . '"><p>Küçük sayı önce gösterilir (0, 1, 2 ...).</p></td></tr>';
}
add_action( 'afis_grubu_edit_form_fields', 'kervan_afis_grubu_sira_edit_field' );

function kervan_afis_grubu_sira_save_field( $term_id ) {
	if ( isset( $_POST['grubu_sira'] ) ) {
		update_term_meta( $term_id, 'grubu_sira', intval( $_POST['grubu_sira'] ) );
	}
}
add_action( 'created_afis_grubu', 'kervan_afis_grubu_sira_save_field' );
add_action( 'edited_afis_grubu', 'kervan_afis_grubu_sira_save_field' );

function kervan_add_afis_meta_box() {
	add_meta_box( 'kervan_afis_details', 'Afiş Bilgileri', 'kervan_render_afis_meta_box', 'afis', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'kervan_add_afis_meta_box' );

function kervan_render_afis_meta_box( $post ) {
	wp_nonce_field( 'kervan_save_afis', 'kervan_afis_nonce' );

	$file_id = get_post_meta( $post->ID, '_kervan_afis_file', true );
	echo '<p><label style="font-weight:600;display:block;margin-bottom:4px;">Dosya (Görsel veya PDF)</label>';
	echo '<input type="hidden" id="kervan_afis_file_id" name="kervan_afis_file_id" value="' . esc_attr( $file_id ) . '">';
	echo '<span id="kervan_afis_file_preview">' . ( $file_id ? esc_html( basename( get_attached_file( $file_id ) ) ) : 'Henüz dosya seçilmedi' ) . '</span> ';
	echo '<button type="button" class="button" id="kervan_afis_file_button">Dosya Seç / Değiştir</button></p>';
}

function kervan_save_afis_meta( $post_id ) {
	if ( ! isset( $_POST['kervan_afis_nonce'] ) || ! wp_verify_nonce( $_POST['kervan_afis_nonce'], 'kervan_save_afis' ) ) {
		return;
	}
	if ( isset( $_POST['kervan_afis_file_id'] ) ) {
		update_post_meta( $post_id, '_kervan_afis_file', sanitize_text_field( $_POST['kervan_afis_file_id'] ) );
	}
}
add_action( 'save_post_afis', 'kervan_save_afis_meta' );

function kervan_afis_admin_scripts( $hook ) {
	global $post_type;
	if ( $post_type !== 'afis' ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script( 'media-editor', '
		document.addEventListener("DOMContentLoaded", function() {
			var btn = document.getElementById("kervan_afis_file_button");
			if (!btn) return;
			btn.addEventListener("click", function(e) {
				e.preventDefault();
				var frame = wp.media({ title: "Dosya Seç", multiple: false, library: { type: ["image", "application/pdf"] } });
				frame.on("select", function() {
					var att = frame.state().get("selection").first();
					document.getElementById("kervan_afis_file_id").value = att.id;
					document.getElementById("kervan_afis_file_preview").textContent = att.attributes.filename;
				});
				frame.open();
			});
		});
	' );
}
add_action( 'admin_enqueue_scripts', 'kervan_afis_admin_scripts' );

// Kontinent als Kategorie (im Adminbereich frei erstellbar/löschbar/bearbeitbar)
function kervan_register_kita_taxonomy() {
	register_taxonomy( 'kita', 'tur', array(
		'labels' => array(
			'name'          => 'Kıtalar',
			'singular_name' => 'Kıta',
		),
		'hierarchical'      => true, // funktioniert wie Kategorien: anlegen, umbenennen, löschen im Adminbereich
		'show_admin_column' => true,
		'show_in_rest'      => true,
	) );
}
add_action( 'init', 'kervan_register_kita_taxonomy' );

// ---------- Eingabefelder für jede Tour (eigene Box im Adminbereich, ersetzt ACF) ----------
function kervan_add_tour_meta_box() {
	add_meta_box(
		'kervan_tur_details',
		'Tur Bilgileri',
		'kervan_render_tour_meta_box',
		'tur',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'kervan_add_tour_meta_box' );

function kervan_render_tour_meta_box( $post ) {
	wp_nonce_field( 'kervan_save_tour', 'kervan_tour_nonce' );

	// Bildergalerie über den eingebauten WordPress-Medien-Uploader
	$gallery_ids = get_post_meta( $post->ID, '_kervan_gallery', true );
	echo '<p><label style="font-weight:600;display:block;margin-bottom:4px;">Fotoğraf Galerisi (Hauptbild oben rechts unter "Titelbild" separat einstellen)</label>';
	echo '<input type="hidden" id="kervan_gallery_ids" name="kervan_gallery_ids" value="' . esc_attr( $gallery_ids ) . '">';
	echo '<div id="kervan_gallery_preview" style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px; padding-top:8px;"></div>';
	echo '<script>window.kervanGalleryThumbs = window.kervanGalleryThumbs || {};';
	if ( $gallery_ids ) {
		foreach ( explode( ',', $gallery_ids ) as $gid ) {
			$thumb = wp_get_attachment_image_url( $gid, 'thumbnail' );
			if ( $thumb ) echo 'window.kervanGalleryThumbs["' . esc_js( $gid ) . '"] = "' . esc_url( $thumb ) . '";';
		}
	}
	echo '</script>';
	echo '<button type="button" class="button" id="kervan_gallery_button">Fotoğraf Ekle / Değiştir</button></p>';

	$flyer_id = get_post_meta( $post->ID, '_kervan_flyer', true );
	echo '<p><label style="font-weight:600;display:block;margin-bottom:4px;">Tur Flyer (PDF)</label>';
	echo '<input type="hidden" id="kervan_flyer_id" name="kervan_flyer_id" value="' . esc_attr( $flyer_id ) . '">';
	echo '<span id="kervan_flyer_preview">' . ( $flyer_id ? esc_html( basename( get_attached_file( $flyer_id ) ) ) : 'Henüz PDF seçilmedi' ) . '</span> ';
	echo '<button type="button" class="button" id="kervan_flyer_button">PDF Seç / Değiştir</button></p>';

	echo '<p><label style="font-weight:600;display:block;margin-bottom:4px;">Rezervasyon Kişisi Grubu</label>';
	$group = get_post_meta( $post->ID, 'kervan_group', true );
	$group_list = $group ? explode( ',', $group ) : array( 'turkiye' );
	echo '<label style="margin-right:18px;"><input type="checkbox" name="kervan_group[]" value="turkiye" ' . checked( in_array( 'turkiye', $group_list, true ), true, false ) . '> Türkiye Turları (Mustafa Yılmaz)</label>';
	echo '<label><input type="checkbox" name="kervan_group[]" value="dunya" ' . checked( in_array( 'dunya', $group_list, true ), true, false ) . '> Dünya Turları (Arif + Nuran Yılmaz)</label></p>';
	echo '<p style="opacity:.7;font-size:.85rem;">İkisini de işaretlerseniz üçü de gösterilir — hangi tarihin kime ait olduğunu NOT alanında belirtin.</p>';

	$fields = array(
		'kervan_price'         => 'Fiyat (örn. 1850€)',
		'kervan_days'          => 'Gün Sayısı (örn. 7)',
		'kervan_year'          => 'Yıl(lar) — virgülle ayırın, örn: 2026,2027',
		'kervan_badge'         => 'Rozet metni (örn. SON 2 KİŞİ) – boş bırakılabilir',
		'kervan_highlight'     => 'Öne çıkar mı? (evet / hayır)',
		'kervan_highlight_until' => 'Öne çıkarma bitiş tarihi (YYYY-AA-GG) – istediğiniz kadar uzun/kısa olabilir, 15 gün sadece bir öneridir',
	);
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<p><label style="font-weight:600;display:block;margin-bottom:4px;">' . esc_html( $label ) . '</label>';
		echo '<input type="text" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" style="width:100%;" /></p>';
	}

	$textareas = array(
		'kervan_dates'        => 'Tarihler (her satıra bir tarih, örn. 07.02–13.02.2026)',
		'kervan_places'       => 'Gezilecek Yerler — bir yer adı yazın (örn. Kayseri), altına varsa alt maddeleri "-" ile başlayarak yazın (örn. - Erciyes Dağı). Alt madde yazmazsanız sadece yer adı gösterilir. Çizgisiz bir not için satırı ">" ile başlatın (örn. > Transfer dahil) — bu not kartlarda görünmez.',
		'kervan_included'     => 'Fiyata Dahil (her satıra bir madde)',
		'kervan_not_included' => 'Fiyata Dahil Olmayan (her satıra bir madde)',
		'kervan_notes'        => 'NOT (her satıra bir uyarı)',
	);
	foreach ( $textareas as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		$rows = ( $key === 'kervan_places' ) ? 10 : 4;
		echo '<p><label style="font-weight:600;display:block;margin-bottom:4px;">' . esc_html( $label ) . '</label>';
		echo '<textarea name="' . esc_attr( $key ) . '" rows="' . $rows . '" style="width:100%;">' . esc_textarea( $value ) . '</textarea></p>';
	}
}

function kervan_save_tour_meta( $post_id ) {
	if ( ! isset( $_POST['kervan_tour_nonce'] ) || ! wp_verify_nonce( $_POST['kervan_tour_nonce'], 'kervan_save_tour' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$keys = array(
		'kervan_price', 'kervan_days', 'kervan_year', 'kervan_badge', 'kervan_highlight', 'kervan_highlight_until',
		'kervan_dates', 'kervan_places', 'kervan_included', 'kervan_not_included', 'kervan_notes',
	);
	foreach ( $keys as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_textarea_field( $_POST[ $key ] ) );
		}
	}
	if ( isset( $_POST['kervan_gallery_ids'] ) ) {
		update_post_meta( $post_id, '_kervan_gallery', sanitize_text_field( $_POST['kervan_gallery_ids'] ) );
	}
	if ( isset( $_POST['kervan_flyer_id'] ) ) {
		update_post_meta( $post_id, '_kervan_flyer', sanitize_text_field( $_POST['kervan_flyer_id'] ) );
	}
	if ( isset( $_POST['kervan_group'] ) && is_array( $_POST['kervan_group'] ) ) {
		$clean = array_map( 'sanitize_text_field', $_POST['kervan_group'] );
		update_post_meta( $post_id, 'kervan_group', implode( ',', $clean ) );
	} else {
		update_post_meta( $post_id, 'kervan_group', '' );
	}
}
add_action( 'save_post_tur', 'kervan_save_tour_meta' );

// Medien-Uploader-Skript nur auf der Tour-Bearbeiten-Seite laden
function kervan_admin_scripts( $hook ) {
	global $post_type;
	if ( $post_type !== 'tur' ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script( 'media-editor', '
		document.addEventListener("DOMContentLoaded", function() {
			var galleryIds = (document.getElementById("kervan_gallery_ids").value || "").split(",").filter(Boolean);

			function renderGalleryPreview() {
				var wrap = document.getElementById("kervan_gallery_preview");
				wrap.innerHTML = "";
				galleryIds.forEach(function(id, idx) {
					var box = document.createElement("div");
					box.style.cssText = "position:relative; display:inline-block;";
					var img = document.createElement("img");
					img.src = window.kervanGalleryThumbs[id] || "";
					img.dataset.id = id;
					img.draggable = true;
					img.style.cssText = "border-radius:6px;height:80px;cursor:grab;display:block;";
					img.addEventListener("dragstart", function(e){ e.dataTransfer.setData("text/plain", idx); img.style.opacity=".4"; });
					img.addEventListener("dragend", function(){ img.style.opacity="1"; });
					img.addEventListener("dragover", function(e){ e.preventDefault(); });
					img.addEventListener("drop", function(e){
						e.preventDefault();
						var fromIdx = +e.dataTransfer.getData("text/plain");
						var moved = galleryIds.splice(fromIdx, 1)[0];
						galleryIds.splice(idx, 0, moved);
						document.getElementById("kervan_gallery_ids").value = galleryIds.join(",");
						renderGalleryPreview();
					});
					var removeBtn = document.createElement("button");
					removeBtn.type = "button";
					removeBtn.textContent = "×";
					removeBtn.title = "Kaldır";
					removeBtn.style.cssText = "position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#B3261E; color:#fff; border:2px solid #fff; cursor:pointer; font-size:13px; line-height:1; padding:0;";
					removeBtn.addEventListener("click", function(){
						galleryIds.splice(idx, 1);
						document.getElementById("kervan_gallery_ids").value = galleryIds.join(",");
						renderGalleryPreview();
					});
					box.appendChild(img);
					box.appendChild(removeBtn);
					wrap.appendChild(box);
				});
			}
			window.kervanGalleryThumbs = window.kervanGalleryThumbs || {};

			var btn = document.getElementById("kervan_gallery_button");
			if (btn) btn.addEventListener("click", function(e) {
				e.preventDefault();
				var frame = wp.media({ title: "Fotoğraf Seç", multiple: true, library: { type: "image" } });
				frame.on("select", function() {
					frame.state().get("selection").each(function(att) {
						if (galleryIds.indexOf(String(att.id)) === -1) {
							galleryIds.push(att.id);
							window.kervanGalleryThumbs[att.id] = att.attributes.sizes.thumbnail.url;
						}
					});
					document.getElementById("kervan_gallery_ids").value = galleryIds.join(",");
					renderGalleryPreview();
				});
				frame.open();
			});
			renderGalleryPreview();
			var fbtn = document.getElementById("kervan_flyer_button");
			if (fbtn) fbtn.addEventListener("click", function(e) {
				e.preventDefault();
				var frame = wp.media({ title: "PDF Seç", multiple: false, library: { type: "application/pdf" } });
				frame.on("select", function() {
					var att = frame.state().get("selection").first();
					document.getElementById("kervan_flyer_id").value = att.id;
					document.getElementById("kervan_flyer_preview").textContent = att.attributes.filename;
				});
				frame.open();
			});
		});
	' );
}
add_action( 'admin_enqueue_scripts', 'kervan_admin_scripts' );

/**
 * Kleiner Helfer: wandelt ein mehrzeiliges Textfeld (z.B. Termine, Dahil-Liste)
 * in eine PHP-Liste um – so entsteht ohne Repeater-Feld trotzdem eine strukturierte Liste.
 */
function kervan_lines_to_array( $text ) {
	if ( empty( $text ) ) {
		return array();
	}
	return array_filter( array_map( 'trim', explode( "\n", $text ) ) );
}

/**
 * Wandelt den Gezilecek-Yerler-Text in eine Liste von Orten mit optionalen Unterpunkten um.
 * Eine Zeile ohne "-" davor = neuer Ort. Eine Zeile mit "-" davor = Unterpunkt des letzten Orts.
 */
function kervan_parse_places( $text ) {
	$places = array();
	$current = null;
	foreach ( kervan_lines_to_array( $text ) as $line ) {
		if ( preg_match( '/^-\s*(.+)/', $line, $m ) ) {
			if ( $current !== null ) {
				$places[ $current ][] = $m[1];
			}
		} elseif ( preg_match( '/^>\s*(.+)/', $line, $m ) ) {
			// Hinweiszeile: wird ohne Strich unter dem Ort angezeigt
			if ( $current !== null ) {
				$places[ $current ][] = array( 'note' => $m[1] );
			}
		} else {
			$current = $line;
			if ( ! isset( $places[ $current ] ) ) {
				$places[ $current ] = array();
			}
		}
	}
	return $places;
}

/**
 * Leitet eine kurze Orte-Zusammenfassung (für Kacheln, Suche) direkt aus den
 * Gezilecek-Yerler-Ortsnamen ab, damit man die Orte nicht doppelt pflegen muss.
 */
function kervan_places_pins( $post_id ) {
	$places = kervan_parse_places( get_post_meta( $post_id, 'kervan_places', true ) );
	$names = array();
	foreach ( array_keys( $places ) as $name ) {
		// Hinweise in Klammern (z. B. "(2 gece)") gehören nur auf die Detailseite, nicht auf die Kachel
		$clean = trim( preg_replace( '/\s*\([^)]*\)/u', '', $name ) );
		if ( $clean !== '' ) $names[] = $clean;
	}
	return implode( ', ', $names );
}

/**
 * Prüft, ob der Rozet/Status-Text "DOLU" bzw. "DOLDU" (in beliebiger Schreibweise) enthält.
 */
function kervan_badge_is_full( $badge ) {
	return (bool) preg_match( '/\bdol(?:u|du)\b/iu', (string) $badge );
}

/**
 * Prüft, ob der Rozet/Status-Text "Gerçekleşti" (auch ohne türkische Sonderzeichen) enthält = Tour ist vorbei.
 */
function kervan_badge_is_past( $badge ) {
	$b = strtr( (string) $badge, array( 'İ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ğ' => 'g', 'ğ' => 'g', 'Ü' => 'u', 'ü' => 'u', 'Ş' => 's', 'ş' => 's', 'Ö' => 'o', 'ö' => 'o', 'Ç' => 'c', 'ç' => 'c' ) );
	return (bool) preg_match( '/gercekles/', mb_strtolower( $b, 'UTF-8' ) );
}

function kervan_is_de() {
	return strpos( get_locale(), 'de' ) === 0;
}

/**
 * Gibt bei Deutsch die vom Nutzer selbst gepflegte Übersetzung zurück (Kervan Ayarları),
 * sonst den türkischen Originaltext. So bleibt der Filterbereich frei editierbar.
 */
function kervan_ft( $tr_text ) {
	if ( ! kervan_is_de() ) return $tr_text;
	static $dict = null;
	if ( $dict === null ) {
		$dict = array();
		foreach ( kervan_lines_to_array( kervan_get_option( 'kervan_filter_translations' ) ) as $line ) {
			$parts = explode( '|', $line, 2 );
			if ( count( $parts ) === 2 ) $dict[ trim( $parts[0] ) ] = trim( $parts[1] );
		}
	}
	return isset( $dict[ $tr_text ] ) ? $dict[ $tr_text ] : $tr_text;
}



// ---------- Kervan-Menü mit eigenen Unterseiten pro Bereich ----------
function kervan_add_settings_page() {
	add_menu_page(
		'Kervan Ayarları',
		'Kervan Ayarları',
		'manage_options',
		'kervan-genel',
		function() { kervan_render_settings_page( 'genel', 'Kervan Genel', 'Site genelinde (Footer, İletişim kutuları, sosyal medya vb.) kullanılan bilgiler.' ); },
		'dashicons-palmtree',
		3
	);
	add_submenu_page( 'kervan-genel', 'Anasayfa Ayarları', 'Anasayfa', 'manage_options', 'kervan-anasayfa', function() { kervan_render_settings_page( 'anasayfa', 'Anasayfa Ayarları', 'Sadece anasayfada görünen içerikler (hero, video, rakamlar, Kervan Kültür Turları Farkı).' ); } );
	add_submenu_page( 'kervan-genel', 'İletişim Ayarları', 'İletişim', 'manage_options', 'kervan-iletisim-ayarlari', function() { kervan_render_settings_page( 'iletisim', 'İletişim Ayarları', 'Sadece İletişim sayfasında görünen ek metinler.' ); } );

	// Turlar ayarları, Turlar menüsünün kendi altına eklenir
	add_submenu_page( 'edit.php?post_type=tur', 'Filtreler ve Kayıt', 'Filtreler ve Kayıt', 'manage_options', 'kervan-turlar-ayarlari', function() { kervan_render_settings_page( 'turlar', 'Filtreler ve Kayıt', 'Turlar sayfası filtreleri ve "Nasıl Kayıt Olurum?" görselleriyle ilgili ayarlar.' ); } );
}
add_action( 'admin_menu', 'kervan_add_settings_page' );

add_action( 'admin_menu', 'kervan_add_settings_page' );

function kervan_settings_fields() {
	return array(
		'kervan_hizmet_text'   => array( 'label' => 'Hizmet Devamlılık Cümlesi', 'type' => 'textarea', 'default' => 'Hizmetimiz, Kervan Kültür Turlari / Marti Turizm olarak devam etmektedir.', 'group' => 'iletisim' ),
		'kervan_hero_eyebrow'  => array( 'label' => 'Hero Üst Yazı', 'type' => 'text', 'default' => 'Seyahat et sıhhat bul', 'group' => 'anasayfa' ),
		'kervan_hero_title'    => array( 'label' => 'Ana Başlık (Hero)', 'type' => 'text', 'default' => "Dünyayı güvendiğiniz bir kervanla gezin.", 'group' => 'anasayfa' ),
		'kervan_hero_subtitle' => array( 'label' => 'Alt Yazı / Slogan (Hero, buton altında)', 'type' => 'textarea', 'default' => "Gezen güzel olur, oturan gazel olur.", 'group' => 'anasayfa' ),
		'kervan_hero_button'   => array( 'label' => 'Buton Metni (Hero)', 'type' => 'text', 'default' => '2026 & 2027 Turlarını Gör', 'group' => 'anasayfa' ),
		'kervan_hero_button_show' => array( 'label' => 'Videonun içindeki Buton', 'type' => 'select', 'options' => array( 'show' => 'Göster', 'hide' => 'Gizle' ), 'default' => 'show', 'group' => 'anasayfa' ),
		'kervan_facts_list'    => array( 'label' => 'Fakta Rakamları (Sayı|Metin, her satıra bir tane)', 'type' => 'textarea_big', 'default' => "34.984|Mutlu Gurbetçi\n82|Farklı Şehir\n563|Tamamlanan Tur\n2011|'den beri yolda\n4,8 ★|Google Puanı", 'group' => 'anasayfa' ),
		'kervan_farki_items'   => array( 'label' => 'Kervan Kültür Turları Farkı (her satıra bir madde)', 'type' => 'textarea_big', 'default' => "🏨 Turlarımızda bölgenin en iyi otelleri ve restoranları\n🤲 Namaz vakitlerine riayet gösterilir\n🚫 Turlarımızda extra ücret yok\n💳 Tur ücretinde ödeme kolaylığı\n✈️ Tüm havaalanlarından uçuş imkanı\n🎧 Rehber anlatımı herkese özel kulaklıkla", 'group' => 'anasayfa' ),
		'kervan_hero_video'    => array( 'label' => 'Hero Arkaplan Videosu (mp4 dosya linki — boş bırakılırsa video gösterilmez). Videoyu Medya kütüphanesine yükleyip linkini buraya yapıştırın.', 'type' => 'text', 'default' => '', 'group' => 'anasayfa' ),
		'kervan_hero_video_mobile' => array( 'label' => 'Hero Arkaplan Videosu — MOBİL (dikey 9:16 mp4 linki, isteğe bağlı — boş bırakılırsa telefonda da yukarıdaki video kullanılır)', 'type' => 'text', 'default' => '', 'group' => 'anasayfa' ),
		'kervan_hero_mode' => array( 'label' => 'Hero Video Görünümü', 'type' => 'select', 'options' => array( 'cover' => 'Tam ekran, kırpılmış (bilgisayarda tüm ekran; telefonda mobil video yoksa yatay şerit)', 'cinema' => 'Sinematik: tam ekran, videonun tamamı görünür, üstte ve altta siyah bantlar' ), 'default' => 'cover', 'group' => 'anasayfa' ),
		'kervan_extra_page_id' => array( 'label' => 'Ek Bölüm (Anasayfa) – normal WordPress sayfa editörüyle serbestçe düzenlenebilir bir alan. Yeni kutu/buton/resim eklemek için: önce Sayfalar > Yeni Ekle ile bir sayfa oluşturun, sonra burada seçin.', 'type' => 'page_select', 'default' => '', 'group' => 'anasayfa' ),

		'kervan_whatsapp'      => array( 'label' => 'WhatsApp Numarası (örn. 491624936027)', 'type' => 'text', 'default' => '491624936027', 'group' => 'genel' ),
		'kervan_wa_channel'    => array( 'label' => 'WhatsApp Kanal Linki', 'type' => 'text', 'default' => 'https://whatsapp.com/channel/0029VaFKuQwLNSa59Zjzpx28', 'group' => 'genel' ),
		'kervan_phone'         => array( 'label' => 'Telefon (KERVAN TÜRK)', 'type' => 'text', 'default' => '+90 507 66 33 626', 'group' => 'genel' ),
		'kervan_email'         => array( 'label' => 'E-posta', 'type' => 'text', 'default' => 'info@kervantur.de', 'group' => 'genel' ),
		'kervan_contact1_name' => array( 'label' => 'İletişim Kişisi 1 — Ad', 'type' => 'text', 'default' => 'Mustafa Yılmaz', 'group' => 'genel' ),
		'kervan_contact1_phone' => array( 'label' => 'İletişim Kişisi 1 — Telefon', 'type' => 'text', 'default' => '+49 162 4936027', 'group' => 'genel' ),
		'kervan_contact1_label' => array( 'label' => 'İletişim Kişisi 1 — Sorumluluk', 'type' => 'text', 'default' => 'Türkiye Turları', 'group' => 'genel' ),
		'kervan_contact2_name' => array( 'label' => 'İletişim Kişisi 2 — Ad', 'type' => 'text', 'default' => 'Arif Yılmaz', 'group' => 'genel' ),
		'kervan_contact2_phone' => array( 'label' => 'İletişim Kişisi 2 — Telefon', 'type' => 'text', 'default' => '+49 176 83085180', 'group' => 'genel' ),
		'kervan_contact2_label' => array( 'label' => 'İletişim Kişisi 2 — Sorumluluk', 'type' => 'text', 'default' => 'Dünya Turları', 'group' => 'genel' ),
		'kervan_contact3_name' => array( 'label' => 'İletişim Kişisi 3 — Ad (sadece Dünya Turları rezervasyonlarında ek olarak gösterilir)', 'type' => 'text', 'default' => 'Nuran Yılmaz', 'group' => 'genel' ),
		'kervan_contact3_phone' => array( 'label' => 'İletişim Kişisi 3 — Telefon', 'type' => 'text', 'default' => '+49 174 7846947', 'group' => 'genel' ),
		'kervan_instagram'     => array( 'label' => 'Instagram Linki', 'type' => 'text', 'default' => 'https://www.instagram.com/kervan_kultur_turlari', 'group' => 'genel' ),
		'kervan_instagram_handle' => array( 'label' => 'Instagram Görünen Ad', 'type' => 'text', 'default' => 'kervan_kultur_turlari', 'group' => 'genel' ),
		'kervan_facebook'      => array( 'label' => 'Facebook Linki', 'type' => 'text', 'default' => 'https://www.facebook.com/kervankulturturlari', 'group' => 'genel' ),
		'kervan_facebook_name' => array( 'label' => 'Facebook Görünen Ad', 'type' => 'text', 'default' => 'Kervan Kültür Turlari', 'group' => 'genel' ),
		'kervan_google_url'    => array( 'label' => 'Google Değerlendirme Linki', 'type' => 'text', 'default' => 'https://www.google.com/search?q=Kervan+K%C3%BClt%C3%BCr+Turlari#lrd=0x47bf2de89e120703:0xf1dea6021771e610,3', 'group' => 'genel' ),
		'kervan_avrupatur_url' => array( 'label' => 'avrupatur.de Linki', 'type' => 'text', 'default' => 'https://avrupatur.de/', 'group' => 'genel' ),
		'kervan_ga_id'         => array( 'label' => 'Google Analytics Measurement ID (örn. G-XXXXXXX) — boş bırakılırsa istatistik takibi kapalı olur', 'type' => 'text', 'default' => 'G-MZZDWSYY59', 'group' => 'genel' ),

		'kervan_filter_translations' => array( 'label' => 'Turlar Sayfası Filtre Metinleri — Almanca Çeviriler (format: Türkçe|Almanca, her satıra bir tane, istediğiniz gibi ekleyin/silin)', 'type' => 'textarea_big', 'default' => "Kıta|Kontinent\nTüm Kıtalar|Alle Kontinente\nTarih Aralığı|Zeitraum\nSırala|Sortieren\nFiltrele|Filtern\nTur ara…|Tour suchen…\nFiltreleri Temizle|Filter zurücksetzen\nBu filtreye uygun tur yok.|Für diesen Filter sind keine Touren verfügbar.\nVarsayılan|Standard\nFiyat: Düşükten Yükseğe|Preis: Aufsteigend\nFiyat: Yüksekten Düşüğe|Preis: Absteigend\nTarih: Yakından Uzağa|Datum: Bald zu Später\nTarih: Uzaktan Yakına|Datum: Später zu Bald\nÖnce: Türkiye Turları|Zuerst: Türkei-Touren\nÖnce: Dünya Turları|Zuerst: Welt-Touren\nKıtaya Göre (A-Z)|Nach Kontinent (A-Z)", 'group' => 'turlar' ),
		'kervan_kayit_bilgi_image_tr' => array( 'label' => '"Nasıl Kayıt Olurum?" Açıklama Görseli — Türkiye Turları için', 'type' => 'image', 'default' => '', 'group' => 'turlar' ),
		'kervan_kayit_bilgi_image_dunya' => array( 'label' => '"Nasıl Kayıt Olurum?" Açıklama Görseli — Dünya Turları için', 'type' => 'image', 'default' => '', 'group' => 'turlar' ),
	);
}

function kervan_get_option( $key ) {
	$fields = kervan_settings_fields();
	$default = isset( $fields[ $key ] ) ? $fields[ $key ]['default'] : '';
	return get_option( $key, $default );
}

function kervan_render_settings_page( $group, $title, $desc ) {
	$fields = array_filter( kervan_settings_fields(), function( $f ) use ( $group ) { return $f['group'] === $group; } );

	if ( isset( $_POST['kervan_settings_nonce'] ) && wp_verify_nonce( $_POST['kervan_settings_nonce'], 'kervan_save_settings' ) ) {
		foreach ( $fields as $key => $field ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_option( $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
		echo '<div class="updated"><p>Kaydedildi.</p></div>';
	}
	echo '<div class="wrap"><h1>' . esc_html( $title ) . '</h1><p>' . esc_html( $desc ) . '</p>';
	if ( ! $fields ) {
		echo '<p><em>Bu bölümde henüz düzenlenebilir bir ayar yok.</em></p></div>';
		return;
	}
	echo '<form method="post">';
	wp_nonce_field( 'kervan_save_settings', 'kervan_settings_nonce' );
	foreach ( $fields as $key => $field ) {
		$value = kervan_get_option( $key );
		echo '<h3>' . esc_html( $field['label'] ) . '</h3>';
		if ( $field['type'] === 'textarea_big' ) {
			echo '<textarea name="' . esc_attr( $key ) . '" rows="8" style="width:100%;max-width:700px;">' . esc_textarea( $value ) . '</textarea>';
		} elseif ( $field['type'] === 'textarea' ) {
			echo '<textarea name="' . esc_attr( $key ) . '" rows="3" style="width:100%;max-width:700px;">' . esc_textarea( $value ) . '</textarea>';
		} elseif ( $field['type'] === 'select' ) {
			echo '<select name="' . esc_attr( $key ) . '" style="width:100%;max-width:700px;">';
			foreach ( $field['options'] as $opt_value => $opt_label ) {
				echo '<option value="' . esc_attr( $opt_value ) . '" ' . selected( $value, $opt_value, false ) . '>' . esc_html( $opt_label ) . '</option>';
			}
			echo '</select>';
		} elseif ( $field['type'] === 'page_select' ) {
			echo '<select name="' . esc_attr( $key ) . '" style="width:100%;max-width:700px;"><option value="">— Yok —</option>';
			foreach ( get_pages() as $page ) {
				echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( $value, $page->ID, false ) . '>' . esc_html( $page->post_title ) . '</option>';
			}
			echo '</select>';
		} elseif ( $field['type'] === 'image' ) {
			wp_enqueue_media();
			echo '<input type="hidden" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
			echo '<div id="' . esc_attr( $key ) . '_preview" style="margin-bottom:8px;">';
			if ( $value ) echo wp_get_attachment_image( $value, 'medium', false, array( 'style' => 'border-radius:8px;max-width:260px;' ) );
			echo '</div>';
			echo '<button type="button" class="button kervan-image-picker" data-target="' . esc_attr( $key ) . '">Görsel Seç / Değiştir</button>';
		} else {
			echo '<input type="text" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" style="width:100%;max-width:700px;">';
		}
	}
	echo '<script>
	jQuery(document).on("click", ".kervan-image-picker", function(e){
		e.preventDefault();
		var btn = jQuery(this), target = btn.data("target");
		var frame = wp.media({ title: "Görsel Seç", multiple: false });
		frame.on("select", function(){
			var att = frame.state().get("selection").first().toJSON();
			var url = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : att.url;
			jQuery("#"+target).val(att.id);
			jQuery("#"+target+"_preview").html(jQuery("<img>").attr("src", url).css({borderRadius:"8px", maxWidth:"260px"}));
		});
		frame.open();
	});
	</script>';
	echo '<p><button class="button button-primary">Kaydet</button></p></form></div>';
}

// ---------- Fehlermelde-Formular auf der İletişim-Seite ----------
function kervan_handle_report_issue() {
	if ( ! isset( $_POST['kervan_report_nonce'] ) || ! wp_verify_nonce( $_POST['kervan_report_nonce'], 'kervan_report_issue' ) ) {
		wp_die( 'Güvenlik doğrulaması başarısız.' );
	}
	if ( ! empty( $_POST['kervan_hp'] ) ) { // Honeypot gegen Spam-Bots
		wp_safe_redirect( add_query_arg( 'bildirim', 'ok', wp_get_referer() ) );
		exit;
	}
	$message = isset( $_POST['kervan_report_message'] ) ? sanitize_textarea_field( $_POST['kervan_report_message'] ) : '';
	if ( $message ) {
		$to = kervan_get_option( 'kervan_email' );
		wp_mail( $to, 'Web sitesi hata bildirimi', $message . "\n\nSayfa: " . wp_get_referer() );
	}
	wp_safe_redirect( add_query_arg( 'bildirim', 'ok', wp_get_referer() ) );
	exit;
}
add_action( 'admin_post_kervan_report_issue', 'kervan_handle_report_issue' );
add_action( 'admin_post_nopriv_kervan_report_issue', 'kervan_handle_report_issue' );

function kervan_handle_tour_wish() {
	if ( ! isset( $_POST['kervan_wish_nonce'] ) || ! wp_verify_nonce( $_POST['kervan_wish_nonce'], 'kervan_tour_wish' ) ) {
		wp_die( 'Güvenlik doğrulaması başarısız.' );
	}
	if ( ! empty( $_POST['kervan_hp2'] ) ) {
		wp_safe_redirect( add_query_arg( 'tur_istegi', 'ok', wp_get_referer() ) );
		exit;
	}
	$place = isset( $_POST['kervan_wish_place'] ) ? sanitize_text_field( $_POST['kervan_wish_place'] ) : '';
	if ( $place ) {
		$to = kervan_get_option( 'kervan_email' );
		wp_mail( $to, 'Yeni tur önerisi', "Bir ziyaretçi şu tur önerisinde bulundu:\n\n" . $place );
	}
	wp_safe_redirect( add_query_arg( 'tur_istegi', 'ok', wp_get_referer() ) );
	exit;
}
add_action( 'admin_post_kervan_tour_wish', 'kervan_handle_tour_wish' );
add_action( 'admin_post_nopriv_kervan_tour_wish', 'kervan_handle_tour_wish' );

// ---------- Google Analytics + Cookie-Zustimmung ----------
function kervan_render_analytics() {
	$ga_id = kervan_get_option( 'kervan_ga_id' );
	if ( ! $ga_id ) return;
	?>
	<div id="kervanCookieBanner" style="display:none; position:fixed; bottom:0; left:0; right:0; z-index:999; background:var(--ink,#1B2438); color:#fff; padding:18px 20px; gap:16px; align-items:center; flex-wrap:wrap; justify-content:center;">
		<span style="font-size:.86rem; max-width:520px;">Deneyiminizi iyileştirmek için çerezler kullanıyoruz.</span>
		<button onclick="kervanCookieChoice(true)" style="background:var(--clay,#BD5B3B); color:#fff; border:none; padding:9px 18px; border-radius:100px; font-weight:600; cursor:pointer;">Kabul Et</button>
		<button onclick="kervanCookieChoice(false)" style="background:transparent; color:#fff; border:1px solid rgba(255,255,255,.4); padding:9px 18px; border-radius:100px; cursor:pointer;">Reddet</button>
	</div>
	<script>
	function kervanLoadGA(){
		var s = document.createElement('script');
		s.src = 'https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js( $ga_id ); ?>';
		s.async = true;
		document.head.appendChild(s);
		window.dataLayer = window.dataLayer || [];
		function gtag(){ dataLayer.push(arguments); }
		window.gtag = gtag;
		gtag('js', new Date());
		gtag('config', '<?php echo esc_js( $ga_id ); ?>');
	}
	function kervanCookieChoice(accepted){
		localStorage.setItem('kervanCookieConsent', accepted ? 'yes' : 'no');
		document.getElementById('kervanCookieBanner').style.display = 'none';
		if (accepted) kervanLoadGA();
	}
	document.addEventListener('DOMContentLoaded', function(){
		var choice = localStorage.getItem('kervanCookieConsent');
		if (choice === 'yes') { kervanLoadGA(); }
		else if (choice !== 'no') { document.getElementById('kervanCookieBanner').style.display = 'flex'; }
	});
	// WhatsApp-Klicks als Ereignis tracken (nur wenn Zustimmung erteilt wurde)
	document.addEventListener('click', function(e){
		var link = e.target.closest('a[href*="wa.me"]');
		if (link && localStorage.getItem('kervanCookieConsent') === 'yes' && window.gtag) {
			gtag('event', 'whatsapp_click', { 'page_location': location.href });
		}
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'kervan_render_analytics' );

// ---------- Einmaliger Import: 2026 Turlar von der alten Seite ----------
function kervan_get_import_data() {
	return array(
		array(
			'title' => 'Doğu Karadeniz', 'price' => '1550€', 'days' => '9',
			'group' => 'turkiye', 'kita' => '',
			'dates' => "13.06–21.06.2026\n19.09–27.09.2026 (dolu)",
			'included' => "4-5* Otel ve kahvaltı\nOtobüs seyahati\nAkşam yemekleri\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nTÜRSAB sigortası\nMüze giriş ücretleri\nGidiş-dönüş uçak bileti",
			'notincluded' => "Özel harcamalar\nÖğlen Yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Doğu Karadeniz\n- Trabzon\n- Uzungöl\n- Ayder Yaylası\n- Sümela Manastırı\n- Rize\n- Giresun\n- Ordu\n- Samsun\n- Amasya\n- Çorum\n- Ankara",
		),
		array(
			'title' => 'Güneydoğu', 'price' => '1550€', 'days' => '9',
			'group' => 'turkiye', 'kita' => '',
			'dates' => "13.04–21.04.2026\n25.04–03.05.2026\n02.05–10.05.2026\n09.05–17.05.2026\n16.05–24.05.2026\n30.05–07.06.2026\n26.09–04.10.2026 (dolu)\n03.10–11.10.2026 (dolu)\n10.10–18.10.2026 (dolu)\n17.10–25.10.2026 (dolu)\n24.10–01.11.2026\n31.10–08.11.2026",
			'included' => "5* Otel\nKahvaltı\nOtobüs seyahati\nAkşam yemekleri\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nTÜRSAB sigortası\nMüze kart ve tüm giriş ücretleri\nGidiş-dönüş uçak bileti\nHalfeti Tekne Turu\nZeugma (Göbeklitepe) Müzesi\nSıra gecesi yemekli",
			'notincluded' => "Özel harcamalar\nÖğlen Yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Güneydoğu\n- Gaziantep\n- Halfeti Fırat Nehri Tekne Turu\n- Şanlıurfa – Harran Ovası\n- Göbeklitepe – Viranşehir\n- Atatürk Barajı\n- Mardin – Midyat – Beyazsu\n- Dara Antik Kent\n- Diyarbakır",
		),
		array(
			'title' => 'Balkan-Bosna', 'price' => '1550€', 'days' => '9',
			'group' => 'dunya', 'kita' => 'Avrupa',
			'dates' => "30.05–07.06.2026 (dolu)\n19.09–27.09.2026",
			'included' => "Gidiş-dönüş uçak bileti\n4* Otel ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nProgram dahilinde müze giriş ücretleri",
			'notincluded' => "Özel harcamalar\nÖğle ve akşam yemekleri\nBelirtilmeyen müze giriş ücretleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Bosna-Hersek\n- Saraybosna\n- Mostar\n- Blagaj\n- Konjic\nKaradağ\n- Kotor\nArnavutluk\n- Tiran\n- İşkodra\nMakedonya\n- Üsküp\n- Ohrid\n- Tetova\nKosova\n- Priştina",
		),
		array(
			'title' => 'Doğu Anadolu', 'price' => '1550€', 'days' => '9',
			'group' => 'turkiye', 'kita' => '',
			'dates' => "06.06–14.06.2026\n12.09–20.09.2026 (dolu)",
			'included' => "4-5* Otel ve kahvaltı\nOtobüs seyahati\nAkşam yemekleri\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nTÜRSAB sigortası\nMüze giriş ücretleri\nGidiş-dönüş uçak bileti\nAkdamar Tekne Turu",
			'notincluded' => "Özel harcamalar\nÖğlen Yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Doğu Anadolu\n- Diyarbakır\n- Siirt\n- Malabadi Köprüsü\n- Hz. Veysel Karani\n- Ahlat\n- Van\n- Akdamar Tekne Turu\n- Muradiye Şelalesi\n- Doğubeyazıt İshak Paşa Sarayı\n- Iğdır\n- Kars (Ani Harabeleri)\n- Sarıkamış\n- Erzurum",
		),
		array(
			'title' => 'Endülüs-İspanya/Lizbon-Portekiz', 'price' => '1550€', 'days' => '8',
			'group' => 'dunya', 'kita' => 'Avrupa',
			'dates' => "06.02–13.02.2026\n06.11–13.11.2026 (dolu)",
			'included' => "Gidiş-dönüş uçak bileti\nOtel ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nElhamra Sarayı giriş ücreti\nFlamenko Show giriş ücreti",
			'notincluded' => "Özel harcamalar\nBelirtilmeyen müze girişleri\nÖğle ve akşam yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "İspanya (4 gece 5 gün)\n- Malaga\n- Cordoba\n- Granada\n- Sevilla\nPortekiz (2 gece 3 gün)\n- Lizbon\n- Sintra\n- Cascais\n- Cabo Da Roca",
		),
		array(
			'title' => 'Ürdün', 'price' => '1690€', 'days' => '7',
			'group' => 'dunya', 'kita' => 'Ortadoğu',
			'dates' => "15.05–21.05.2026 (dolu)",
			'included' => "Gidiş-dönüş uçak bileti\nAmman 4* Otel\nPetra 4* Otel\nKahvaltı\nAkşam Yemeği\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nWadi Rum'da çadırda konaklama\nWadi Rum Jeep Safari\nJeraş Antik Kent giriş ücreti\nAmman Antik Kent giriş ücreti\nÖlüdeniz Plajı giriş ücreti\nPetra Antik Kent giriş ücreti\nAkabe Kalesi giriş ücreti\nKızıldeniz Sualtı Bot Turu",
			'notincluded' => "Özel harcamalar\nYemeklerde alınan içecekler, meşrubatlar\nAmman'da bir akşam yemeği",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Ürdün\n- Amman\n- Jeraş\n- Ölüdeniz/Lut Gölü\n- Madaba\n- Nebi Dağı\n- Karak\n- Mute\n- Petra\n- Wadi Rum\n- Akabe",
		),
		array(
			'title' => 'Mısır', 'price' => '2090€', 'days' => '9',
			'group' => 'dunya', 'kita' => 'Afrika',
			'dates' => "18.04–26.04.2026",
			'included' => "Gidiş-dönüş uçak bileti\nKahire-Aswan İç hatlar uçak bileti\n5* Otel konaklama\nKahvaltı ve Akşam Yemeği\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\n3 Gün 5* Nil Cruise Gemide konaklama kahvaltı, öğle yemeği ve akşam yemeği\nProgramda belirtilen müze girişleri\nKızıldeniz'de sualtı bot turu",
			'notincluded' => "Özel harcamalar\nKahire akşam yemeği\nDalış Aktivitesi\nBalon Turu\nÇöl ATV Turu\nProgramda belirtilmeyen müze girişleri\nVize Ücreti 25€",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nArzu eden misafirlerimiz ATV Turu gibi aktivitelere extra katılabilir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Kahire\n- Kahire Milli Müzesi\n- Gizah Keops Piramitleri\n- Sfenks Heykeli\n- Papirüs Atölyesi\n- Kavalalı Mehmet Ali Paşa Camii\n- Selahaddin Eyyubi Kalesi\n- El-Ezher Camii ve Külliyesi\n- El-Muiz Caddesi\nAswan\n- İç hatlardan Aswan'a uçuş\n- Nubian Köyü ziyareti\nNil Nehri\n- 3 gece 5* Nil Nehri Cruise turu, kahvaltı, öğle ve akşam yemeği\nLuksor\n- Karnak Tapınağı\n- Krallar Vadisi\n- Luksor Merkez\nHurgada\n- 5* tatil otelinde konaklama\n- Glassboat Tour, Kızıldeniz'de su altı botundan seyir\n- Otelde deniz keyfi",
		),
		array(
			'title' => 'Özbekistan-Kazakistan-Türkistan', 'price' => '2290€', 'days' => '10',
			'group' => 'dunya', 'kita' => 'Asya',
			'dates' => "01.05–10.05.2026 (dolu)\n12.06–21.06.2026 (yeni)",
			'included' => "Gidiş-dönüş uçak bileti\nİç hat uçak biletleri\nHızlı Tren bileti\n4-5* Otel konaklama\nKahvaltı\nAkşam Yemeği\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nMüze giriş ücretleri",
			'notincluded' => "Özel harcamalar\nÖğlen Yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Özbekistan\n- Semerkant\n- Registan Meydanı\n- Şah-ı Zinde Kompleksi\n- Uluğbey Rasathanesi\n- İmam Buhari Türbesi\n- İmam Maturidi Türbesi\n- Taşkent\n- Barak Han Medresesi\n- Hz. İmam Kompleksi\n- Kukeldaş Medresesi\n- Bağımsızlık Meydanı\n- Buhara\n- Ark Kalesi\n- Bolo Havuz Mescidi\n- Kalan Minare\nKazakistan\n- Türkistan\n- Hoca Ahmet Yesevi Türbesi\n- Kıluet Yeraltı Camii",
		),
		array(
			'title' => 'Güney Kore', 'price' => '2390€', 'days' => '7',
			'group' => 'dunya', 'kita' => 'Uzak Doğu',
			'dates' => "17.10–23.10.2026 (dolu)",
			'included' => "Gidiş-dönüş uçak bileti\nHızlı Tren bileti\n4-5* Otel konaklama ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber",
			'notincluded' => "Özel harcamalar\nAkşam yemekleri\nÖğlen Yemekleri",
			'notes' => "Japonya Turu ile birleştirildiğinde 480€ indirim imkanı (22.10.–30.10.2026), detaylarına ana sayfamızda göz atabilirsiniz.\nDönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Busan\n- El-Fatah Camii\n- Gamcheon Kültür Köyü\n- Biff Meydanı\n- Yonggungsa Tapınağı\n- Haeundae Plajı\n- Nurimaru Apec Evi\n- Shinsegae Centrum City Mağazası\n- Birleşmiş Milletler Anıt Mezarlığı\n- Türk Şehitliği\nSeul\n- Gyeongbok Sarayı\n- Bukchon Hanok Köyü\n- Insadong Geleneksel Pazar ve Kültür Parkı\n- Myeongdong Caddesi\n- Lotte World Tower\n- Coex Mall\n- Starfield Kütüphanesi\n- Gang Nam Style Sembolü",
		),
		array(
			'title' => 'Umre', 'price' => '2690€', 'days' => '11',
			'group' => 'dunya', 'kita' => 'Ortadoğu',
			'dates' => "31.01–10.02.2026\n24.12.2026–03.01.2027 (VIP 3290€)",
			'included' => "Gidiş-dönüş uçak bileti\nMedine-Mekke hızlı tren bileti (VIP tarihte)\nMekke ve Medine'de 5* otellerde yürüme mesafesinde\nKahvaltı\nAkşam yemeği\nKonforlu özel otobüs\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nKulaklık sistemli anlatım\nVize",
			'notincluded' => "Özel harcamalar\nÖğlen yemekleri",
			'notes' => "Türk veya Avrupa ülkeleri vatandaşları için en az 6 ay süre geçerli pasaport/Reisepass.\nEn az 6 ay oturum müsadesi (Türk vatandaşları için).\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Mekke\nMedine",
		),
		array(
			'title' => 'Katar-Singapur-Malezya', 'price' => '2890€', 'days' => '10',
			'group' => 'dunya', 'kita' => 'Uzak Doğu',
			'dates' => "01.10–10.10.2026",
			'included' => "Gidiş-dönüş uçak bileti\nOtel konaklama ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nKatar ve Malezya'da akşam yemekleri",
			'notincluded' => "Özel harcamalar\nÖğle yemekleri\nSingapur'da akşam yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Singapur (2 gece)\n- Gardens by the Bay\n- Marina Bay Sands\n- Sentosa Island\nKatar (2 gece)\n- Doha\n- Souq Waqif Çarşısı\n- Mina Bölgesi\n- Katara Kültür Köyü\n- Education City\n- Ulusal Kütüphane\n- Minaretein Camii\nMalezya (3 gece)\n- Kuala Lumpur\n- Genting Highland\n- Batu Caves Tapınağı\n- Petronas Twin Towers\n- Putrajaya",
		),
		array(
			'title' => 'Kapadokya', 'price' => '550€', 'days' => '6',
			'group' => 'turkiye', 'kita' => '',
			'dates' => "23.06–28.06.2026\n04.09–09.09.2026",
			'included' => "Otel ve Kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber",
			'notincluded' => "Özel harcamalar\nBalon Turu\nATV Motor\nAt Gezisi\nMüze giriş ücreti\nÖğlen Yemekleri\nUçak Bileti",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.",
			'places' => "Nevşehir\n- Kapadokya\n- Ürgüp\n- Göreme\n- Avanos\n- Balon Turu\n- ATV Motor\n- At gezisi\nAksaray\n- İhlara Vadisi\nKayseri",
		),
		array(
			'title' => 'Dubai-Abu Dhabi', 'price' => '1850€', 'days' => '7',
			'group' => 'turkiye', 'kita' => 'Ortadoğu',
			'dates' => "07.02–13.02.2026\n25.12.2026–01.01.2027 (2150€, 8 gün)",
			'included' => "Gidiş-dönüş uçak bileti\n4-5* Otel ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nAbu Dhabi'de öğle yemeği\nÇölde Jeep safari turu akşam yemekli açık büfe\nBurj Khalifa çıkış ücreti\n5* Dhow Cruise gemi turu akşam yemekli açık büfe\nGlobal Village giriş ücreti",
			'notincluded' => "Özel harcamalar\nTürk vatandaşları için vize ücreti\nBelirtilmeyen günlerde akşam yemeği\nMiracle Garden giriş ücreti\nPrograma dahil olmayan giriş ücretleri\nZorunlu Seyahat Sağlık Sigortası",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Dubai\n- Burj al Arab, Marina, Palm Island, Atlantis Hotel, Şeyh Sarayı, Jumeirah Beach için fotoğraf durağı\n- Safari Turu + Çölde Animasyonlu Açık Büfe Akşam Yemeği (4x4 Jeep)\n- Burj Khalifa 124. kat çıkış bileti\n- Dubai Mall\n- Creek Gemi Turu ve Açık Büfe Yemek\n- Fountain Show (Su dansı)\n- Eski Dubai (Gold Souq)\n- Dubai Museum\n- Global Village\n- Şehir Turu\nAbu Dhabi\n- Şehir Turu\n- Hurma Pazarı\n- Şeyh Zayed Camii",
		),
		array(
			'title' => 'İstanbul-Çanakkale-Bursa', 'price' => '1190€', 'days' => '8',
			'group' => 'dunya', 'kita' => '',
			'dates' => "03.04–10.04.2026 (uçak biletli)\n29.08–05.09.2026 (890€, uçak biletsiz)",
			'included' => "5* Otel konaklama ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nKulaklık sistemli anlatım\nBoğaz turu\nMüzekart Ayasofya/Topkapı\nYüzen Çarşı Turu\nMiniatürk giriş ücreti\nPanorama 1453 Fetih Müzesi giriş ücreti\nÇanakkale'de iki akşam yemeği",
			'notincluded' => "Özel harcamalar\nBelirtilmeyen müze girişleri\nÖğlen yemekleri, İstanbul ve Bursa'da akşam yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Istanbul\n- Sultanahmet\n- Ayasofya camii\n- Topkapı Sarayı\n- Eminönü\n- Mısır Çarşısı\n- Kapalı Çarşı\n- Boğaz Turu\n- Kız Kulesi\n- Süleymaniye\n- Miniatürk Müzesi\n- 1453 Fetih Müzesi\n- Eyüp Sultan\n- Her gün yeterince serbest zaman\nÇanakkale\n- Tam gün Şehitlik\n- Aynalı Çarşı\nBursa\n- Ulu Camii\n- Muradiye Külliyesi\n- Emir Sultan Türbesi\n- Tophane\n- Ulu Çarşı",
		),
		array(
			'title' => 'Londra', 'price' => '690€', 'days' => '3',
			'group' => 'dunya', 'kita' => 'Avrupa',
			'dates' => "17.04–19.04.2026\n16.10–18.10.2026",
			'included' => "Gidiş-dönüş uçak bileti\nOtel\nKahvaltı\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nMetro ücreti",
			'notincluded' => "Özel harcamalar\nÖğle ve akşam yemekleri\nLondon Eye\nWestminster Abbey\nTower Hill\nTower Bridge\nAlman vatandaşları için İngiltere ETA vize ücreti (~25€)\nTürk vatandaşları için İngiltere vize ücreti (~250€)",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nİngiltere'ye giriş için ETA veya vizeye tabi olan katılımcılar, başvurularını bireysel olarak ve kendi sorumluluklarında yapmakla yükümlüdür.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Londra\n- Buckingham Palace\n- Borough Market\n- Westminster\n- Big Ben\n- London Eye\n- Piccadilly Circus\n- Trafalgar Square\n- China Town\n- British Museum\n- Victoria & Albert Museum\n- Harrods",
		),
		array(
			'title' => 'Bosna-Hersek', 'price' => '1090€', 'days' => '5',
			'group' => 'dunya', 'kita' => 'Avrupa',
			'dates' => "28.03–01.04.2026",
			'included' => "Gidiş-dönüş uçak bileti\n5* Otel ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nProgram dahilinde müze giriş ücretleri",
			'notincluded' => "Özel harcamalar\nÖğle ve akşam yemekleri\nBelirtilmeyen müze giriş ücretleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Bosna-Hersek\n- Saraybosna\n- Mostar\n- Travnik\n- Blagaj\n- Konjic",
		),
		array(
			'title' => 'Endülüs-İspanya', 'price' => '1090€', 'days' => '5',
			'group' => 'dunya', 'kita' => 'Avrupa',
			'dates' => "08.04–12.04.2026 (dolu)",
			'included' => "Gidiş-dönüş uçak bileti\nOtel ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nElhamra Sarayı giriş ücreti\nFlamenko Show giriş ücreti",
			'notincluded' => "Özel harcamalar\nBelirtilmeyen müze girişleri\nÖğle ve akşam yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "İspanya\n- Malaga\n- Cordoba\n- Granada\n- Sevilla\n- Endülüs",
		),
		array(
			'title' => 'Kıbrıs', 'price' => '1350€', 'days' => '6',
			'group' => 'turkiye', 'kita' => 'Asya',
			'dates' => "04.04–09.04.2026\n07.11–12.11.2026",
			'included' => "Gidiş-dönüş uçak bileti\n5* Otel ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nTÜRSAB sigortası\nMüze girişleri\nAkşam yemekleri",
			'notincluded' => "Özel harcamalar\nÖğlen Yemekleri",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Kıbrıs\n- Lefkoşa\n- Gazi Mağusa\n- Girne\n- Lefke\n- Güzelyurt",
		),
		array(
			'title' => 'Fas-Marokko', 'price' => '1390€', 'days' => '6',
			'group' => 'dunya', 'kita' => 'Afrika',
			'dates' => "13.11–18.11.2026 (dolu)",
			'included' => "Gidiş-dönüş uçak bileti\n4* Otel konaklama\nKahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber",
			'notincluded' => "Özel harcamalar\nAkşam yemekleri\nÖğlen Yemekleri",
			'notes' => "Güney Kore Turu ile birleştirmede indirim imkanı 480€, detaylarına ana sayfamızda göz atabilirsiniz.\nDönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Fas\n- Kazablanka\n- Marakeş\n- Rabat\n- Sale\n- Essaouira",
		),
		array(
			'title' => 'Japonya', 'price' => '3290€', 'days' => '9', 'pins' => 'Osaka, Nara, Kyoto, Tokyo',
			'group' => 'dunya', 'kita' => 'Uzak Doğu',
			'dates' => "22.10–30.10.2026 (dolu)",
			'included' => "Gidiş-dönüş uçak bileti\nHızlı Tren bileti\n4-5* Otel ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber",
			'notincluded' => "Özel harcamalar\nAkşam yemekleri\nÖğlen Yemekleri",
			'notes' => "Güney Kore Turu ile birleştirmede indirim imkanı 480€, detaylarına ana sayfamızda göz atabilirsiniz.\nDönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Osaka\n- Namba Ya Hanshin\n- Dotonbori\n- Shinsaibashi\n- Osaka Kalesi\nNara\n- Heijo-kyo\n- Todaiji Tapınağı\n- Nara Parkı\n- Myeongdong Caddesi\n- Lotte World Tower\n- Coex Mall\n- Starfield Kütüphanesi\n- Gang Nam Style Sembolü\nKyoto\n- Arashiyama\n- Kinkakuji Tapınağı\n- Kiyomizudera Tapınağı\n- Fushimi Inari-Taisha\nTokyo\n- Sensoji Tapınağı\n- Tokyo Kulesi\n- Shibuya Meydanı\n- Kabukicho\n- Ginza\n- Tokyo Camii",
		),
		array(
			'title' => 'Tayland', 'price' => '2290€', 'days' => '10', 'pins' => 'Bangkok, Pattaya, Phuket',
			'group' => 'dunya', 'kita' => 'Uzak Doğu',
			'dates' => "09.10–18.10.2026",
			'included' => "Gidiş-dönüş uçak bileti\nTayland iç hat uçak bileti\n5* Otel konaklama ve kahvaltı\nOtobüs seyahati\nTransferler\nOrganizatör eşliğinde tur\nProfesyonel Türkçe Rehber\nChao Phraya Cruise – akşam yemekli\nBaiyoke Sky Turu – akşam yemekli\nYüzen Çarşı Turu\nBuddha Tapınağı giriş ücreti\nTimsah Çiftliği\nPhuket James Bond ada turu – öğle yemekli\nPattaya Botanik Bahçe\nSiam Niramit Show – akşam yemekli",
			'notincluded' => "Özel harcamalar\nBelirtilmeyen günlerdeki öğlen yemekleri\nBelirtilmeyen günlerdeki akşam yemekleri\nPhi Phi Adası",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyat 2 kişilik odalarda konaklamalar ve erken rezervasyon için geçerlidir, geç başvurularda fiyat farkı oluşabilir. Tek kişilik oda ekstra ücrete tabidir.\nÖn ödeme bilet ücreti, geri kalan ücret turda alınacaktır.\nHizmetimiz Türkiye merkezli Kervan tur/Martı turizm olarak devam edecektir.\nReiserücktritts- und Auslandszusatzkrankenversicherung tavsiye edilir.",
			'places' => "Bangkok\n- Altın Buddha Tapınağı\n- Bangkok Panoramik Şehir Turu\n- Baiyoke Sky Turu\n- Yüzen Çarşı\n- Chao Phraya Dinner Cruise Turu\n- Siam İkizleri Samut Songkram kasabası\n- Maeklong Tren Pazarı\n- Hindistan Cevizi Çiftliği\nPattaya\n- Dünyaca ünlü mücevher atölyesi\n- Timsah çiftliği\n- Dünyanın en büyük Botanik Bahçesi Nong Nooch\nPhuket\n- James Bond adası – öğle yemekli\n- Phi Phi adası – öğle yemekli (extra)\n- Denize girme imkanı\n- Siam Niramit Show – akşam yemekli",
		),

		// ---------- 2027 Türkiye Turları (Mustafa Yılmaz) ----------
		array(
			'title' => 'Doğu Anadolu', 'price' => '1590€', 'days' => '9', 'year' => '2027',
			'group' => 'turkiye', 'kita' => '',
			'dates' => "29.05–06.06.2027 (dolu)\n18.09–26.09.2027",
			'included' => "Gidiş-dönüş uçak bileti\n4 & 5 yıldızlı otellerde konaklama\nLüks otobüs ile ulaşım\nSabah kahvaltıları ve akşam yemekleri\nTransferler\nKervan Kültür Turları organizasyonu\nKokartlı profesyonel rehberlik hizmeti\nAkdamar Adası tekne turu\nMüze giriş ücretleri\nTÜRSAB seyahat sigortası",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyatlar kişi başıdır ve 2 kişilik odalarda konaklama için geçerlidir. Tek kişilik katılımlarda single oda extra ücrete tabidir.\nÜçlü odalarda bazı otellerde 1 yatak ek yatak olma durumu var.\nTur öncesinde veya tur bitişinde tatilinizi uzatabilirsiniz, ya da Türkiye'de memlekette kalabilirsiniz.\nŞartlara göre tarih ve fiyat değişiklikleri olabilir.\nHizmetimiz Kervan Kültür Turları / Marti Turizm olarak devam etmektedir.",
			'places' => "Diyarbakır (2 gece)\n- Diyarbakır\n- Siirt – Malabadi Köprüsü\n- Hz. Veysel Karani Türbesi\nTatvan (1 gece)\n- Ahlat\n- Akdamar Adası Tekne Turu\nVan (1 gece)\n- Van\n- Muradiye Şelalesi\n- Doğubeyazıt – İshak Paşa Sarayı\nKars (2 gece)\n- Iğdır\n- Kars\n- Ani Harabeleri\n- Sarıkamış\nErzurum (2 gece)\n- Erzurum",
		),
		array(
			'title' => 'Doğu Karadeniz', 'price' => '1590€', 'days' => '9', 'year' => '2027',
			'group' => 'turkiye', 'kita' => '',
			'dates' => "05.06–13.06.2027\n25.09–03.10.2027",
			'included' => "Gidiş-dönüş uçak bileti\n4-5 yıldızlı oteller\nLüks otobüs seyahati\nKahvaltı ve akşam yemekleri\nKervan Kültür Turları organizasyonu\nKokartlı profesyonel rehber\nMüze giriş ücretleri\nOrdu Boztepe teleferik ücreti\nTÜRSAB sigortası",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyatlar kişi başıdır ve 2 kişilik odalarda konaklama için geçerlidir. Tek kişilik katılımlarda single oda extra ücrete tabidir.\nÜçlü odalarda bazı otellerde 1 yatak ek yatak olma durumu var.\nTur öncesinde veya tur bitişinde tatilinizi uzatabilirsiniz, ya da Türkiye'de memlekette kalabilirsiniz.\nŞartlara göre tarih ve fiyat değişiklikleri olabilir.\nHizmetimiz Kervan Kültür Turları / Marti Turizm olarak devam etmektedir.",
			'places' => "Trabzon (4 gece)\n- Trabzon\n- Uzungöl\n- Ayder\n- Sümela\n- Rize\n- Giresun\nOrdu (1 gece)\n- Ordu\nAmasya (1 gece)\n- Samsun\n- Amasya\nÇorum (1 gece)\n- Çorum\nAnkara (1 gece)\n- Ankara",
		),
		array(
			'title' => 'Dubai-Abu Dhabi', 'price' => '1850€', 'days' => '7', 'year' => '2027',
			'group' => 'turkiye', 'kita' => 'Ortadoğu',
			'dates' => "20.03–26.03.2027\n10.04–17.04.2027 (2150€, 8 gün)\n25.12.2027–01.01.2028 (2150€, 8 gün, yılbaşı özel)",
			'included' => "Gidiş-dönüş uçak bileti\n5 yıldızlı otellerde konaklama\nLüks otobüs ile ulaşım\nSabah kahvaltıları\nHavalimanı transferleri\nKervan Kültür Turları organizasyonu\nTürkçe profesyonel rehberlik hizmeti\nAbu Dhabi öğle/akşam yemeği (programa göre)\nYemekli çöl safari turu\nYemekli Dhow Cruise tekne turu\nBurj Khalifa giriş bileti\nGlobal Village giriş bileti\nMiracle Garden giriş bileti",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyatlar kişi başıdır ve 2 kişilik odalarda konaklama için geçerlidir. Tek kişilik katılımlarda single oda extra ücrete tabidir.\nŞartlara göre tarih ve fiyat değişiklikleri olabilir.\nHizmetimiz Kervan Kültür Turları / Marti Turizm olarak devam etmektedir.",
			'places' => "Dubai\n- Dubai Şehir Turu\n- Eski Dubai (Gold Souq)\n- Burj Khalifa\n- Dubai Mall\n- Dubai Fountain (Su Dansı Gösterisi)\n- Dubai Creek Tekne Turu\n- Global Village\n- Miracle Garden\n- Safari Turu ve Çöl Deneyimi\n- Hurma Pazarı\n- Serbest Zaman\nAbu Dhabi\n- Abu Dhabi Şehir Turu\n- Şeyh Zayed Camii",
		),
		array(
			'title' => 'Ege', 'price' => '1850€', 'days' => '9', 'year' => '2027',
			'group' => 'turkiye', 'kita' => '',
			'dates' => "12.06–20.06.2027\n19.06–27.06.2027",
			'included' => "Gidiş-dönüş uçak bileti\n4 ve 5 yıldızlı otellerde konaklama\nLüks otobüs ile ulaşım\nSabah kahvaltıları ve akşam yemekleri\nHavalimanı transferleri\nProfesyonel rehberlik hizmeti\nMarmaris Jeep safari öğlen yemekli\nGruba özel Göcek ve Gökova 12 adalar Tekne turu öğlen yemekli\nSeyahat sigortası\nTÜRSAB seyahat sigortası\nKervan Kültür Turları organizasyonu",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyatlar kişi başıdır ve 2 kişilik odalarda konaklama için geçerlidir. Tek kişilik katılımlarda single oda extra ücrete tabidir.\nŞartlara göre tarih ve fiyat değişiklikleri olabilir.\nHizmetimiz Kervan Kültür Turları / Marti Turizm olarak devam etmektedir.",
			'places' => "İzmir\n- İzmir\n- Alaçatı\n- Çeşme\n- Şirince\nMuğla-Aydın Kıyısı\n- Kuşadası\n- Bodrum\n- Marmaris\n- Göcek\n- Fethiye\n- Ölüdeniz\n- Saklıkent\n- Akyaka",
		),
		array(
			'title' => 'Güneydoğu', 'price' => '1590€', 'days' => '9', 'year' => '2027',
			'group' => 'turkiye', 'kita' => '',
			'dates' => "17.04–25.04.2027\n24.04–02.05.2027\n01.05–09.05.2027\n08.05–16.05.2027\n22.05–30.05.2027\n02.10–10.10.2027\n09.10–17.10.2027\n16.10–24.10.2027\n23.10–31.10.2027",
			'included' => "Gidiş-dönüş uçak bileti\n5 yıldızlı oteller\nLüks otobüs seyahati\nKahvaltı ve akşam yemekleri\nKervan Kültür Turları organizasyonu\nKokartlı profesyonel rehber\nMüze kart ve giriş ücretleri\nHalfeti tekne turu\nSıra gecesi yemeği\nTÜRSAB sigortası",
			'notes' => "Dönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyatlar kişi başıdır ve 2 kişilik odalarda konaklama için geçerlidir. Tek kişilik katılımlarda single oda extra ücrete tabidir.\nŞartlara göre tarih ve fiyat değişiklikleri olabilir.\nHizmetimiz Kervan Kültür Turları / Marti Turizm olarak devam etmektedir.",
			'places' => "Gaziantep (2 gece)\n- Gaziantep\n- Göbeklitepe\nŞanlıurfa (2 gece)\n- Halfeti\n- Şanlıurfa\n- Harran Ovası\nMardin (2 gece)\n- Mardin\n- Midyat\n- Beyazsu\n- Dara Antik Kenti\nDiyarbakır (2 gece)\n- Diyarbakır",
		),
		array(
			'title' => 'Kıbrıs/İstanbul', 'price' => '1690€', 'days' => '9', 'year' => '2027',
			'group' => 'turkiye', 'kita' => 'Asya',
			'dates' => "27.03–04.04.2027",
			'included' => "Gidiş-dönüş uçak bileti\n4 ve 5 yıldızlı otellerde konaklama\nLüks otobüs ile ulaşım\nSadece Kıbrıs'ta akşam yemekleri\nHavalimanı transferleri\nProfesyonel rehberlik hizmeti\nMüze giriş ücretleri\nSeyahat sigortası\nTÜRSAB seyahat sigortası\nKervan Kültür Turları organizasyonu",
			'notincluded' => "Özel harcamalar\nİstanbul'da akşam yemekleri",
			'notes' => "6 gün Kıbrıs + 3 gün İstanbul (İstanbul 5 güne uzatılabilir). İsteyen sadece Kıbrıs (6 gün) veya sadece İstanbul (3 gün) turu da yapabilir.\nDönüş tarihinden itibaren 6 ay pasaport geçerlilik süresi gerekmektedir.\nFiyatlar kişi başıdır ve 2 kişilik odalarda konaklama için geçerlidir. Tek kişilik katılımlarda single oda extra ücrete tabidir.\nŞartlara göre tarih ve fiyat değişiklikleri olabilir.\nHizmetimiz Kervan Kültür Turları / Marti Turizm olarak devam etmektedir.",
			'places' => "Kıbrıs\n- Girne\n- Gazimağusa\n- Lefkoşa\n- Güzelyurt\n- Lefke\nİstanbul\n- Sultanahmet, Ayasofya ve Süleymaniye Camii\n- Topkapı Sarayı\n- Eminönü\n- Mısır ve Kapalı Çarşı\n- Boğaz Turu, Kız Kulesi\n- 1453 Fetih Müzesi\n- Eyüp Sultan Türbesi\n- Serbest Zaman",
		),
	);
}

function kervan_cleanup_merged_tours() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Yetki yok.' );
	if ( ! isset( $_GET['kervan_cleanup_nonce'] ) || ! wp_verify_nonce( $_GET['kervan_cleanup_nonce'], 'kervan_cleanup_merged' ) ) wp_die( 'Güvenlik hatası.' );

	$titles = array( 'Doğu Anadolu', 'Doğu Karadeniz', 'Dubai-Abu Dhabi', 'Güneydoğu' );
	$deleted = 0;
	foreach ( $titles as $title ) {
		$posts = get_posts( array( 'post_type' => 'tur', 'title' => $title, 'post_status' => array( 'publish', 'draft', 'pending', 'future', 'private' ), 'numberposts' => -1 ) );
		foreach ( $posts as $p ) {
			wp_delete_post( $p->ID, true );
			$deleted++;
		}
	}
	wp_die( $deleted . ' tur silindi. Şimdi "Şimdi İçe Aktar" ile yeniden ekleyebilirsiniz. <a href="' . esc_url( admin_url( 'edit.php?post_type=tur&page=kervan-import' ) ) . '">Geri dön</a>' );
}
add_action( 'admin_post_kervan_cleanup_merged', 'kervan_cleanup_merged_tours' );

function kervan_run_import() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Yetki yok.' );
	if ( ! isset( $_GET['kervan_import_nonce'] ) || ! wp_verify_nonce( $_GET['kervan_import_nonce'], 'kervan_run_import' ) ) wp_die( 'Güvenlik hatası.' );

	$created = 0;
	$merged = 0;
	foreach ( kervan_get_import_data() as $t ) {
		$year = ! empty( $t['year'] ) ? $t['year'] : '2026';
		$existing = get_posts( array(
			'post_type'   => 'tur',
			'title'       => $t['title'],
			'post_status' => array( 'publish', 'draft', 'pending', 'future', 'private' ), // Papierkorb bewusst ausgeschlossen
			'numberposts' => -1,
		) );

		// Nur überspringen, wenn EXAKT dieser Titel + dieses Jahr schon existiert. Gleicher Titel, anderes Jahr = eigene, separate Tour.
		$dup = false;
		foreach ( $existing as $ep ) {
			$ep_years = array_filter( array_map( 'trim', explode( ',', get_post_meta( $ep->ID, 'kervan_year', true ) ) ) );
			if ( in_array( $year, $ep_years, true ) ) { $dup = true; break; }
		}
		if ( $dup ) continue;

		$post_id = wp_insert_post( array(
			'post_title'  => $t['title'],
			'post_type'   => 'tur',
			'post_status' => 'publish',
		) );
		if ( is_wp_error( $post_id ) || ! $post_id ) continue;

		update_post_meta( $post_id, 'kervan_price', $t['price'] );
		update_post_meta( $post_id, 'kervan_days', $t['days'] );
		update_post_meta( $post_id, 'kervan_year', $year );
		update_post_meta( $post_id, 'kervan_group', $t['group'] );
		update_post_meta( $post_id, 'kervan_dates', $t['dates'] );
		if ( ! empty( $t['places'] ) ) update_post_meta( $post_id, 'kervan_places', $t['places'] );
		update_post_meta( $post_id, 'kervan_included', $t['included'] );
		if ( ! empty( $t['notincluded'] ) ) update_post_meta( $post_id, 'kervan_not_included', $t['notincluded'] );
		if ( ! empty( $t['notes'] ) ) update_post_meta( $post_id, 'kervan_notes', $t['notes'] );
		if ( ! empty( $t['kita'] ) ) wp_set_object_terms( $post_id, $t['kita'], 'kita' );

		$created++;
	}
	wp_die( $created . ' yeni tur eklendi. <a href="' . esc_url( admin_url( 'edit.php?post_type=tur' ) ) . '">Turları görüntüle</a>' );
}
add_action( 'admin_post_kervan_run_import', 'kervan_run_import' );

function kervan_add_import_button() {
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=kervan_run_import' ), 'kervan_run_import', 'kervan_import_nonce' );
	$cleanup_url = wp_nonce_url( admin_url( 'admin-post.php?action=kervan_cleanup_merged' ), 'kervan_cleanup_merged', 'kervan_cleanup_nonce' );
	echo '<div class="wrap"><h1>Turları İçe Aktar</h1><p>Zaten var olan (aynı başlık + aynı yıl) turları atlar, yeni yıl/içerik için ayrı bir tur olarak ekler.</p>';
	echo '<a href="' . esc_url( $url ) . '" class="button button-primary" onclick="return confirm(\'Emin misiniz?\')">Şimdi İçe Aktar</a>';
	echo '<p style="margin-top:24px;"><strong>Sorun mu var?</strong> Doğu Anadolu, Doğu Karadeniz, Dubai-Abu Dhabi ve Güneydoğu turları yanlışlıkla birleştirildiyse, önce bunu çalıştırın, sonra yukarıdakini tekrar tıklayın:</p>';
	echo '<a href="' . esc_url( $cleanup_url ) . '" class="button" onclick="return confirm(\'Bu 4 tur tamamen silinecek. Emin misiniz?\')">Bu 4 Turu Sil ve Yeniden Başla</a></div>';
}
function kervan_register_import_page() {
	add_submenu_page( 'edit.php?post_type=tur', 'İçe Aktar', 'İçe Aktar', 'manage_options', 'kervan-import', 'kervan_add_import_button' );
}
add_action( 'admin_menu', 'kervan_register_import_page' );

// ---------- "Beiträge"-Menü nach unten verschieben (unter "Seiten") ----------
function kervan_move_posts_menu() {
	global $menu;
	foreach ( $menu as $pos => $item ) {
		if ( isset( $item[2] ) && $item[2] === 'edit.php' ) {
			$posts_item = $menu[ $pos ];
			unset( $menu[ $pos ] );
			$menu[21] = $posts_item; // 20 = Seiten, 21 = direkt danach
			break;
		}
	}
}
add_action( 'admin_menu', 'kervan_move_posts_menu', 999 );

// ---------- "yil"-Parameter als bekannt anmelden, damit WordPress ihn beim kanonischen Redirect nicht entfernt ----------
add_filter( 'query_vars', function( $vars ) {
	$vars[] = 'yil';
	return $vars;
} );

// ---------- Turlar-Adminliste: standardmäßig nach der manuellen Sıra sortieren ----------
function kervan_admin_tur_default_order( $query ) {
	if ( is_admin() && $query->is_main_query() && $query->get( 'post_type' ) === 'tur' && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'kervan_admin_tur_default_order' );

// ---------- Titelbild und Sıra als eigene Spalten in der Turlar-Adminliste ----------
function kervan_tur_admin_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( $key === 'title' ) {
			$new['kervan_thumb'] = '';
		}
		$new[ $key ] = $label;
		if ( $key === 'title' ) {
			$new['kervan_sira'] = 'Sıra';
		}
	}
	return $new;
}
add_filter( 'manage_tur_posts_columns', 'kervan_tur_admin_columns' );

function kervan_tur_admin_column_content( $column, $post_id ) {
	if ( $column === 'kervan_thumb' ) {
		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail( $post_id, array( 44, 44 ), array( 'style' => 'border-radius:6px; object-fit:cover;' ) );
		} else {
			echo '<div style="width:44px;height:44px;border-radius:6px;background:#eee;"></div>';
		}
	} elseif ( $column === 'kervan_sira' ) {
		$post = get_post( $post_id );
		echo intval( $post->menu_order );
	}
}
add_action( 'manage_tur_posts_custom_column', 'kervan_tur_admin_column_content', 10, 2 );

function kervan_tur_admin_column_css() {
	$screen = get_current_screen();
	if ( $screen && $screen->id === 'edit-tur' ) {
		echo '<style>.column-kervan_thumb{ width:44px; padding-right:0!important; } .column-title{ padding-left:10px!important; } .column-kervan_sira{ width:60px; }</style>';
	}
}
add_action( 'admin_head', 'kervan_tur_admin_column_css' );

// ---------- Test-Subdomain nur für eingeloggte Admins sichtbar machen ----------
function kervan_restrict_test_subdomain() {
	if ( is_admin() ) return; // Adminbereich selbst nie blockieren, sonst kommt ihr nicht mehr rein
	if ( strpos( home_url(), 'test.kervan-tur.com' ) !== false && ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Bu bir test sitesidir ve sadece yöneticiler tarafından görüntülenebilir.', 'Erişim Kısıtlı', array( 'response' => 403 ) );
	}
}
add_action( 'template_redirect', 'kervan_restrict_test_subdomain' );

<?php
/**
 * SEO: canonical, Open Graph, базовый JSON-LD (настройки темы / WordPress).
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Канонический URL текущего запроса (фронт).
 *
 * @return string Пустая строка, если определить нельзя.
 */
function wpf_get_canonical_url_for_request(): string {
	if ( is_feed() || is_trackback() || is_robots() ) {
		return '';
	}

	if ( is_singular() ) {
		$url = wp_get_canonical_url();
		return is_string( $url ) ? $url : '';
	}

	if ( is_front_page() ) {
		return home_url( '/' );
	}

	if ( is_home() && ! is_front_page() ) {
		$page_id = (int) get_option( 'page_for_posts' );
		return $page_id ? (string) get_permalink( $page_id ) : home_url( '/' );
	}

	if ( is_search() ) {
		return get_search_link();
	}

	if ( is_post_type_archive() ) {
		$pto = get_queried_object();
		if ( $pto instanceof WP_Post_Type ) {
			$link = get_post_type_archive_link( $pto->name );
			return is_string( $link ) ? $link : '';
		}
	}

	if ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$link = get_term_link( $term );
			return is_wp_error( $link ) ? '' : (string) $link;
		}
	}

	if ( is_author() ) {
		return (string) get_author_posts_url( (int) get_queried_object_id() );
	}

	if ( is_date() ) {
		global $wp_query;
		$y = (int) ( $wp_query->query['year'] ?? 0 );
		$m = (int) ( $wp_query->query['monthnum'] ?? 0 );
		$d = (int) ( $wp_query->query['day'] ?? 0 );
		if ( $d && $m && $y ) {
			return (string) get_day_link( $y, $m, $d );
		}
		if ( $m && $y ) {
			return (string) get_month_link( $y, $m );
		}
		if ( $y ) {
			return (string) get_year_link( $y );
		}
	}

	return '';
}

/**
 * Вывод link rel=canonical.
 *
 * @return void
 */
function wpf_output_canonical_link(): void {
	$url = wpf_get_canonical_url_for_request();
	if ( '' === $url ) {
		return;
	}

	printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );
}
add_action( 'wp_head', 'wpf_output_canonical_link', 2 );

/**
 * Open Graph и Twitter Card.
 *
 * @return void
 */
function wpf_output_open_graph_tags(): void {
	if ( is_feed() || is_robots() || is_trackback() ) {
		return;
	}

	$site_name = get_bloginfo( 'name', 'display' );
	$img       = '';

	if ( is_singular() ) {
		$url   = wpf_get_canonical_url_for_request();
		$title = get_the_title();
		$raw   = get_the_excerpt();
		if ( '' === trim( wp_strip_all_tags( $raw ) ) ) {
			$raw = wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', get_queried_object_id() ) ), 35, '…' );
		}
		$desc = $raw;
		$type = is_front_page() ? 'website' : 'article';

		if ( has_post_thumbnail() ) {
			$thumb = wp_get_attachment_image_url( (int) get_post_thumbnail_id(), 'large' );
			$img   = is_string( $thumb ) ? $thumb : '';
		}
	} elseif ( is_front_page() ) {
		$url   = home_url( '/' );
		$title = $site_name;
		$desc  = get_bloginfo( 'description', 'display' );
		$type  = 'website';
	} else {
		$url   = wpf_get_canonical_url_for_request();
		$title = wp_get_document_title();
		$desc  = get_bloginfo( 'description', 'display' );
		$type  = 'website';
	}

	if ( '' === $url ) {
		$url = home_url( '/' );
	}

	$title = wp_strip_all_tags( (string) $title );
	$desc  = wp_strip_all_tags( (string) $desc );

	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( wp_strip_all_tags( (string) $site_name ) ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_attr( esc_url_raw( $url ) ) );
	printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( get_locale() ) );

	if ( '' !== $desc ) {
		$snippet = wp_trim_words( $desc, 40, '' );
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $snippet ) );
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $snippet ) );
	}

	if ( '' !== $img ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $img ) );
	}

	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
	if ( '' !== $desc ) {
		printf(
			'<meta name="twitter:description" content="%s" />' . "\n",
			esc_attr( wp_trim_words( $desc, 40, '' ) )
		);
	}
	if ( '' !== $img ) {
		printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $img ) );
	}
}
add_action( 'wp_head', 'wpf_output_open_graph_tags', 5 );

/**
 * JSON-LD WebSite / Organization (контакты из настроек темы).
 *
 * @return void
 */
function wpf_output_json_ld(): void {
	if ( is_feed() || is_admin() ) {
		return;
	}

	$data = [
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'name'     => wp_strip_all_tags( get_bloginfo( 'name', 'display' ) ),
		'url'      => home_url( '/' ),
	];

	$email = wpf_get_setting( 'contact_email', '' );
	$phone = wpf_get_setting( 'contact_phone', '' );
	if ( $email || $phone ) {
		$data['publisher'] = [
			'@type' => 'Organization',
			'name'  => wp_strip_all_tags( get_bloginfo( 'name', 'display' ) ),
		];
		if ( $email ) {
			$data['publisher']['email'] = sanitize_email( $email );
		}
		if ( $phone ) {
			$data['publisher']['telephone'] = sanitize_text_field( $phone );
		}
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
	);
}
add_action( 'wp_head', 'wpf_output_json_ld', 8 );

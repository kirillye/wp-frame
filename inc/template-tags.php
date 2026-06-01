<?php
/**
 * Custom template tags for this theme.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Print HTML with meta information for the current post-date/time.
 *
 * @return void
 */
function wpf_posted_on(): void {
	$time_string = '<time class="wpf-post__date" datetime="%1$s">%2$s</time>';

	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = sprintf(
			'<time class="wpf-post__date" datetime="%1$s">%2$s</time> <time class="wpf-post__updated" datetime="%3$s">%4$s</time>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);
	} else {
		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);
	}

	$posted_on = sprintf(
		/* translators: %s: post date. */
		esc_html_x( 'Posted on %s', 'post date', 'wp-frame' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	echo '<span class="wpf-post__meta-item">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Print HTML with meta information for the current author.
 *
 * @return void
 */
function wpf_posted_by(): void {
	$byline = sprintf(
		/* translators: %s: post author. */
		esc_html_x( 'by %s', 'post author', 'wp-frame' ),
		'<span class="wpf-post__author-name"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="wpf-post__meta-item"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Print HTML with meta information for the categories, tags and comments.
 *
 * @return void
 */
function wpf_entry_footer(): void {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		$categories_list = get_the_category_list( esc_html__( ', ', 'wp-frame' ) );
		if ( $categories_list ) {
			printf(
				'<span class="wpf-post__meta-item wpf-post__categories">' . esc_html__( 'Posted in %1$s', 'wp-frame' ) . '</span>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}

		$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'wp-frame' ) );
		if ( $tags_list ) {
			printf(
				'<span class="wpf-post__meta-item wpf-post__tags">' . esc_html__( 'Tagged %1$s', 'wp-frame' ) . '</span>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="wpf-post__meta-item wpf-post__comments">';
		comments_popup_link(
			sprintf(
				wp_kses(
					/* translators: %s: post title */
					__( 'Leave a Comment<span class="wpf-screen-reader-text"> on %s</span>', 'wp-frame' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			)
		);
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Edit <span class="wpf-screen-reader-text">%s</span>', 'wp-frame' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			wp_kses_post( get_the_title() )
		),
		'<span class="wpf-post__edit-link">',
		'</span>'
	);
}

/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * when on single views.
 *
 * @return void
 */
function wpf_post_thumbnail(): void {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
		?>

		<div class="wpf-post__thumbnail">
			<?php the_post_thumbnail( 'large' ); ?>
		</div><!-- .wpf-post__thumbnail -->

	<?php else : ?>

		<a class="wpf-post__thumbnail-link" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail(
				'wpf-card',
				array(
					'alt' => the_title_attribute(
						array(
							'echo' => false,
						)
					),
				)
			);
			?>
		</a>

		<?php
	endif;
}

/**
 * Display pagination for archive pages.
 *
 * @return void
 */
function wpf_pagination(): void {
	$args = array(
		'type'      => 'list',
		'prev_text' => _x( '&laquo; Previous', 'pagination', 'wp-frame' ),
		'next_text' => _x( 'Next &raquo;', 'pagination', 'wp-frame' ),
	);

	$paginate = paginate_links( $args );

	if ( $paginate ) {
		printf(
			'<nav class="wpf-pagination" aria-label="%s">%s</nav>',
			esc_attr__( 'Posts pagination', 'wp-frame' ),
			wp_kses_post( $paginate )
		);
	}
}

/**
 * Display breadcrumbs.
 *
 * @return void
 */
function wpf_breadcrumbs(): void {
	if ( function_exists( 'yoast_breadcrumbs' ) ) {
		yoast_breadcrumbs();
		return;
	}

	// Simple fallback breadcrumbs.
	if ( is_home() || is_front_page() ) {
		return;
	}

	echo '<nav class="wpf-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'wp-frame' ) . '">';
	echo '<span class="wpf-breadcrumbs__item"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'wp-frame' ) . '</a></span>';

	if ( is_category() || is_single() ) {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			echo '<span class="wpf-breadcrumbs__separator"> / </span>';
			echo '<span class="wpf-breadcrumbs__item">' . esc_html( $categories[0]->name ) . '</span>';
		}

		if ( is_single() ) {
			echo '<span class="wpf-breadcrumbs__separator"> / </span>';
			echo '<span class="wpf-breadcrumbs__item wpf-breadcrumbs__item--current">' . esc_html( get_the_title() ) . '</span>';
		}
	} elseif ( is_page() ) {
		echo '<span class="wpf-breadcrumbs__separator"> / </span>';
		echo '<span class="wpf-breadcrumbs__item wpf-breadcrumbs__item--current">' . esc_html( get_the_title() ) . '</span>';
	}

	echo '</nav>';
}

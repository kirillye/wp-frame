<?php
/**
 * The template for displaying the footer.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name = get_bloginfo( 'name' );
$site_desc = get_bloginfo( 'description', 'display' );

$quick_links = [
	[ 'href' => '#benefits', 'label' => __( 'Преимущества WordPress', 'wp-frame' ) ],
	[ 'href' => '#tips',     'label' => __( 'Подсказки разработчику', 'wp-frame' ) ],
];

$tech_stack = [ 'WordPress', 'PHP 8', 'ES Modules', 'BEM' ];
?>

	<footer id="colophon" class="wpf-footer" role="contentinfo">

		<!-- Top: 3-column grid -->
		<div class="wpf-footer__top">
			<div class="wpf-container">
				<div class="wpf-footer__grid">

					<!-- Col 1: Brand -->
					<div class="wpf-footer__col wpf-footer__col--brand">
						<p class="wpf-footer__brand-name">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php echo esc_html( $site_name ); ?>
							</a>
						</p>

						<?php if ( $site_desc ) : ?>
							<p class="wpf-footer__brand-desc">
								<?php echo esc_html( $site_desc ); ?>
							</p>
						<?php endif; ?>

						<div class="wpf-footer__tech">
							<?php foreach ( $tech_stack as $tech ) : ?>
								<span class="wpf-footer__tech-pill"><?php echo esc_html( $tech ); ?></span>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- Col 2: Footer nav menu -->
					<div class="wpf-footer__col">
						<p class="wpf-footer__col-title"><?php esc_html_e( 'Навигация', 'wp-frame' ); ?></p>
						<?php
						if ( has_nav_menu( 'footer' ) ) {
							wp_nav_menu(
								array(
									'theme_location' => 'footer',
									'container'      => false,
									'menu_class'     => 'wpf-footer__menu',
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
						} else {
							// Fallback: quick anchor links
							?>
							<ul class="wpf-footer__menu">
								<?php foreach ( $quick_links as $link ) : ?>
									<li>
										<a href="<?php echo esc_url( $link['href'] ); ?>" class="wpf-footer__menu-link">
											<?php echo esc_html( $link['label'] ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php
						}
						?>
					</div>

					<!-- Col 3: Contact CTA -->
					<div class="wpf-footer__col">
						<p class="wpf-footer__col-title"><?php esc_html_e( 'Контакты', 'wp-frame' ); ?></p>
						<p class="wpf-footer__contact-text">
							<?php esc_html_e( 'Есть вопросы по теме или хотите обсудить проект?', 'wp-frame' ); ?>
						</p>
						<button class="wpf-btn wpf-btn--primary wpf-btn--sm" type="button" data-modal-open="contact">
							<?php esc_html_e( 'Написать нам', 'wp-frame' ); ?>
						</button>
					</div>

				</div><!-- .wpf-footer__grid -->
			</div><!-- .wpf-container -->
		</div><!-- .wpf-footer__top -->

		<!-- Bottom bar -->
		<div class="wpf-footer__bottom">
			<div class="wpf-container wpf-flex wpf-flex--between wpf-footer__bottom-inner">
				<p class="wpf-footer__copyright">
					&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php echo esc_html( $site_name ); ?>
					</a>
				</p>
				<p class="wpf-footer__made-with">
					<?php esc_html_e( 'Built with WordPress + WP Frame', 'wp-frame' ); ?>
				</p>
			</div>
		</div><!-- .wpf-footer__bottom -->

	</footer><!-- #colophon -->

</div><!-- #page -->

<?php get_template_part( 'inc/parts/cookie-banner' ); ?>
<?php get_template_part( 'template-parts/modal-success' ); ?>

<?php wp_footer(); ?>

</body>
</html>

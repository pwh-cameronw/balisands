<?php
/**
 * Dark footer with a wavy top edge.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

// Colour of the section above the wave: the homepage ends on sand.
$bs_wave_from = apply_filters( 'bali_sands_footer_wave_from', is_front_page() ? '#faf6ee' : '#ffffff' );
$bs_abn       = bali_sands_yard( 'bs_abn' );
$bs_phone     = bali_sands_yard( 'bs_phone' );
$bs_legal     = function_exists( 'bs_menu_tree' ) ? bs_menu_tree( 'legal' ) : array();
?>
<footer class="bs-footer">
	<?php bali_sands_wave( $bs_wave_from, '#201c17' ); ?>
	<div class="bs-footer__inner">
		<div class="bs-footer__grid">
			<div>
				<div class="bs-footer__brand"><?php bloginfo( 'name' ); ?></div>
				<p class="bs-footer__blurb"><?php echo esc_html( bali_sands_yard( 'bs_footer_blurb' ) ); ?></p>
			</div>
			<div>
				<h2 class="bs-footer__title"><?php esc_html_e( 'Landscape supplies', 'bali-sands' ); ?></h2>
				<?php bali_sands_footer_links( 'footer_supplies', 'landscape-supplies' ); ?>
			</div>
			<div>
				<h2 class="bs-footer__title"><?php esc_html_e( 'Bali imports', 'bali-sands' ); ?></h2>
				<?php bali_sands_footer_links( 'footer_imports', 'bali-imports' ); ?>
			</div>
			<div>
				<h2 class="bs-footer__title"><?php esc_html_e( 'Find the yard', 'bali-sands' ); ?></h2>
				<ul class="bs-footer__links">
					<?php if ( bali_sands_yard( 'bs_street' ) ) : ?>
						<li><a href="<?php echo esc_url( bali_sands_yard( 'bs_maps_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( bali_sands_yard( 'bs_street' ) ); ?></a></li>
						<li><?php echo esc_html( bali_sands_yard( 'bs_locality' ) ); ?></li>
					<?php endif; ?>
					<?php if ( $bs_phone ) : ?>
						<li><a href="<?php echo esc_url( bs_tel_href(), array( 'tel' ) ); ?>"><?php echo esc_html( $bs_phone ); ?></a></li>
					<?php endif; ?>
					<?php
					if ( function_exists( 'bs_hours_lines' ) ) {
						foreach ( bs_hours_lines() as $bs_line ) {
							echo '<li>' . esc_html( $bs_line ) . '</li>';
						}
					}
					?>
				</ul>
			</div>
		</div>
		<div class="bs-footer__bottom">
			<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?><?php echo $bs_abn ? ' · ABN ' . esc_html( $bs_abn ) : ''; ?></span>
			<?php if ( $bs_legal ) : ?>
				<span class="bs-footer__legal">
					<?php
					$bs_links = array();
					foreach ( $bs_legal as $bs_node ) {
						$bs_links[] = sprintf( '<a href="%s">%s</a>', esc_url( $bs_node['item']->url ), esc_html( $bs_node['item']->title ) );
					}
					echo implode( ' · ', $bs_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</span>
			<?php elseif ( get_privacy_policy_url() ) : ?>
				<span class="bs-footer__legal"><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy', 'bali-sands' ); ?></a></span>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php
/**
 * Green info bar and the main header with dropdown menus.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

$bs_left  = bali_sands_lines( bali_sands_yard( 'bs_topbar_left' ) );
$bs_right = bali_sands_lines( bali_sands_yard( 'bs_topbar_right' ) );
$bs_phone = bali_sands_yard( 'bs_phone' );
$bs_logo  = (int) get_theme_mod( 'custom_logo' );
?>
<header class="bs-header" id="bs-header">
	<?php if ( $bs_left || $bs_right || $bs_phone ) : ?>
		<div class="bs-topbar">
			<div class="bs-topbar__group">
				<?php foreach ( $bs_left as $i => $bs_line ) : ?>
					<?php if ( $i ) : ?><span class="bs-topbar__sep" aria-hidden="true">|</span><?php endif; ?>
					<span><?php echo esc_html( $bs_line ); ?></span>
				<?php endforeach; ?>
			</div>
			<div class="bs-topbar__group bs-topbar__group--right">
				<?php foreach ( $bs_right as $bs_line ) : ?>
					<span class="bs-topbar__extra"><?php echo esc_html( $bs_line ); ?></span><span class="bs-topbar__sep bs-topbar__extra" aria-hidden="true">|</span>
				<?php endforeach; ?>
				<?php if ( $bs_phone ) : ?>
					<a class="bs-topbar__phone" href="<?php echo esc_url( bs_tel_href(), array( 'tel' ) ); ?>"><?php echo esc_html( $bs_phone ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="bs-bar">
		<a class="bs-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			if ( $bs_logo ) {
				echo wp_get_attachment_image( $bs_logo, 'medium', false, array( 'class' => 'bs-brand__logo', 'alt' => '' ) );
			}
			?>
			<span class="bs-brand__name"><?php bloginfo( 'name' ); ?></span>
		</a>

		<button type="button" class="bs-burger" aria-expanded="false" aria-controls="bs-nav">
			<span class="bs-burger__lines" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'bali-sands' ); ?></span>
		</button>

		<nav class="bs-nav" id="bs-nav" aria-label="<?php esc_attr_e( 'Main', 'bali-sands' ); ?>">
			<?php bali_sands_nav(); ?>
		</nav>
	</div>
</header>

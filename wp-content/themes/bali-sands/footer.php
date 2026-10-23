<?php
/**
 * Site footer. An Elementor Pro Theme Builder footer, if one is set, replaces it.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'template-parts/site-footer' );
}

wp_footer();
?>
</body>
</html>

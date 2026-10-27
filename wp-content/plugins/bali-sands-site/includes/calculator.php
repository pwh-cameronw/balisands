<?php
/**
 * [bs_calculator] — the cubic-metre calculator's inputs and live result.
 *
 * Only the interactive part lives here. The heading, intro text, note and
 * buttons around it are ordinary Elementor widgets.
 *
 * Attributes (all optional):
 *   length="6" width="4" depth="50"  Starting values (m, m, mm).
 *   tipper="8"                        Cubic metres per tipper load.
 *   result_label="You need about"
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'bs_calculator', 'bss_calculator_shortcode' );
add_action( 'wp_enqueue_scripts', 'bss_enqueue_calculator_assets' );

/**
 * Register the calculator's assets.
 */
function bss_register_calculator_assets() {
	wp_register_style( 'bss-calculator', BSS_URL . 'assets/css/calculator.css', array(), bss_asset_ver( 'assets/css/calculator.css' ) );
	wp_register_script( 'bss-calculator', BSS_URL . 'assets/js/calculator.js', array(), bss_asset_ver( 'assets/js/calculator.js' ), true );
}

/**
 * Load the calculator's CSS (in the head) and JS on every front-end page.
 *
 * Both are tiny. Loading them up front means the calculator is styled from
 * the first paint, and still works when Elementor serves the shortcode's
 * HTML from its element cache without running the shortcode again.
 */
function bss_enqueue_calculator_assets() {
	bss_register_calculator_assets();
	wp_enqueue_style( 'bss-calculator' );
	wp_enqueue_script( 'bss-calculator' );
}

/**
 * Render the calculator.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bss_calculator_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'length'       => '6',
			'width'        => '4',
			'depth'        => '50',
			'tipper'       => '8',
			'result_label' => __( 'You need about', 'bali-sands-site' ),
		),
		$atts,
		'bs_calculator'
	);

	if ( ! wp_script_is( 'bss-calculator', 'registered' ) ) {
		bss_register_calculator_assets();
	}
	wp_enqueue_style( 'bss-calculator' );
	wp_enqueue_script( 'bss-calculator' );

	$fields = array(
		'length' => __( 'Length (m)', 'bali-sands-site' ),
		'width'  => __( 'Width (m)', 'bali-sands-site' ),
		'depth'  => __( 'Depth (mm)', 'bali-sands-site' ),
	);

	ob_start();
	?>
	<div class="bs-calc" data-tipper="<?php echo esc_attr( (float) $atts['tipper'] ); ?>">
		<div class="bs-calc__fields">
			<?php foreach ( $fields as $key => $label ) : ?>
				<label class="bs-calc__field">
					<span class="bs-calc__label"><?php echo esc_html( $label ); ?></span>
					<input class="bs-calc__input" type="text" inputmode="decimal" data-calc="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $atts[ $key ] ); ?>">
				</label>
			<?php endforeach; ?>
		</div>
		<div class="bs-calc__result">
			<span class="bs-calc__result-label"><?php echo esc_html( $atts['result_label'] ); ?></span>
			<output class="bs-calc__result-value" data-calc="order" aria-live="polite">—</output>
		</div>
		<div class="bs-calc__load" data-calc="load"></div>
	</div>
	<?php
	return ob_get_clean();
}

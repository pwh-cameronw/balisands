<?php
/**
 * "How many cubes do you need?" band with a live m³ calculator.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class BS_Widget_Calculator extends BS_Widget_Base {

	public function get_name() {
		return 'bs-calculator';
	}

	public function get_title() {
		return __( 'BS Calculator', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-number-field';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bali-sands' ) ) );
		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Label', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Calculator',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'How many cubes do you need?',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => "Put in the size of the area and get the cubic metres back. Get it right before you come down and you won't need a second trip.",
			)
		);
		$this->add_control(
			'note',
			array(
				'label'       => __( 'Second paragraph', 'bali-sands' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => "Not sure which product to use? Call the yard on <strong>{phone}</strong> and we'll tell you.",
				'description' => __( '<strong> and {phone} are allowed.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'anchor',
			array(
				'label'   => __( 'Anchor', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'calculator',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'calc', array( 'label' => __( 'Calculator', 'bali-sands' ) ) );
		foreach ( array(
			'length' => array( __( 'Starting length (m)', 'bali-sands' ), 6 ),
			'width'  => array( __( 'Starting width (m)', 'bali-sands' ), 4 ),
			'depth'  => array( __( 'Starting depth (mm)', 'bali-sands' ), 50 ),
		) as $key => $def ) {
			$this->add_control(
				$key,
				array(
					'label'   => $def[0],
					'type'    => Controls_Manager::NUMBER,
					'min'     => 0,
					'step'    => 0.1,
					'default' => $def[1],
				)
			);
		}
		$this->add_control(
			'advice',
			array(
				'label'       => __( 'Advice under the result', 'bali-sands' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Order a little over rather than under. A second delivery costs more than the extra sand.',
				'description' => __( 'Shown after the number of tipper loads. Load size is set in Yard details.', 'bali-sands' ),
			)
		);
		$this->add_button_controls( 'btn1', 'Get a quote', '#quote', 'amber', __( 'First button', 'bali-sands' ) );
		$this->add_button_controls( 'btn2', 'Call the yard', '{tel}', 'outline', __( 'Second button', 'bali-sands' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$uid    = 'bs-calc-' . $this->get_id();
		$tipper = max( 0.5, (float) bs_yard( 'bs_tipper_m3' ) );
		$l      = (float) $s['length'];
		$w      = (float) $s['width'];
		$d      = (float) $s['depth'];
		$m3     = $l * $w * $d / 1000;
		$order  = max( 0.5, ceil( $m3 * 2 ) / 2 );
		$loads  = $order <= $tipper ? 'Fits in one tipper load' : ceil( $order / $tipper ) . ' tipper loads';
		$id     = sanitize_title( $s['anchor'] );
		?>
		<div class="bs-sec bs-calc"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
			<div class="bs-calc__copy">
				<div class="bs-eyebrow bs-eyebrow--sage"><?php echo esc_html( $s['eyebrow'] ); ?></div>
				<h2 class="bs-h2 bs-h2--light"><?php echo esc_html( $s['title'] ); ?></h2>
				<?php if ( '' !== trim( $s['text'] ) ) : ?>
					<p class="bs-calc__text"><?php echo esc_html( self::tokens( $s['text'] ) ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== trim( $s['note'] ) ) : ?>
					<p class="bs-calc__note"><?php echo self::inline_html( self::tokens( $s['note'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>
			<form class="bs-calc__card" data-bs-calc data-tipper="<?php echo esc_attr( $tipper ); ?>" onsubmit="return false">
				<div class="bs-calc__fields">
					<label for="<?php echo esc_attr( $uid ); ?>-l"><?php esc_html_e( 'Length (m)', 'bali-sands' ); ?>
						<input id="<?php echo esc_attr( $uid ); ?>-l" name="length" type="text" inputmode="decimal" autocomplete="off" value="<?php echo esc_attr( $s['length'] ); ?>">
					</label>
					<label for="<?php echo esc_attr( $uid ); ?>-w"><?php esc_html_e( 'Width (m)', 'bali-sands' ); ?>
						<input id="<?php echo esc_attr( $uid ); ?>-w" name="width" type="text" inputmode="decimal" autocomplete="off" value="<?php echo esc_attr( $s['width'] ); ?>">
					</label>
					<label for="<?php echo esc_attr( $uid ); ?>-d"><?php esc_html_e( 'Depth (mm)', 'bali-sands' ); ?>
						<input id="<?php echo esc_attr( $uid ); ?>-d" name="depth" type="text" inputmode="decimal" autocomplete="off" value="<?php echo esc_attr( $s['depth'] ); ?>">
					</label>
				</div>
				<div class="bs-calc__result">
					<span><?php esc_html_e( 'You need about', 'bali-sands' ); ?></span>
					<output class="bs-calc__order" aria-live="polite" data-out="order"><?php echo esc_html( number_format( $order, 1 ) ); ?> m³</output>
				</div>
				<div class="bs-calc__advice"><span data-out="loads"><?php echo esc_html( $loads ); ?></span>. <?php echo esc_html( $s['advice'] ); ?></div>
				<div class="bs-calc__btns">
					<?php
					$this->print_button( $s, 'btn1' );
					$this->print_button( $s, 'btn2' );
					?>
				</div>
			</form>
		</div>
		<?php
	}
}

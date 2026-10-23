<?php
/**
 * Wavy edge between two sections.
 *
 * Drop it in its own container between two sections. "From" is the colour of
 * the section above, "To" the colour of the section below.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class BS_Widget_Wave extends BS_Widget_Base {

	const SHAPES = array(
		'a' => 'M0,0 H1280 V26 C1110,62 930,60 760,36 C590,12 420,8 260,30 C160,44 70,48 0,38 Z',
		'b' => 'M0,0 H1280 V36 C1180,50 1060,52 920,30 C760,6 600,10 440,34 C290,56 140,58 0,26 Z',
	);

	public function get_name() {
		return 'bs-wave';
	}

	public function get_title() {
		return __( 'BS Wave divider', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-divider-shape';
	}

	public function get_script_depends() {
		return array();
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Wave', 'bali-sands' ) ) );
		$this->add_control(
			'shape',
			array(
				'label'   => __( 'Shape', 'bali-sands' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'a' => __( 'Wave A', 'bali-sands' ),
					'b' => __( 'Wave B', 'bali-sands' ),
				),
				'default' => 'a',
			)
		);
		$this->add_control(
			'from',
			array(
				'label'       => __( 'Colour above', 'bali-sands' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#faf6ee',
				'description' => __( 'Match the section above.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'to',
			array(
				'label'       => __( 'Colour below', 'bali-sands' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#ffffff',
				'description' => __( 'Match the section below.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'flip',
			array(
				'label' => __( 'Flip horizontally', 'bali-sands' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		self::print_wave( $s['shape'], $s['from'], $s['to'], 'yes' === $s['flip'] );
	}

	/**
	 * Print a wave. Also used by the theme footer.
	 *
	 * @param string $shape a|b.
	 * @param string $from  Colour above.
	 * @param string $to    Colour below.
	 * @param bool   $flip  Mirror it.
	 */
	public static function print_wave( $shape, $from, $to, $flip = false ) {
		$path = self::SHAPES[ $shape ] ?? self::SHAPES['a'];
		printf(
			'<div class="bs-wave%s" style="background-color:%s" aria-hidden="true"><svg viewBox="0 0 1280 64" preserveAspectRatio="none" focusable="false"><path d="%s" fill="%s"/></svg></div>',
			$flip ? ' bs-wave--flip' : '',
			esc_attr( $to ),
			esc_attr( $path ),
			esc_attr( $from )
		);
	}
}

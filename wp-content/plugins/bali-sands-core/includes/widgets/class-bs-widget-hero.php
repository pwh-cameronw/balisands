<?php
/**
 * Homepage hero: open badge, headline, buttons, ticks and the yard photo card.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class BS_Widget_Hero extends BS_Widget_Base {

	public function get_name() {
		return 'bs-hero';
	}

	public function get_title() {
		return __( 'BS Hero', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bali-sands' ) ) );

		$this->add_control(
			'show_badge',
			array(
				'label'        => __( 'Show "Open today" badge', 'bali-sands' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'description'  => __( 'Worked out from the hours in Yard details.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Headline', 'bali-sands' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => 'Landscape supplies & Bali imports in Perth',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Intro', 'bali-sands' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => 'Sand, soil, stone and mulch by the scoop or the truckload. Plus pots, statues, water features and huts straight from Bali. One yard in Forrestdale, open seven days.',
			)
		);

		$this->add_button_controls( 'btn1', 'Shop the range', '#landscape-supplies', 'green', __( 'First button', 'bali-sands' ) );
		$this->add_button_controls( 'btn2', 'How much do I need?', '#calculator', 'outline', __( 'Second button', 'bali-sands' ) );

		$checks = new Repeater();
		$checks->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Delivery 7 days',
			)
		);
		$this->add_control(
			'checks',
			array(
				'label'       => __( 'Ticks', 'bali-sands' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $checks->get_controls(),
				'title_field' => '{{{ text }}}',
				'separator'   => 'before',
				'default'     => array(
					array( 'text' => 'Delivery 7 days' ),
					array( 'text' => 'Bulk or bagged' ),
					array( 'text' => 'Trade accounts' ),
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'photo', array( 'label' => __( 'Photo', 'bali-sands' ) ) );
		$this->add_control(
			'image',
			array(
				'label'       => __( 'Photo', 'bali-sands' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => '' ),
				'description' => __( 'The product bays at the yard. Landscape, 1400px wide or more.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'show_card',
			array(
				'label'   => __( 'Show address card', 'bali-sands' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$this->add_control(
			'card_title',
			array(
				'label'       => __( 'Card title', 'bali-sands' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => bs_yard( 'bs_address_short' ),
				'description' => __( 'Leave empty to use the short address from Yard details.', 'bali-sands' ),
				'condition'   => array( 'show_card' => 'yes' ),
			)
		);
		$this->add_control(
			'card_text',
			array(
				'label'       => __( 'Card text', 'bali-sands' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '{hours}',
				'description' => __( '{hours} shows the opening hours from Yard details.', 'bali-sands' ),
				'condition'   => array( 'show_card' => 'yes' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$img    = $s['image']['url'] ?? '';
		$c_head = '' !== trim( $s['card_title'] ) ? $s['card_title'] : bs_yard( 'bs_address_short' );
		$c_text = self::tokens( $s['card_text'] );
		?>
		<div class="bs-sec bs-hero">
			<div class="bs-hero__copy">
				<?php
				if ( 'yes' === $s['show_badge'] ) :
					$status = bs_open_status();
					?>
					<div class="bs-open<?php echo $status['open'] ? '' : ' is-closed'; ?>" data-bs-hours="<?php echo esc_attr( bs_hours_json() ); ?>">
						<span class="bs-open__dot"></span><span class="bs-open__label"><?php echo esc_html( $status['label'] ); ?></span>
					</div>
				<?php endif; ?>
				<h1 class="bs-hero__title"><?php echo esc_html( $s['title'] ); ?></h1>
				<?php if ( '' !== trim( $s['text'] ) ) : ?>
					<p class="bs-hero__text"><?php echo esc_html( self::tokens( $s['text'] ) ); ?></p>
				<?php endif; ?>
				<div class="bs-btns">
					<?php
					$this->print_button( $s, 'btn1' );
					$this->print_button( $s, 'btn2' );
					?>
				</div>
				<?php if ( ! empty( $s['checks'] ) ) : ?>
					<ul class="bs-hero__checks">
						<?php foreach ( $s['checks'] as $check ) : ?>
							<li>✓ <?php echo esc_html( $check['text'] ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="bs-photo bs-photo--hero">
				<?php
				if ( $img ) {
					if ( ! empty( $s['image']['id'] ) ) {
						echo wp_get_attachment_image( (int) $s['image']['id'], 'large', false, array( 'class' => 'bs-photo__img', 'fetchpriority' => 'high', 'loading' => 'eager' ) );
					} else {
						printf( '<img class="bs-photo__img" src="%s" alt="">', esc_url( $img ) );
					}
				}
				if ( 'yes' === $s['show_card'] ) :
					?>
					<div class="bs-photo__card">
						<div class="bs-photo__card-title"><?php echo esc_html( $c_head ); ?></div>
						<div class="bs-photo__card-text"><?php echo esc_html( $c_text ); ?></div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

<?php
/**
 * Photo beside copy, small fact cards and buttons (the Delivery section).
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class BS_Widget_Media_Split extends BS_Widget_Base {

	public function get_name() {
		return 'bs-media-split';
	}

	public function get_title() {
		return __( 'BS Photo + text', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_script_depends() {
		return array();
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bali-sands' ) ) );
		$this->add_control(
			'image',
			array(
				'label'       => __( 'Photo', 'bali-sands' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => '' ),
				'description' => __( 'E.g. the loader filling a truck or trailer.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'image_side',
			array(
				'label'   => __( 'Photo side', 'bali-sands' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'left'  => __( 'Left', 'bali-sands' ),
					'right' => __( 'Right', 'bali-sands' ),
				),
				'default' => 'left',
			)
		);
		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Label', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Delivery',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Delivered across Perth, or take it home yourself',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => "Tipper loads from half a cube upwards, dropped on the verge or the driveway. Bigger Bali pieces can be delivered too. Or bring the ute or trailer and we'll load you up at the yard.",
			)
		);

		$cards = new Repeater();
		$cards->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Bulk loads',
			)
		);
		$cards->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Fact cards', 'bali-sands' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $cards->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => 'Bulk loads',
						'text'  => '0.5–8m³ per tipper',
					),
					array(
						'title' => 'Bagged',
						'text'  => 'For the small jobs',
					),
				),
			)
		);
		$this->add_button_controls( 'btn1', 'Delivery options', '#quote', 'green', __( 'First button', 'bali-sands' ) );
		$this->add_button_controls( 'btn2', 'Get a quote', '#quote', 'outline', __( 'Second button', 'bali-sands' ) );
		$this->add_control(
			'anchor',
			array(
				'label'     => __( 'Anchor', 'bali-sands' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'delivery',
				'separator' => 'before',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$id  = sanitize_title( $s['anchor'] );
		$img = $s['image']['url'] ?? '';
		?>
		<div class="bs-sec bs-split bs-split--<?php echo esc_attr( $s['image_side'] ); ?>"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
			<div class="bs-photo bs-photo--split">
				<?php
				if ( $img ) {
					if ( ! empty( $s['image']['id'] ) ) {
						echo wp_get_attachment_image( (int) $s['image']['id'], 'large', false, array( 'class' => 'bs-photo__img' ) );
					} else {
						printf( '<img class="bs-photo__img" src="%s" alt="" loading="lazy">', esc_url( $img ) );
					}
				}
				?>
			</div>
			<div class="bs-split__copy">
				<div class="bs-eyebrow bs-eyebrow--earth"><?php echo esc_html( $s['eyebrow'] ); ?></div>
				<h2 class="bs-h2"><?php echo esc_html( $s['title'] ); ?></h2>
				<?php if ( '' !== trim( $s['text'] ) ) : ?>
					<p class="bs-split__text"><?php echo esc_html( self::tokens( $s['text'] ) ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $s['cards'] ) ) : ?>
					<div class="bs-facts">
						<?php foreach ( $s['cards'] as $card ) : ?>
							<div class="bs-fact">
								<div class="bs-fact__title"><?php echo esc_html( $card['title'] ); ?></div>
								<?php if ( '' !== trim( $card['text'] ) ) : ?>
									<div class="bs-fact__text"><?php echo esc_html( $card['text'] ); ?></div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="bs-btns bs-btns--md">
					<?php
					$this->print_button( $s, 'btn1' );
					$this->print_button( $s, 'btn2' );
					?>
				</div>
			</div>
		</div>
		<?php
	}
}

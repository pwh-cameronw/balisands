<?php
/**
 * Centred heading over numbered cards ("Why people come back").
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class BS_Widget_Cards extends BS_Widget_Base {

	public function get_name() {
		return 'bs-cards';
	}

	public function get_title() {
		return __( 'BS Numbered cards', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-number-field';
	}

	public function get_script_depends() {
		return array();
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bali-sands' ) ) );
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Why people come back',
			)
		);

		$cards = new Repeater();
		$cards->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Title',
			)
		);
		$cards->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => '',
			)
		);
		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Cards', 'bali-sands' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $cards->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => 'Delivery 7 days',
						'text'  => 'Tipped on the verge or the driveway, anywhere in the metro.',
					),
					array(
						'title' => 'Two shops in one',
						'text'  => 'Pick up the base materials and the pots and statues in the same trip.',
					),
					array(
						'title' => 'Imported direct',
						'text'  => 'Bali pieces come straight from the makers, priced close to wholesale.',
					),
					array(
						'title' => 'Ask us first',
						'text'  => "Tell us about the job at the counter and we'll tell you what you need and how much.",
					),
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$count = count( (array) $s['cards'] );
		?>
		<div class="bs-sec bs-cards">
			<?php if ( '' !== trim( $s['title'] ) ) : ?>
				<h2 class="bs-h2 bs-h2--center"><?php echo esc_html( $s['title'] ); ?></h2>
			<?php endif; ?>
			<ol class="bs-cards__list" style="--bs-cols:<?php echo (int) min( 4, max( 1, $count ) ); ?>">
				<?php foreach ( (array) $s['cards'] as $i => $card ) : ?>
					<li class="bs-card">
						<span class="bs-card__num" aria-hidden="true"><?php echo (int) $i + 1; ?></span>
						<h3 class="bs-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="bs-card__text"><?php echo esc_html( $card['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
		<?php
	}
}

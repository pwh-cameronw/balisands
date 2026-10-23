<?php
/**
 * Three customer review cards.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class BS_Widget_Reviews extends BS_Widget_Base {

	public function get_name() {
		return 'bs-reviews';
	}

	public function get_title() {
		return __( 'BS Reviews', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
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
				'default' => 'What customers say',
			)
		);
		$this->add_control(
			'more_text',
			array(
				'label'   => __( 'Link text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'All Google reviews',
			)
		);
		$this->add_control(
			'more_link',
			array(
				'label'       => __( 'Link', 'bali-sands' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '' ),
				'description' => __( 'Google Business Profile reviews URL. The link is hidden until this is set.', 'bali-sands' ),
			)
		);

		$reviews = new Repeater();
		$reviews->add_control(
			'stars',
			array(
				'label'   => __( 'Stars', 'bali-sands' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 5,
				'default' => 5,
			)
		);
		$reviews->add_control(
			'text',
			array(
				'label'   => __( 'Review', 'bali-sands' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => '[Paste a real Google review here.]',
			)
		);
		$reviews->add_control(
			'name',
			array(
				'label'   => __( 'Name', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '[Customer name]',
			)
		);
		$this->add_control(
			'reviews',
			array(
				'label'       => __( 'Reviews', 'bali-sands' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $reviews->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'stars' => 5,
						'text'  => '[Paste a real Google review here.]',
						'name'  => '[Customer name]',
					),
					array(
						'stars' => 5,
						'text'  => '[Paste a real Google review here.]',
						'name'  => '[Customer name]',
					),
					array(
						'stars' => 5,
						'text'  => '[Paste a real Google review here.]',
						'name'  => '[Customer name]',
					),
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="bs-sec bs-reviews">
			<?php $this->print_head( '', $s['title'], $s['more_text'], $s['more_link'] ); ?>
			<ul class="bs-reviews__list">
				<?php
				foreach ( (array) $s['reviews'] as $review ) :
					$stars = max( 1, min( 5, (int) $review['stars'] ) );
					?>
					<li class="bs-review">
						<div class="bs-review__stars" role="img" aria-label="<?php echo esc_attr( sprintf( '%d out of 5 stars', $stars ) ); ?>"><?php echo esc_html( str_repeat( '★', $stars ) ); ?></div>
						<blockquote class="bs-review__text"><?php echo esc_html( $review['text'] ); ?></blockquote>
						<div class="bs-review__name"><?php echo esc_html( $review['name'] ); ?></div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

<?php
/**
 * A row of product range tiles for one department (Product ranges → Departments).
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class BS_Widget_Range_Grid extends BS_Widget_Base {

	public function get_name() {
		return 'bs-range-grid';
	}

	public function get_title() {
		return __( 'BS Range tiles', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_script_depends() {
		return array();
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bali-sands' ) ) );

		$terms   = get_terms(
			array(
				'taxonomy'   => 'bs_department',
				'hide_empty' => false,
			)
		);
		$options = array();
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}
		$this->add_control(
			'department',
			array(
				'label'       => __( 'Department', 'bali-sands' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $options,
				'default'     => 'landscape-supplies',
				'description' => __( 'Tiles come from Product ranges, in the order set by each range\'s Order field.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Label', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Landscape supplies',
			)
		);
		$this->add_control(
			'tone',
			array(
				'label'   => __( 'Label colour', 'bali-sands' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'earth' => __( 'Terracotta', 'bali-sands' ),
					'green' => __( 'Green', 'bali-sands' ),
				),
				'default' => 'earth',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Everything for the job, in one yard',
			)
		);
		$this->add_control(
			'more_text',
			array(
				'label'   => __( '"View all" text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'View all supplies',
			)
		);
		$this->add_control(
			'more_link',
			array(
				'label'       => __( '"View all" link', 'bali-sands' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '' ),
				'description' => __( 'The link is hidden until this is set.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'anchor',
			array(
				'label'       => __( 'Anchor', 'bali-sands' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'landscape-supplies',
				'description' => __( 'Menu links can jump here with /#anchor.', 'bali-sands' ),
			)
		);
		$this->add_control(
			'spacing',
			array(
				'label'   => __( 'Spacing', 'bali-sands' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'first' => __( 'First of a pair (more space above)', 'bali-sands' ),
					'last'  => __( 'Second of a pair (more space below)', 'bali-sands' ),
					'solo'  => __( 'On its own', 'bali-sands' ),
				),
				'default' => 'first',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$ranges = bs_get_ranges( $s['department'] );
		$id     = sanitize_title( $s['anchor'] );
		?>
		<div class="bs-sec bs-ranges bs-ranges--<?php echo esc_attr( $s['spacing'] ); ?>"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
			<?php $this->print_head( $s['eyebrow'], $s['title'], $s['more_text'], $s['more_link'], $s['tone'] ); ?>
			<?php if ( ! $ranges ) : ?>
				<?php if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
					<p class="bs-empty"><?php esc_html_e( 'No ranges in this department yet. Add them under Product ranges.', 'bali-sands' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<ul class="bs-tiles">
					<?php foreach ( $ranges as $r ) : ?>
						<li class="bs-tile">
							<?php
							$tag   = $r['link'] ? 'a' : 'div';
							$attrs = $r['link'] ? ' href="' . esc_url( $r['link'] ) . '"' : '';
							$bg    = $r['image'] ? "background-image:url('" . esc_url( $r['image'] ) . "')" : ( $r['swatch'] ? 'background-image:' . $r['swatch'] : '' );
							?>
							<<?php echo $tag . $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="bs-tile__inner">
								<span class="bs-tile__media<?php echo $r['image'] ? ' has-photo' : ''; ?>" style="<?php echo esc_attr( $bg ); ?>"></span>
								<span class="bs-tile__body">
									<span class="bs-tile__name"><?php echo esc_html( $r['title'] ); ?></span>
									<?php if ( $r['sub'] ) : ?>
										<span class="bs-tile__sub"><?php echo esc_html( $r['sub'] ); ?></span>
									<?php endif; ?>
								</span>
							</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}
}

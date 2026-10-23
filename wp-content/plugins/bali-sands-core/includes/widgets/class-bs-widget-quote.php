<?php
/**
 * "Tell us about the job": copy, phone and address beside the Gravity quote form.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class BS_Widget_Quote extends BS_Widget_Base {

	public function get_name() {
		return 'bs-quote';
	}

	public function get_title() {
		return __( 'BS Quote form', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_script_depends() {
		return array();
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bali-sands' ) ) );
		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Label', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Get a quote',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Tell us about the job',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => "Add your address if you want it delivered. It changes the price and which truck we send. Not sure what you need? Describe the job and we'll work it out.",
			)
		);
		$this->add_control(
			'show_contact',
			array(
				'label'       => __( 'Show phone and address', 'bali-sands' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => __( 'From Yard details.', 'bali-sands' ),
			)
		);

		$forms = array( '0' => __( 'Quote form made by setup', 'bali-sands' ) );
		if ( class_exists( 'GFAPI' ) ) {
			foreach ( GFAPI::get_forms() as $form ) {
				$forms[ (string) $form['id'] ] = $form['title'];
			}
		}
		$this->add_control(
			'form_id',
			array(
				'label'   => __( 'Gravity form', 'bali-sands' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $forms,
				'default' => '0',
			)
		);
		$this->add_control(
			'anchor',
			array(
				'label'   => __( 'Anchor', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'quote',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$id      = sanitize_title( $s['anchor'] );
		$form_id = (int) $s['form_id'] ? (int) $s['form_id'] : bs_quote_form_id();
		?>
		<div class="bs-sec bs-quote"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
			<div class="bs-quote__copy">
				<div class="bs-eyebrow bs-eyebrow--earth"><?php echo esc_html( $s['eyebrow'] ); ?></div>
				<h2 class="bs-h2"><?php echo esc_html( $s['title'] ); ?></h2>
				<?php if ( '' !== trim( $s['text'] ) ) : ?>
					<p class="bs-quote__text"><?php echo esc_html( self::tokens( $s['text'] ) ); ?></p>
				<?php endif; ?>
				<?php if ( 'yes' === $s['show_contact'] ) : ?>
					<a class="bs-quote__phone" href="<?php echo esc_url( bs_tel_href(), array( 'tel' ) ); ?>"><?php echo esc_html( bs_yard( 'bs_phone' ) ); ?></a>
					<div class="bs-quote__addr"><?php echo esc_html( bs_yard( 'bs_street' ) . ', ' . bs_yard( 'bs_locality' ) ); ?></div>
				<?php endif; ?>
			</div>
			<div class="bs-quote__card">
				<?php
				if ( $form_id && function_exists( 'gravity_form' ) ) {
					echo do_shortcode( sprintf( '[gravityform id="%d" title="false" description="false" ajax="true" theme="gravity-theme"]', $form_id ) );
				} elseif ( current_user_can( 'edit_pages' ) ) {
					echo '<p class="bs-empty">' . esc_html__( 'The quote form shows here once Gravity Forms is active and Tools → Bali Sands setup has been run.', 'bali-sands' ) . '</p>';
				} else {
					printf(
						'<p class="bs-quote__fallback">%s <a href="%s">%s</a>.</p>',
						esc_html__( 'Call the yard for a quote on', 'bali-sands' ),
						esc_url( bs_tel_href(), array( 'tel' ) ),
						esc_html( bs_yard( 'bs_phone' ) )
					);
				}
				?>
			</div>
		</div>
		<?php
	}
}

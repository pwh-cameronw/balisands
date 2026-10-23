<?php
/**
 * The header dropdown menu, for the Elementor Pro header template.
 *
 * Put it in the header bar container. Below 1180px it turns into a burger
 * button with a full-width drawer under the bar.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

class BS_Widget_Header_Menu extends BS_Widget_Base {

	public function get_name() {
		return 'bs-header-menu';
	}

	public function get_title() {
		return __( 'BS Header menu', 'bali-sands' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_keywords() {
		return array( 'menu', 'nav', 'dropdown', 'header' );
	}

	public function get_style_depends() {
		return array( 'bs-header-menu' );
	}

	public function get_script_depends() {
		return array( 'bs-header-menu' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => __( 'Menu', 'bali-sands' ) ) );

		$options = array( '' => __( 'Header location (Appearance → Menus)', 'bali-sands' ) );
		foreach ( wp_get_nav_menus() as $menu ) {
			$options[ (string) $menu->term_id ] = $menu->name;
		}
		$this->add_control(
			'menu',
			array(
				'label'       => __( 'Menu', 'bali-sands' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $options,
				'default'     => '',
				'description' => sprintf(
					/* translators: %s: link to the Menus screen */
					__( 'Edit items, dropdown rows and thumbnails in <a href="%s" target="_blank">Appearance → Menus</a>. Top-level items with children become dropdowns; give an item the CSS class <code>bs-cta</code> to make it the amber "Get a quote" button.', 'bali-sands' ),
					esc_url( admin_url( 'nav-menus.php' ) )
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$menu = $this->get_settings_for_display( 'menu' );
		bs_header_menu( $menu ? (int) $menu : 'primary' );
	}
}

<?php
/**
 * The header dropdown menu, for the Elementor Pro header template.
 *
 * Put it in the header bar container. Below 1180px it turns into a burger
 * button with a full-width drawer under the bar. Colours live in the Style
 * tab; the containers around it carry their own backgrounds.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

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
		$this->add_control(
			'cta_item',
			array(
				'label'       => __( '"Get a quote" menu item', 'bali-sands' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'show'   => __( 'Show', 'bali-sands' ),
					'mobile' => __( 'Mobile drawer only', 'bali-sands' ),
					'hide'   => __( 'Hide', 'bali-sands' ),
				),
				'default'     => 'show',
				'description' => __( 'The menu item with the <code>bs-cta</code> class. Pick "Mobile drawer only" when a Button widget beside the menu does the job on desktop and tablet.', 'bali-sands' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style_links',
			array(
				'label' => __( 'Links', 'bali-sands' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_var( 'link_color', __( 'Colour', 'bali-sands' ), 'link' );
		$this->add_color_var( 'link_active_color', __( 'Hover and current page', 'bali-sands' ), 'link-active' );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .bs-menu .bs-nav__link, {{WRAPPER}} .bs-menu .bs-nav__link:hover, {{WRAPPER}} .bs-menu .bs-nav__link:focus',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style_panel',
			array(
				'label' => __( 'Dropdown panel', 'bali-sands' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_var( 'panel_bg', __( 'Background', 'bali-sands' ), 'panel-bg' );
		$this->add_color_var( 'panel_border', __( 'Border', 'bali-sands' ), 'panel-border' );
		$this->add_color_var( 'row_hover', __( 'Row hover background', 'bali-sands' ), 'row-hover' );
		$this->add_color_var( 'row_name', __( 'Row title', 'bali-sands' ), 'row-name' );
		$this->add_color_var( 'row_sub', __( 'Row description', 'bali-sands' ), 'row-sub' );
		$this->end_controls_section();

		$this->start_controls_section(
			'style_drawer',
			array(
				'label' => __( 'Mobile drawer', 'bali-sands' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_color_var( 'burger_color', __( 'Burger icon', 'bali-sands' ), 'burger' );
		$this->add_color_var( 'drawer_bg', __( 'Drawer background', 'bali-sands' ), 'drawer-bg' );
		$this->add_color_var( 'drawer_panel_bg', __( 'Open dropdown background', 'bali-sands' ), 'drawer-panel-bg' );
		$this->add_color_var( 'cta_bg', __( '"Get a quote" background', 'bali-sands' ), 'cta-bg' );
		$this->add_color_var( 'cta_color', __( '"Get a quote" text', 'bali-sands' ), 'cta-color' );
		$this->end_controls_section();
	}

	/**
	 * A colour control that sets one of the menu's CSS variables.
	 *
	 * @param string $id    Control id.
	 * @param string $label Label.
	 * @param string $var   Variable name after "--bs-menu-".
	 */
	private function add_color_var( $id, $label, $var ) {
		$this->add_control(
			$id,
			array(
				'label'     => $label,
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .bs-menu' => '--bs-menu-' . $var . ': {{VALUE}};',
				),
			)
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		bs_header_menu( empty( $s['menu'] ) ? 'primary' : (int) $s['menu'], $s['cta_item'] ?? 'show' );
	}
}

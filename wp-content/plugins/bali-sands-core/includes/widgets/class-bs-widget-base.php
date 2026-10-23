<?php
/**
 * Shared bits for the Bali Sands widgets.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Base widget: category, assets and small render helpers.
 */
abstract class BS_Widget_Base extends Widget_Base {

	public function get_categories() {
		return array( 'bali-sands' );
	}

	public function get_style_depends() {
		return array( 'bs-widgets' );
	}

	public function get_script_depends() {
		return array( 'bs-widgets' );
	}

	/**
	 * Button styles used across the design.
	 *
	 * @return array<string,string>
	 */
	protected static function button_styles() {
		return array(
			'green'   => __( 'Green', 'bali-sands' ),
			'amber'   => __( 'Amber', 'bali-sands' ),
			'outline' => __( 'Outline', 'bali-sands' ),
		);
	}

	/**
	 * Add label / link / style controls for one button.
	 *
	 * @param string $prefix  Control prefix.
	 * @param string $label   Default text.
	 * @param string $url     Default URL.
	 * @param string $style   Default style.
	 * @param string $heading Section heading in the panel.
	 */
	protected function add_button_controls( $prefix, $label, $url, $style, $heading ) {
		$this->add_control(
			$prefix . '_heading',
			array(
				'label'     => $heading,
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			$prefix . '_text',
			array(
				'label'   => __( 'Text', 'bali-sands' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $label,
			)
		);
		$this->add_control(
			$prefix . '_link',
			array(
				'label'   => __( 'Link', 'bali-sands' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => $url ),
			)
		);
		$this->add_control(
			$prefix . '_style',
			array(
				'label'   => __( 'Style', 'bali-sands' ),
				'type'    => Controls_Manager::SELECT,
				'options' => self::button_styles(),
				'default' => $style,
			)
		);
	}

	/**
	 * Print a button from its controls. Nothing is printed when the text is empty.
	 *
	 * @param array  $s      Settings.
	 * @param string $prefix Control prefix.
	 * @param string $extra  Extra classes.
	 */
	protected function print_button( $s, $prefix, $extra = '' ) {
		$text = $s[ $prefix . '_text' ] ?? '';
		if ( '' === trim( $text ) ) {
			return;
		}
		$style = $s[ $prefix . '_style' ] ?? 'green';
		$this->print_link(
			$s[ $prefix . '_link' ] ?? array(),
			$text,
			trim( 'bs-btn bs-btn--' . $style . ' ' . $extra )
		);
	}

	/**
	 * Print an anchor (or a span when there is no URL).
	 *
	 * @param array|string $link  Elementor URL value or plain URL.
	 * @param string       $text  Link text (escaped here).
	 * @param string       $class Classes.
	 */
	protected function print_link( $link, $text, $class ) {
		$link = is_array( $link ) ? $link : array( 'url' => (string) $link );
		$url  = strtr(
			$link['url'] ?? '',
			array(
				'{tel}'  => bs_tel_href(),
				'{maps}' => bs_yard( 'bs_maps_url' ),
			)
		);
		if ( '' === $url ) {
			printf( '<span class="%s">%s</span>', esc_attr( $class ), esc_html( $text ) );
			return;
		}
		$attrs = '';
		if ( ! empty( $link['is_external'] ) ) {
			$attrs .= ' target="_blank"';
		}
		$rel = array();
		if ( ! empty( $link['is_external'] ) ) {
			$rel[] = 'noopener';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}
		if ( $rel ) {
			$attrs .= ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
		}
		printf( '<a class="%s" href="%s"%s>%s</a>', esc_attr( $class ), esc_url( $url, array( 'http', 'https', 'tel', 'mailto' ) ), $attrs, esc_html( $text ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Eyebrow + H2 + optional "View all" link row used by most sections.
	 *
	 * @param string       $eyebrow   Small uppercase label.
	 * @param string       $title     Heading.
	 * @param string       $link_text Right-hand link text.
	 * @param array|string $link      Right-hand link.
	 * @param string       $tone      Eyebrow colour: earth|green|sage.
	 */
	protected function print_head( $eyebrow, $title, $link_text = '', $link = array(), $tone = 'earth' ) {
		$url = is_array( $link ) ? ( $link['url'] ?? '' ) : (string) $link;
		echo '<div class="bs-head">';
		echo '<div>';
		if ( '' !== trim( $eyebrow ) ) {
			printf( '<div class="bs-eyebrow bs-eyebrow--%s">%s</div>', esc_attr( $tone ), esc_html( $eyebrow ) );
		}
		if ( '' !== trim( $title ) ) {
			printf( '<h2 class="bs-h2">%s</h2>', esc_html( $title ) );
		}
		echo '</div>';
		if ( '' !== trim( $link_text ) && '' !== $url ) {
			$this->print_link( $link, $link_text . ' →', 'bs-more' );
		}
		echo '</div>';
	}

	/**
	 * Allow <strong>, <em>, <a> and <br> in short copy fields.
	 *
	 * @param string $html Input.
	 * @return string
	 */
	protected static function inline_html( $html ) {
		return wp_kses(
			$html,
			array(
				'strong' => array(),
				'em'     => array(),
				'br'     => array(),
				'a'      => array(
					'href'   => array(),
					'target' => array(),
					'rel'    => array(),
				),
			)
		);
	}

	/**
	 * Replace {phone}, {address} and {hours} tokens in copy.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	protected static function tokens( $text ) {
		return strtr(
			(string) $text,
			array(
				'{phone}'   => bs_yard( 'bs_phone' ),
				'{address}' => bs_yard( 'bs_street' ) . ', ' . bs_yard( 'bs_locality' ),
				'{hours}'   => bs_hours_summary(),
			)
		);
	}
}

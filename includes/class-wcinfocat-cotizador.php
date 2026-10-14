<?php
/**
 * Clase principal del plugin WC Infocat Cart Cotizador.
 *
 * @package WC_Infocat_Cart_Cotizador
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clase principal.
 */
class WCINFOCAT_Cotizador {

	/**
	 * Instancia única (singleton).
	 *
	 * @var WCINFOCAT_Cotizador|null
	 */
	private static $instance = null;

	/**
	 * Obtiene la instancia única.
	 *
	 * @return WCINFOCAT_Cotizador
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor: registra hooks.
	 */
	private function __construct() {
		// Admin.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'maybe_show_woocommerce_notice' ) );

		// Frontend.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( $this, 'render_offcanvas' ) );

		// Botones en WooCommerce.
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'render_cotizar_button' ) );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'render_cotizar_button' ) );

		// Clase para ocultar el botón "Añadir al carrito".
		add_filter( 'body_class', array( $this, 'add_hide_add_to_cart_body_class' ) );

		// Enlace "Ajustes" en la lista de plugins.
		add_filter( 'plugin_action_links_' . plugin_basename( WCINFOCAT_PLUGIN_FILE ), array( $this, 'add_action_links' ) );
	}

	/**
	 * Agrega la página de configuración en el menú de Ajustes.
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Cotizador Infocat', 'wcinfocat-cotizador' ),
			__( 'Cotizador Infocat', 'wcinfocat-cotizador' ),
			'manage_options',
			'wcinfocat-cotizador',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Registra la opción y los campos de configuración.
	 */
	public function register_settings() {
		register_setting(
			'wcinfocat_settings_group',
			'wcinfocat_whatsapp_number',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_whatsapp_number' ),
				'default'           => '',
			)
		);

		register_setting(
			'wcinfocat_settings_group',
			'wcinfocat_header_color',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
				'default'           => '#016c79',
			)
		);

		register_setting(
			'wcinfocat_settings_group',
			'wcinfocat_accent_color',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_accent_color' ),
				'default'           => '#046876',
			)
		);

		register_setting(
			'wcinfocat_settings_group',
			'wcinfocat_hide_add_to_cart',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
				'default'           => '',
			)
		);

		add_settings_section(
			'wcinfocat_main_section',
			__( 'Configuración general', 'wcinfocat-cotizador' ),
			array( $this, 'render_section_description' ),
			'wcinfocat-cotizador'
		);

		add_settings_field(
			'wcinfocat_whatsapp_number',
			__( 'Número de WhatsApp', 'wcinfocat-cotizador' ),
			array( $this, 'render_whatsapp_field' ),
			'wcinfocat-cotizador',
			'wcinfocat_main_section'
		);

		add_settings_field(
			'wcinfocat_header_color',
			__( 'Color del encabezado', 'wcinfocat-cotizador' ),
			array( $this, 'render_header_color_field' ),
			'wcinfocat-cotizador',
			'wcinfocat_main_section'
		);

		add_settings_field(
			'wcinfocat_accent_color',
			__( 'Color de énfasis', 'wcinfocat-cotizador' ),
			array( $this, 'render_accent_color_field' ),
			'wcinfocat-cotizador',
			'wcinfocat_main_section'
		);

		add_settings_field(
			'wcinfocat_hide_add_to_cart',
			__( 'Ocultar "Añadir al carrito"', 'wcinfocat-cotizador' ),
			array( $this, 'render_hide_add_to_cart_field' ),
			'wcinfocat-cotizador',
			'wcinfocat_main_section'
		);
	}

	/**
	 * Limpia el número de WhatsApp dejando solo dígitos.
	 *
	 * @param string $value Valor ingresado.
	 * @return string
	 */
	public function sanitize_whatsapp_number( $value ) {
		return preg_replace( '/[^0-9]/', '', (string) $value );
	}

	/**
	 * Limpia el color hexadecimal (acepta #RGB o #RRGGBB).
	 *
	 * @param string $value Valor ingresado.
	 * @return string
	 */
	public function sanitize_hex_color( $value ) {
		return $this->sanitize_hex_color_value( $value, '#016c79' );
	}

	/**
	 * Limpia el color de énfasis hexadecimal (acepta #RGB o #RRGGBB).
	 *
	 * @param string $value Valor ingresado.
	 * @return string
	 */
	public function sanitize_accent_color( $value ) {
		return $this->sanitize_hex_color_value( $value, '#046876' );
	}

	/**
	 * Limpia un valor de checkbox: '1' si está marcado, '' en caso contrario.
	 *
	 * @param mixed $value Valor ingresado.
	 * @return string
	 */
	public function sanitize_checkbox( $value ) {
		return '1' === (string) $value ? '1' : '';
	}

	/**
	 * Valida un color hexadecimal y aplica un valor por defecto si es inválido.
	 *
	 * @param string $value   Valor ingresado.
	 * @param string $default Color por defecto.
	 * @return string
	 */
	private function sanitize_hex_color_value( $value, $default ) {
		$value = trim( (string) $value );
		if ( preg_match( '/^#?([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $value ) ) {
			if ( '#' !== substr( $value, 0, 1 ) ) {
				$value = '#' . $value;
			}
			return $value;
		}
		return $default;
	}

	/**
	 * Descripción de la sección.
	 */
	public function render_section_description() {
		echo '<p>' . esc_html__( 'Configure el número de WhatsApp al que se enviarán las cotizaciones. Se guarda en la base de datos.', 'wcinfocat-cotizador' ) . '</p>';
	}

	/**
	 * Campo del número de WhatsApp.
	 */
	public function render_whatsapp_field() {
		$value = get_option( 'wcinfocat_whatsapp_number', '' );
		printf(
			'<input type="text" name="wcinfocat_whatsapp_number" id="wcinfocat_whatsapp_number" value="%s" class="regular-text" placeholder="51999999999" />',
			esc_attr( $value )
		);
		echo '<p class="description">' . esc_html__( 'Ingrese el número con el código de país, sin espacios ni símbolos. Ejemplo: 51999999999', 'wcinfocat-cotizador' ) . '</p>';
	}

	/**
	 * Campo del color del encabezado.
	 */
	public function render_header_color_field() {
		$value = get_option( 'wcinfocat_header_color', '#016c79' );
		printf(
			'<input type="text" name="wcinfocat_header_color" id="wcinfocat_header_color" value="%s" class="regular-text" placeholder="#016c79" data-default-color="#016c79" />',
			esc_attr( $value )
		);
		echo '<p class="description">' . esc_html__( 'Color de fondo del encabezado del cotizador en formato hexadecimal. Ejemplo: #016c79', 'wcinfocat-cotizador' ) . '</p>';
	}

	/**
	 * Campo del color de énfasis.
	 */
	public function render_accent_color_field() {
		$value = get_option( 'wcinfocat_accent_color', '#046876' );
		printf(
			'<input type="text" name="wcinfocat_accent_color" id="wcinfocat_accent_color" value="%s" class="regular-text" placeholder="#046876" data-default-color="#046876" />',
			esc_attr( $value )
		);
		echo '<p class="description">' . esc_html__( 'Color de los textos numéricos (cantidades, precios y total) en formato hexadecimal. Ejemplo: #046876', 'wcinfocat-cotizador' ) . '</p>';
	}

	/**
	 * Campo para ocultar el botón "Añadir al carrito".
	 */
	public function render_hide_add_to_cart_field() {
		$value = get_option( 'wcinfocat_hide_add_to_cart', '' );
		printf(
			'<label><input type="checkbox" name="wcinfocat_hide_add_to_cart" value="1" %s /> %s</label>',
			checked( '1', $value, false ),
			esc_html__( 'Ocultar el botón "Añadir al carrito" en la página de producto y en el listado.', 'wcinfocat-cotizador' )
		);
		echo '<p class="description">' . esc_html__( 'Al activarlo solo se mostrará el botón "Cotizar".', 'wcinfocat-cotizador' ) . '</p>';
	}

	/**
	 * Renderiza la página de configuración.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Cotizador Infocat', 'wcinfocat-cotizador' ); ?></h1>
			<p>
				<?php esc_html_e( 'Proyecto: cotizador de WhatsApp integrado con WooCommerce.', 'wcinfocat-cotizador' ); ?><br />
				<?php esc_html_e( 'Autor: Carlos Pariona @ infocat', 'wcinfocat-cotizador' ); ?>
			</p>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'wcinfocat_settings_group' );
				do_settings_sections( 'wcinfocat-cotizador' );
				submit_button( __( 'Guardar cambios', 'wcinfocat-cotizador' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Agrega enlaces en la fila del plugin.
	 *
	 * @param array $links Enlaces existentes.
	 * @return array
	 */
	public function add_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php?page=wcinfocat-cotizador' ) ),
			esc_html__( 'Ajustes', 'wcinfocat-cotizador' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Agrega una clase al <body> cuando está activada la opción de ocultar
	 * el botón "Añadir al carrito".
	 *
	 * @param array $classes Clases actuales del body.
	 * @return array
	 */
	public function add_hide_add_to_cart_body_class( $classes ) {
		if ( '1' === get_option( 'wcinfocat_hide_add_to_cart', '' ) ) {
			$classes[] = 'wcinfocat-hide-add-to-cart';
		}
		return $classes;
	}

	/**
	 * Muestra aviso si WooCommerce no está activo.
	 */
	public function maybe_show_woocommerce_notice() {
		if ( function_exists( 'WC' ) ) {
			return;
		}
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'WC Infocat Cart Cotizador requiere que WooCommerce esté instalado y activo.', 'wcinfocat-cotizador' ) . '</p></div>';
	}

	/**
	 * Registra los assets del frontend.
	 */
	public function enqueue_assets() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$css_file = WCINFOCAT_PLUGIN_DIR . 'assets/css/wcinfocat-cotizador.css';
		$js_file  = WCINFOCAT_PLUGIN_DIR . 'assets/js/wcinfocat-cotizador.js';

		$css_version = file_exists( $css_file ) ? filemtime( $css_file ) : WCINFOCAT_VERSION;
		$js_version  = file_exists( $js_file ) ? filemtime( $js_file ) : WCINFOCAT_VERSION;

		wp_enqueue_style(
			'wcinfocat-cotizador',
			WCINFOCAT_PLUGIN_URL . 'assets/css/wcinfocat-cotizador.css',
			array(),
			$css_version
		);

		wp_enqueue_script(
			'wcinfocat-cotizador',
			WCINFOCAT_PLUGIN_URL . 'assets/js/wcinfocat-cotizador.js',
			array(),
			$js_version,
			true
		);

		wp_localize_script(
			'wcinfocat-cotizador',
			'wcinfocat_data',
			array(
				'cartIcon'          => WCINFOCAT_PLUGIN_URL . 'assets/cart.svg',
				'whatsappIcon'      => WCINFOCAT_PLUGIN_URL . 'assets/whatsapp.svg',
				'headerColor'       => get_option( 'wcinfocat_header_color', '#016c79' ),
				'whatsappNumber'    => get_option( 'wcinfocat_whatsapp_number', '' ),
				'currencySymbol'    => html_entity_decode( get_woocommerce_currency_symbol() ),
				'priceDecimals'     => wc_get_price_decimals(),
				'decimalSeparator'  => wc_get_price_decimal_separator(),
				'thousandSeparator' => wc_get_price_thousand_separator(),
				'i18n'              => array(
					'title'          => __( 'Cotizador', 'wcinfocat-cotizador' ),
					'listTitle'      => __( 'Tu lista de productos es:', 'wcinfocat-cotizador' ),
					'total'          => __( 'Total', 'wcinfocat-cotizador' ),
					'whatsappButton' => __( 'Cotizar por WhatsApp', 'wcinfocat-cotizador' ),
					'close'          => __( 'Cerrar', 'wcinfocat-cotizador' ),
					'empty'          => __( 'Tu cotización está vacía.', 'wcinfocat-cotizador' ),
					'noNumber'       => __( 'Configure el número de WhatsApp en la página de ajustes del plugin.', 'wcinfocat-cotizador' ),
					'quoteTitle'     => __( 'Cotización', 'wcinfocat-cotizador' ),
					'dateTime'       => __( 'Fecha y hora', 'wcinfocat-cotizador' ),
				),
			)
		);
	}

	/**
	 * Devuelve el producto actual del loop.
	 *
	 * @return WC_Product|null
	 */
	private function get_product() {
		global $product;
		if ( $product instanceof WC_Product ) {
			return $product;
		}
		return null;
	}

	/**
	 * Genera el HTML del botón "Cotizar".
	 *
	 * @return string
	 */
	private function render_cotizar_button_html() {
		$product = $this->get_product();
		if ( ! $product ) {
			return '';
		}

		$price = $product->get_price();
		if ( '' === $price || null === $price ) {
			$price = 0;
		}

		$image_id = $product->get_image_id();
		$image    = $image_id
			? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' )
			: wc_placeholder_img_src( 'woocommerce_thumbnail' );

		return sprintf(
			'<button type="button" class="button wcinfocat-btn-cotizar" data-product-id="%1$s" data-product-name="%2$s" data-product-price="%3$s" data-product-image="%4$s">%5$s</button>',
			esc_attr( $product->get_id() ),
			esc_attr( $product->get_name() ),
			esc_attr( (string) $price ),
			esc_url( $image ),
			esc_html__( 'Cotizar', 'wcinfocat-cotizador' )
		);
	}

	/**
	 * Imprime el botón "Cotizar".
	 */
	public function render_cotizar_button() {
		echo $this->render_cotizar_button_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ya escapado dentro del helper.
	}

	/**
	 * Renderiza el offcanvas lateral.
	 */
	public function render_offcanvas() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}
		$cart_icon    = WCINFOCAT_PLUGIN_URL . 'assets/cart.svg';
		$whatsapp_icon = WCINFOCAT_PLUGIN_URL . 'assets/whatsapp.svg';
		$header_color = get_option( 'wcinfocat_header_color', '#016c79' );
		$accent_color = get_option( 'wcinfocat_accent_color', '#046876' );
		?>
		<div class="wcinfocat-overlay" data-wcinfocat-close aria-hidden="true"></div>
		<aside class="wcinfocat-offcanvas" role="dialog" aria-modal="true" aria-hidden="true" aria-label="<?php echo esc_attr__( 'Cotizador', 'wcinfocat-cotizador' ); ?>" style="--wcinfocat-header-color: <?php echo esc_attr( $header_color ); ?>; --wcinfocat-accent-color: <?php echo esc_attr( $accent_color ); ?>;">
			<header class="wcinfocat-header">
				<img src="<?php echo esc_url( $cart_icon ); ?>" alt="" class="wcinfocat-header-icon" />
				<span class="wcinfocat-header-title"><?php esc_html_e( 'Cotizador', 'wcinfocat-cotizador' ); ?></span>
				<button type="button" class="wcinfocat-close" aria-label="<?php echo esc_attr__( 'Cerrar', 'wcinfocat-cotizador' ); ?>" data-wcinfocat-close>&times;</button>
			</header>

			<div class="wcinfocat-body">
				<h4 class="wcinfocat-body-title"><?php esc_html_e( 'Tu lista de productos es:', 'wcinfocat-cotizador' ); ?></h4>
				<ul class="wcinfocat-items"></ul>
				<p class="wcinfocat-empty"><?php esc_html_e( 'Tu cotización está vacía.', 'wcinfocat-cotizador' ); ?></p>
			</div>

			<footer class="wcinfocat-footer">
				<div class="wcinfocat-total-row">
					<span><?php esc_html_e( 'Total', 'wcinfocat-cotizador' ); ?></span>
					<span class="wcinfocat-total"></span>
				</div>
				<button type="button" class="wcinfocat-btn-whatsapp">
					<img src="<?php echo esc_url( $whatsapp_icon ); ?>" alt="" class="wcinfocat-whatsapp-icon" aria-hidden="true" />
					<span><?php esc_html_e( 'Cotizar por WhatsApp', 'wcinfocat-cotizador' ); ?></span>
				</button>
			</footer>
		</aside>
		<?php
	}
}

<?php
/**
 * Plugin Name:       WC Infocat Cart Cotizador
 * Plugin URI:        https://infocat.pe
 * Description:       Cotizador de WhatsApp integrado con WooCommerce. Agrega un botón "Cotizar" a los productos, abre un panel lateral para armar la cotización y la envía por WhatsApp con fecha y hora.
 * Version:           1.0.5
 * Author:            Carlos Pariona @ infocat
 * Author URI:        https://infocatsoluciones.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wcinfocat-cotizador
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * WC requires at least: 3.0
 */

// Evitar acceso directo.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WCINFOCAT_VERSION', '1.0.0' );
define( 'WCINFOCAT_PLUGIN_FILE', __FILE__ );
define( 'WCINFOCAT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCINFOCAT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once WCINFOCAT_PLUGIN_DIR . 'includes/class-wcinfocat-cotizador.php';

/**
 * Devuelve la instancia única del plugin.
 */
function wcinfocat_cotizador() {
	return WCINFOCAT_Cotizador::instance();
}

WCINFOCAT_Cotizador::instance();

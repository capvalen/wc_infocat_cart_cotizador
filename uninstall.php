<?php
/**
 * Desinstalación del plugin: limpia las opciones guardadas en la BD.
 *
 * @package WC_Infocat_Cart_Cotizador
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wcinfocat_whatsapp_number' );

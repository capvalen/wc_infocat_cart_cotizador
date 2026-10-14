<?php
add_action( 'wp_enqueue_scripts', 'mi_tema_hijo_cargar_estilos' );
function mi_tema_hijo_cargar_estilos() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
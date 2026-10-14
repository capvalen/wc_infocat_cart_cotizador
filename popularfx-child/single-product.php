<?php
/**
 * Plantilla personalizada para la Ficha de Producto de WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Salida de seguridad
}

get_header(); 
?>

<style>
/* Estilos generales de la ficha de producto */
.custom-product-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: Arial, sans-serif;
    color: #ffffff;
}

@media (max-width: 768px) {
    .custom-product-container { grid-template-columns: 1fr; }
}

/* Columna Izquierda: Galería e Imagen */
.custom-product-gallery {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.custom-product-gallery .woocommerce-product-gallery {
    width: 100% !important;
}

/* Columna Derecha: Detalle de Producto */
.custom-product-summary {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.custom-product-title {
    font-size: 26px;
    font-weight: bold;
    text-transform: uppercase;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
}

.custom-product-sku {
    font-size: 14px;
    color: #aaaaaa;
    margin-top: -5px;
}

.custom-product-price {
    font-size: 28px;
    font-weight: bold;
    color: #ff9900;
    margin: 10px 0;
}

.custom-product-price .woocommerce-Price-amount {
    margin-right: 5px;
}

.custom-product-description {
    font-size: 14px;
    line-height: 1.6;
    color: #dddddd;
}

/* Formulario de Cantidad y Botón */
.custom-product-summary form.cart {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 10px;
}

.custom-quantity-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
}

.custom-quantity-wrapper label {
    font-size: 14px;
    color: #cccccc;
}

.custom-qty-controls {
    display: flex;
    align-items: center;
    border: 1px solid #444;
    border-radius: 4px;
    overflow: hidden;
    background: #111;
}

.custom-qty-controls input.qty {
    width: 40px;
    text-align: center;
    border: none;
    background: transparent;
    color: #fff;
    font-size: 16px;
    -moz-appearance: textfield;
}

.custom-qty-controls input.qty::-webkit-outer-spin-button,
.custom-qty-controls input.qty::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.custom-qty-btn {
    background: #222;
    color: #fff;
    border: none;
    width: 30px;
    height: 35px;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.custom-qty-btn:hover {
    background: #333;
}

/* Enlaces de Información y Modal */
.custom-info-links {
    display: flex;
    gap: 20px;
    margin: 10px 0;
    font-size: 14px;
}

.custom-info-links a {
    color: #ff9900;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    font-weight: 500;
}

.custom-info-links a:hover {
    color: #e08a00;
}

/* Disponibilidad / Stock */
.custom-stock-status {
    font-size: 14px;
    font-weight: bold;
    margin: 5px 0;
}

.stock.in-stock { color: #00ff66; }
.stock.out-of-stock { color: #ff3333; }

.stock-bar {
    width: 100%;
    height: 6px;
    background-color: #ff9900;
    border-radius: 3px;
    margin-bottom: 15px;
}

/* Botón Añadir al Carrito Personalizado */
.custom-product-summary .single_add_to_cart_button {
    background-color: #d87d00 !important;
    color: #ffffff !important;
    border: 2px dashed #ff9900 !important;
    border-radius: 30px !important;
    padding: 12px 30px !important;
    font-size: 16px !important;
    font-weight: bold !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    transition: background 0.3s ease !important;
}

.custom-product-summary .single_add_to_cart_button:hover {
    background-color: #b56800 !important;
}

/* Bloque Red de Farmacias */
.custom-pharmacy-network {
    margin-top: 15px;
    border-top: 1px solid #333;
    padding-top: 15px;
}

.custom-pharmacy-network p {
    margin: 3px 0;
    font-size: 13px;
    color: #aaaaaa;
}

.custom-pharmacy-network strong {
    color: #ffffff;
    font-size: 14px;
}

/* Modal Estilos */
.custom-modal {
    display: block;
    position: fixed;
    z-index: 99999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.8);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.custom-modal.open {
    opacity: 1;
    visibility: visible;
}

.custom-modal-content {
    background-color: #1e1e1e;
    color: #dddddd;
    margin: 5% auto;
    padding: 25px;
    border: 1px solid #444;
    width: 80%;
    max-width: 700px;
    border-radius: 8px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
    transform: translateY(20px) scale(0.98);
    transition: transform 0.3s ease;
    scrollbar-width: thin;
    scrollbar-color: #4bb9ef #2a2a2a;
}

.custom-modal.open .custom-modal-content {
    transform: translateY(0) scale(1);
}

/* Barra de scroll del modal */
.custom-modal-content::-webkit-scrollbar {
    width: 10px;
}

.custom-modal-content::-webkit-scrollbar-track {
    background: #2a2a2a;
    border-radius: 8px;
}

.custom-modal-content::-webkit-scrollbar-thumb {
    background: #4bb9ef;
    border-radius: 8px;
    border: 2px solid #2a2a2a;
}

.custom-modal-content::-webkit-scrollbar-thumb:hover {
    background: #2aa8e0;
}

.custom-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.custom-modal-close:hover {
    color: #fff;
}

.custom-modal h2 {
    color: #4bb9ef !important;
    font-size: 20px;
    margin-top: 0;
}

.custom-modal h3 {
    color: #ffffff;
    font-size: 16px;
    margin-top: 20px;
    border-bottom: 1px solid #444;
    padding-bottom: 5px;
}

.custom-modal ul {
    padding-left: 20px;
    font-size: 13px;
    line-height: 1.5;
}

.custom-modal li {
    margin-bottom: 8px;
}
</style>

<div class="custom-product-container">
    <?php while ( have_posts() ) : the_post(); global $product; ?>

        <!-- COLUMNA 1: GALERÍA E IMÁGENES -->
        <div class="custom-product-gallery">
            <?php
            /**
             * Muestra la imagen principal y miniaturas de WooCommerce
             */
            woocommerce_show_product_images();
            ?>
        </div>

        <!-- COLUMNA 2: DETALLES DEL PRODUCTO -->
        <div class="custom-product-summary">
            
            <!-- Nombre del Producto -->
            <h1 class="custom-product-title"><?php the_title(); ?></h1>

            <!-- Referencia / SKU -->
            <?php if ( $product->get_sku() ) : ?>
                <div class="custom-product-sku">Reference: <?php echo $product->get_sku(); ?></div>
            <?php endif; ?>

            <!-- Precio -->
            <div class="custom-product-price">
                <?php echo $product->get_price_html(); ?>
            </div>

            <!-- Descripción Corta / Descripción -->
            <div class="custom-product-description">
                <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ? $post->post_excerpt : get_the_content() ); ?>
            </div>

            <!-- Formulario y Botón de Añadir al Carrito -->
            <?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
                <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                    
                    <div class="custom-quantity-wrapper">
                        <label for="quantity_product">Cantidad:</label>
                        <div class="custom-qty-controls">
                            <input type="number" id="quantity_product" class="qty" step="1" min="1" <?php $max_qty = $product->get_max_purchase_quantity(); if ( $max_qty > 0 ) { echo 'max="' . esc_attr( $max_qty ) . '"'; } ?> name="quantity" value="1" title="Qty" size="4" placeholder="" inputmode="numeric" />
                            <button type="button" class="custom-qty-btn minus">-</button>
                            <button type="button" class="custom-qty-btn plus">+</button>
                        </div>
                    </div>

                    <!-- Enlaces Informativos (Horarios y Costos) -->
                    <div class="custom-info-links">
                        <a id="openHorariosModal">
                            <span>&#9200;</span> Horarios de atención y entrega
                        </a>
                    </div>

                    <!-- Estado del Stock de WooCommerce -->
                    <div class="custom-stock-status">
                        <?php echo wc_get_stock_html( $product ); ?>
                    </div>
                    <div class="stock-bar"></div>

                    <!-- Botón por defecto de WooCommerce -->
                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt">
                        <span>&#128722;</span> Añadir al carrito
                    </button>

                </form>
            <?php else : ?>
                <div class="custom-stock-status">
                    <?php echo wc_get_stock_html( $product ); ?>
                </div>
            <?php endif; ?>

            <!-- Disponibilidad en red de farmacias -->
            <div class="custom-pharmacy-network">
                <strong>&#128205; Disponibilidad en red de farmacias</strong>
            </div>

        </div>

    <?php endwhile; ?>
</div>

<!-- MODAL PARA HORARIOS DE ATENCIÓN Y ENTREGA -->
<div id="horariosModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="custom-modal-close">&times;</span>
        <h2>Horarios de atención y entrega</h2>
        <ul>
            <li>Nuestro horario de atención es de lunes a sábado de 9 am a 7 pm. El pedido mínimo es de S/45. Escríbenos por whatsapp al 932 479 749 para cualquier consulta que tengas.</li>
            <li>La entrega de pedidos y el costo de envío depende del distrito de entrega.</li>
            <li>No se realizan entregas los días domingo.</li>
            <li>Todas las coordinaciones se realizan con el cliente previo al delivery y confirmación de la orden. Es posible realizar cambios del lugar del delivery máximo una hora luego de la confirmación del pedido.</li>
            <li>Para el caso de compra online con recojo en alguna de nuestras sedes, el pedido se puede recoger en un plazo máximo de 30 días. Posterior a ello, el pedido se desechará sin opción a devolución ni reembolso.</li>
            <li>La información del lugar de envío es de exclusiva responsabilidad del cliente. Los plazos elegidos para el delivery se cuentan desde que la empresa ha validado la solicitud de compra y el medio de pago utilizado.</li>
            <li>El rango de entrega es de 12:00 pm a 09:00 pm.</li>
            <li>El valor del pedido pagado por el cliente incluye un item por concepto de delivery de los productos.</li>
            <li>La empresa podrá entregar la información de delivery otorgada por el usuario a terceros con el objeto de llevar a cabo la distribución de los productos.</li>
            <li>El servicio de delivery de los productos es prestado por la empresa, pero en algunos casos excepcionales podrá ser prestado por terceros, a solicitud del cliente, a los cuales la empresa les entrega el nombre, DNI teléfono y dirección del usuario. Estos terceros distribuidores están sujetos a la obligación legal de reserva respecto a los datos personales de los usuarios.</li>
            <li>El cliente debe ingresar una dirección válida. Si la dirección es inválida, la empresa podrá devolver el monto pagado por el producto pero no se hará devolución del monto de delivery.</li>
            <li>El horario de entrega generalmente es de 12:00 pm a 08:00 pm. El cliente debe asegurar que haya una persona que pueda recibir el producto en caso no se encuentre en su domicilio.</li>
            <li>Cualquier persona mayor de edad en la dirección informada puede recibir el producto. Debido a que es necesario que la boleta sea firmada al tener la conformidad de la entrega.</li>
            <li>Si la dirección ingresada se encuentra en un edificio o recinto en el cual no se permite el libre acceso, el producto puede ser entregado al portero o encargado del edificio o recinto, previa autorización del cliente.</li>
            <li>Para recojo en tienda se esperará máximo 15 días, luego se anulará la venta.</li>
        </ul>

        <h3>Métodos de pago</h3>
        <ul>
            <li>Pago en línea con tarjeta de crédito/débito</li>
            <li>Transferencia bancaria a Banco de Crédito del Perú.</li>
            <li>Transferencia interbancaria se validan el siguiente día por lo que la entrega de producto se dará al siguiente día de confirmado el depósito.</li>
            <li>Yape</li>
            <li>Plin</li>
            <li>No hay pago contra entrega.</li>
        </ul>
        <p style="font-size: 12px; color: #aaa;">Para pagos con yape, transferencias bancaria o interbancaria se considerará como pedido recibido en el momento del envío de la constancia de pago (voucher) a través de un correo electrónico a pedidos@pharmivet.pe. El pedido solo se entrega cuando el pago se haya validado como ingresado a nuestra cuenta bancaria.</p>

        <h3>Devoluciones</h3>
        <ul>
            <li>Para los pedidos online, el cliente podrá solicitar la restitución del precio pagado, en los casos en los que fuera procedente, el usuario deberá proceder con la devolución del producto en perfecto estado y la entrega de la boleta de compra dentro de las 24 horas de realizada la compra. En este caso el reembolso del importe de la compra por parte de la empresa al cliente, será la empresa la que tendrá potestad para decidir el método de devolución (metálico, tarjeta de crédito, vale de compra, u otro). En caso de devolución total del producto, el cliente se encargará del traslado del producto al establecimiento acordado entre la empresa y el cliente. No se devuelven los productos que necesitan estar en cadena fría.</li>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lógica para botones de incremento/decremento de cantidad
    const qtyInput = document.getElementById('quantity_product');
    const plusBtn = document.querySelector('.custom-qty-btn.plus');
    const minusBtn = document.querySelector('.custom-qty-btn.minus');

    if (qtyInput && plusBtn && minusBtn) {
        plusBtn.addEventListener('click', function() {
            let maxAttr = qtyInput.getAttribute('max');
            let max = (maxAttr !== null && parseInt(maxAttr) > 0) ? parseInt(maxAttr) : 999;
            let val = parseInt(qtyInput.value) || 1;
            if (val < max) {
                qtyInput.value = val + 1;
            }
        });

        minusBtn.addEventListener('click', function() {
            let min = parseInt(qtyInput.getAttribute('min')) || 1;
            let val = parseInt(qtyInput.value) || 1;
            if (val > min) {
                qtyInput.value = val - 1;
            }
        });
    }

    // Lógica del Modal
    const modal = document.getElementById('horariosModal');
    const openBtn = document.getElementById('openHorariosModal');
    const closeBtn = document.querySelector('.custom-modal-close');

    if (openBtn && modal) {
        openBtn.onclick = function() {
            modal.classList.add('open');
        }
    }

    if (closeBtn && modal) {
        closeBtn.onclick = function() {
            modal.classList.remove('open');
        }
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.classList.remove('open');
        }
    }
});
</script>

<?php 
get_footer(); 
?>
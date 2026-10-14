/**
 * WC Infocat Cart Cotizador - JavaScript puro (sin jQuery).
 *
 * @package WC_Infocat_Cart_Cotizador
 */
(function () {
	'use strict';

	var cfg = window.wcinfocat_data || {};
	var i18n = cfg.i18n || {};

	/**
	 * Estado del cotizador: items[productId] = { id, name, price, image, qty }.
	 */
	var items = {};

	// ---------------------------------------------------------------------
	// Utilidades
	// ---------------------------------------------------------------------

	/**
	 * Formatea un precio usando la configuración de WooCommerce.
	 *
	 * @param {number} value Valor numérico.
	 * @return {string}
	 */
	function formatPrice(value) {
		var n = parseFloat(value);
		if (isNaN(n)) {
			n = 0;
		}
		var decimals = parseInt(cfg.priceDecimals || '0', 10);
		if (isNaN(decimals)) {
			decimals = 0;
		}
		var sep = typeof cfg.decimalSeparator === 'string' ? cfg.decimalSeparator : '.';
		var thou = typeof cfg.thousandSeparator === 'string' ? cfg.thousandSeparator : ',';

		var fixed = n.toFixed(decimals);
		var parts = fixed.split('.');
		var intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thou);

		var result = intPart;
		if (decimals > 0) {
			result += sep + (parts[1] || '');
		}

		var symbol = String(cfg.currencySymbol || '').trim();
		if (symbol) {
			return symbol + ' ' + result;
		}
		return result;
	}

	/**
	 * Devuelve la fecha y hora actuales formateadas: dd/mm/aaaa hh:mm.
	 *
	 * @return {string}
	 */
	function nowFormatted() {
		var d = new Date();
		var pad = function (n) {
			return (n < 10 ? '0' : '') + n;
		};
		return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() +
			' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
	}

	/**
	 * Calcula el total sumado de todos los ítems.
	 *
	 * @return {number}
	 */
	function total() {
		var sum = 0;
		Object.keys(items).forEach(function (id) {
			sum += items[id].price * items[id].qty;
		});
		return sum;
	}

	// ---------------------------------------------------------------------
	// Render
	// ---------------------------------------------------------------------

	function render() {
		var list = document.querySelector('.wcinfocat-items');
		var empty = document.querySelector('.wcinfocat-empty');
		if (!list) {
			return;
		}

		var html = '';
		Object.keys(items).forEach(function (id) {
			var it = items[id];
			var lineTotal = formatPrice(it.price * it.qty);

			html +=
				'<li class="wcinfocat-item" data-item-id="' + it.id + '">' +
				'<img class="wcinfocat-item-img" src="' + it.image + '" alt="' + escapeHtml(it.name) + '" />' +
				'<div class="wcinfocat-item-info">' +
				'<div class="wcinfocat-item-name">' + escapeHtml(it.name) + '</div>' +
				'<div class="wcinfocat-item-controls">' +
				'<button type="button" class="wcinfocat-qty-btn" data-action="decrease" data-id="' + it.id + '" aria-label="-">&#8722;</button>' +
				'<span class="wcinfocat-qty">' + it.qty + '</span>' +
				'<button type="button" class="wcinfocat-qty-btn" data-action="increase" data-id="' + it.id + '" aria-label="+">+</button>' +
				'<span class="wcinfocat-item-price">' + lineTotal + '</span>' +
				'</div>' +
				'</div>' +
				'</li>';
		});

		list.innerHTML = html;

		if (empty) {
			empty.style.display = Object.keys(items).length === 0 ? 'block' : 'none';
		}

		var title = document.querySelector('.wcinfocat-body-title');
		if (title) {
			title.style.display = Object.keys(items).length === 0 ? 'none' : 'block';
		}

		updateTotal();
	}

	function updateTotal() {
		var totalEl = document.querySelector('.wcinfocat-total');
		if (totalEl) {
			totalEl.textContent = formatPrice(total());
		}
	}

	/**
	 * Escapa HTML básico para textos de producto.
	 *
	 * @param {string} str Texto.
	 * @return {string}
	 */
	function escapeHtml(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	// ---------------------------------------------------------------------
	// Lógica del cotizador
	// ---------------------------------------------------------------------

	function addProduct(data) {
		var id = data.id;
		if (items[id]) {
			items[id].qty += 1;
		} else {
			items[id] = {
				id: id,
				name: data.name,
				price: parseFloat(data.price) || 0,
				image: data.image || '',
				qty: 1
			};
		}
		render();
		open();
	}

	function changeQty(id, delta) {
		if (!items[id]) {
			return;
		}
		var newQty = items[id].qty + delta;
		if (newQty <= 0) {
			// Al bajar de 1 se elimina el producto del cotizador.
			delete items[id];
		} else {
			items[id].qty = newQty;
		}
		render();
	}

	// ---------------------------------------------------------------------
	// Offcanvas
	// ---------------------------------------------------------------------

	function open() {
		document.body.classList.add('wcinfocat-open');
		var offcanvas = document.querySelector('.wcinfocat-offcanvas');
		if (offcanvas) {
			offcanvas.setAttribute('aria-hidden', 'false');
		}
	}

	function close() {
		document.body.classList.remove('wcinfocat-open');
		var offcanvas = document.querySelector('.wcinfocat-offcanvas');
		if (offcanvas) {
			offcanvas.setAttribute('aria-hidden', 'true');
		}
	}

	// ---------------------------------------------------------------------
	// WhatsApp
	// ---------------------------------------------------------------------

	function buildMessage() {
		var i18nQuoteTitle = i18n.quoteTitle || 'Cotización';
		var i18nTotal = i18n.total || 'Total';
		var i18nDateTime = i18n.dateTime || 'Fecha y hora';

		var lines = [];
		lines.push('*' + i18nQuoteTitle + '*');
		lines.push('--------------------------');

		Object.keys(items).forEach(function (id) {
			var it = items[id];
			lines.push('• ' + it.name + ' (x' + it.qty + ') = ' + formatPrice(it.price * it.qty));
		});

		lines.push('--------------------------');
		lines.push('*' + i18nTotal + ':* ' + formatPrice(total()));
		lines.push(i18nDateTime + ': ' + nowFormatted());

		return lines.join('\n');
	}

	function sendWhatsApp() {
		var number = (cfg.whatsappNumber || '').replace(/[^0-9]/g, '');
		if (!number) {
			window.alert(i18n.noNumber || 'Configure el número de WhatsApp en la página de ajustes del plugin.');
			return;
		}
		var message = buildMessage();
		var url = 'https://wa.me/' + number + '?text=' + encodeURIComponent(message);
		window.open(url, '_blank', 'noopener');
	}

	// ---------------------------------------------------------------------
	// Delegación de eventos
	// ---------------------------------------------------------------------

	document.addEventListener('click', function (e) {
		// Botón "Cotizar" del producto.
		var cotizarBtn = e.target.closest ? e.target.closest('.wcinfocat-btn-cotizar') : null;
		if (cotizarBtn) {
			e.preventDefault();
			addProduct({
				id: cotizarBtn.getAttribute('data-product-id'),
				name: cotizarBtn.getAttribute('data-product-name') || '',
				price: cotizarBtn.getAttribute('data-product-price') || '0',
				image: cotizarBtn.getAttribute('data-product-image') || ''
			});
			return;
		}

		// Botones de cantidad (aumentar/disminuir).
		var qtyBtn = e.target.closest ? e.target.closest('.wcinfocat-qty-btn') : null;
		if (qtyBtn) {
			e.preventDefault();
			var id = qtyBtn.getAttribute('data-id');
			var action = qtyBtn.getAttribute('data-action');
			changeQty(id, action === 'increase' ? 1 : -1);
			return;
		}

		// Cerrar (overlay, botón de cierre o cualquier elemento con data-wcinfocat-close).
		if (e.target.hasAttribute && e.target.hasAttribute('data-wcinfocat-close')) {
			e.preventDefault();
			close();
			return;
		}

		// Botón "Cotizar por WhatsApp".
		if (e.target.closest && e.target.closest('.wcinfocat-btn-whatsapp')) {
			e.preventDefault();
			sendWhatsApp();
			return;
		}
	});

	// Cerrar con la tecla Escape.
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' || e.key === 'Esc') {
			close();
		}
	});

	// Inicializar total al cargar.
	document.addEventListener('DOMContentLoaded', function () {
		updateTotal();
	});
})();

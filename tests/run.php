<?php
/**
 * Pruebas del envío, el nonce de la bolsa y el enlace de empacar.
 * php tests/run.php
 */

$failed = 0;

function nj_test($name, $ok) {
    global $failed;
    if ($ok) {
        echo "ok  $name\n";
        return;
    }
    $failed++;
    echo "FAIL $name\n";
}

require dirname(__DIR__) . '/inc/wp-stubs.php';

function wp_verify_nonce($nonce, $action) {
    return $nonce === 'good' && $action === 'nj-bag';
}

function wp_salt($scheme) {
    return 'test-salt-' . $scheme;
}

require dirname(__DIR__) . '/inc/bolsa.php';
require dirname(__DIR__) . '/inc/paginas.php';
require dirname(__DIR__) . '/inc/pedido.php';
require dirname(__DIR__) . '/inc/avisos.php';

nj_test('envio gratis desde 250000', nj_shipping_is_free(250000, 250000) === true);
nj_test('envio gratis por encima del tope', nj_shipping_is_free(250001, 250000) === true);
nj_test('envio con costo bajo el tope', nj_shipping_is_free(249999, 250000) === false);
nj_test('el tope no usa el texto con puntos', nj_shipping_is_free(250000, 250000) === true && nj_shipping_is_free(250, 250000) === false);

nj_test('nonce de la bolsa acepta el valor bueno', nj_bag_nonce_valid('good') === true);
nj_test('nonce de la bolsa rechaza otro valor', nj_bag_nonce_valid('bad') === false);
nj_test('nonce de la bolsa rechaza vacio', nj_bag_nonce_valid('') === false);
nj_test('la accion del nonce es nj-bag', nj_bag_nonce_action() === 'nj-bag');

$token = nj_empacar_token(234);
nj_test('el enlace de empacar acepta su token', nj_empacar_token_matches(234, $token) === true);
nj_test('el enlace de empacar rechaza otro pedido', nj_empacar_token_matches(235, $token) === false);
nj_test('el enlace de empacar rechaza un token corto', nj_empacar_token_matches(234, 'abc') === false);
nj_test('el enlace de empacar rechaza pedido cero', nj_empacar_token_matches(0, $token) === false);

require dirname(__DIR__) . '/inc/colecciones.php';
$nj_banda_base = nj_coleccion_banda_estilo(array());
nj_test('la banda conserva el ritmo y el azul', $nj_banda_base === '--banda-tiempo:28s;background:#17324d;color:#ffffff');
$nj_banda_color = nj_coleccion_banda_estilo(array(
    'vel' => 10,
    'fondo' => 'degradado',
    'color' => '#112233',
    'color_b' => '#abc',
    'rumbo' => 'vertical',
    'tinta' => '#000',
));
nj_test('la banda acepta velocidad y degradado', $nj_banda_color === '--banda-tiempo:6s;background:linear-gradient(180deg,#112233,#aabbcc);color:#000000');
nj_test('la banda ignora un color invalido', strpos(nj_coleccion_banda_estilo(array('color' => 'red;background:url(x)')), 'background:#17324d') !== false);

require dirname(__DIR__) . '/inc/shop.php';
require dirname(__DIR__) . '/inc/busca.php';
require dirname(__DIR__) . '/inc/seo.php';

nj_test('leeggins encuentra un pantalon', nj_busca_coincide('Pantalón de línea', 'leeggins') === true);
nj_test('leeggins no mezcla un vestido', nj_busca_coincide('Vestido unicornio', 'leeggins') === false);
nj_test('una letra no filtra la tienda', nj_busca_coincide('Falda', 'fa') === true);
nj_test('el canonico de una visita suelta es el inicio', nj_seo_url() === '/');
$nj_robots = nj_seo_robots(array());
nj_test('la vista publica permite la foto grande', isset($nj_robots['max-image-preview']) && $nj_robots['max-image-preview'] === 'large');

require dirname(__DIR__) . '/inc/pagos.php';
nj_test('los puntos no reemplazan una llave guardada', nj_pagos_secreto_vacio(nj_pagos_secreto_mascara()) === true);
nj_test('una llave nueva si se guarda', nj_pagos_secreto_vacio('prv_prod_abc') === false);

require dirname(__DIR__) . '/inc/pedidos.php';
list($nj_ini, $nj_fin, $nj_vista, $nj_fecha) = nj_pedidos_rango('mes', '2026-11');
nj_test('el mes de pedidos cubre noviembre', $nj_ini === '2026-11-01' && $nj_fin === '2026-11-30' && $nj_vista === 'mes' && $nj_fecha === '2026-11');
list($nj_ini, $nj_fin, $nj_vista) = nj_pedidos_rango('ano', '2026');
nj_test('el ano de pedidos cubre el calendario', $nj_ini === '2026-01-01' && $nj_fin === '2026-12-31' && $nj_vista === 'ano');
nj_test('elaborado empacado enviado y entregado tienen nombre', nj_pedidos_estado_nombre('elaborado') === 'Elaborado' && nj_pedidos_estado_nombre('empacando') === 'Empacado' && nj_pedidos_estado_nombre('completed') === 'Enviado' && nj_pedidos_estado_nombre('entregado') === 'Entregado');
nj_test('un pedido pagado esta por elaborar', nj_pedidos_etapa('processing', true) === 'por-elaborar' && nj_pedidos_etapa('empacando', false) === 'empacado' && nj_pedidos_etapa('completed', false) === 'enviado' && nj_pedidos_etapa('entregado', false) === 'entregado');
$nj_pasos = nj_pedidos_pasos('empacado');
nj_test('empacado deja listo el check de enviado', $nj_pasos[0]['state'] === 'done' && $nj_pasos[1]['state'] === 'done' && $nj_pasos[2]['state'] === 'now' && $nj_pasos[2]['action'] === 'completed' && $nj_pasos[3]['state'] === 'wait');
$nj_fin = nj_pedidos_pasos('entregado');
$nj_abiertos = 0;
foreach ($nj_fin as $nj_paso) {
    if ($nj_paso['state'] === 'now') {
        $nj_abiertos++;
    }
}
nj_test('entregado cierra los cuatro checks', $nj_abiertos === 0 && $nj_fin[0]['state'] === 'done' && $nj_fin[3]['state'] === 'done');
nj_test('la guia guarda letras y numeros', nj_pedidos_guia_limpia('  ABC-123 45 ') === 'ABC-123 45');
nj_test('la guia vacia no se guarda', nj_pedidos_guia_limpia('***') === '' && nj_pedidos_guia_limpia('AB') === '');
nj_test('la prenda del pedido lee talla y color', nj_pedidos_dato(array(array('pa_talla', '6'), array('pa_color', 'Rosado')), 'talla') === '6' && nj_pedidos_dato(array(array('pa_talla', '6'), array('pa_color', 'Rosado')), 'color') === 'Rosado');
nj_test('la cedula guarda solo numeros', nj_sanitize_cedula('1.023.456.789') === '1023456789' && nj_sanitize_cedula('cc 12a') === '12');
nj_test('la cedula corta no sirve para enviar', is_wp_error(nj_validate_cedula('12345')) && nj_validate_cedula('1023456789') === null);
nj_test('el aviso de envio nombra la transportadora y la guia', nj_envio_aviso('Ana', '2501', 'Servientrega', '9921847751') === 'Hola Ana, Tu pedido 2501 ya salió. Lo enviamos con Servientrega. El número de guía es 9921847751.');

if ($failed > 0) {
    echo "$failed fallaron\n";
    exit(1);
}
echo "34 pruebas ok\n";

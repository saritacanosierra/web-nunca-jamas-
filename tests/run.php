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

if ($failed > 0) {
    echo "$failed fallaron\n";
    exit(1);
}
echo "12 pruebas ok\n";

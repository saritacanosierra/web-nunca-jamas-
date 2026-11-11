<?php
/**
 * Tablero de despacho dentro de Mi tienda.
 */

if (!function_exists('nj_pedidos_admin_panel')) {
    function nj_pedidos_admin_panel() {
        $vista = isset($_GET['nj_vista']) ? sanitize_key(wp_unslash($_GET['nj_vista'])) : 'cola';
        $fecha = isset($_GET['nj_fecha']) ? sanitize_text_field(wp_unslash($_GET['nj_fecha'])) : '';
        $filtro = isset($_GET['nj_filtro']) ? sanitize_key(wp_unslash($_GET['nj_filtro'])) : 'todos';
        $consulta = nj_pedidos_consulta($vista, $fecha, $filtro);
        $rows = $consulta['rows'];
        $cola = nj_pedidos_consulta('cola', '', 'todos');
        $cuentas = array('por-elaborar' => 0, 'elaborado' => 0, 'empacado' => 0, 'enviado' => 0);
        foreach ($cola['rows'] as $row) {
            if (isset($cuentas[$row['etapa']])) {
                $cuentas[$row['etapa']]++;
            }
        }
        $tablero = $consulta['vista'] === 'cola' && $consulta['estado'] === 'todos';
        $columnas = array(
            'por-elaborar' => array('titulo' => 'Por elaborar', 'nota' => 'Todavía no se hace', 'rows' => array()),
            'elaborado' => array('titulo' => 'Elaborado', 'nota' => 'Ya está hecha', 'rows' => array()),
            'empacado' => array('titulo' => 'Empacado', 'nota' => 'Ya está en el paquete', 'rows' => array()),
            'enviado' => array('titulo' => 'Enviado', 'nota' => 'Falta confirmar la entrega', 'rows' => array()),
        );
        $grupos = array();
        foreach ($rows as $row) {
            $clave = $row['etapa'];
            if ($tablero && isset($columnas[$clave])) {
                $columnas[$clave]['rows'][] = $row;
                continue;
            }
            if ($consulta['vista'] === 'ano') {
                $grupo = $row['month'] !== '' ? $row['month'] : 'sin-fecha';
            } elseif ($consulta['vista'] === 'mes') {
                $grupo = $row['day'] !== '' ? $row['day'] : 'sin-fecha';
            } else {
                $grupo = $row['day'] !== '' ? $row['day'] : $consulta['fecha'];
            }
            if (!isset($grupos[$grupo])) {
                $grupos[$grupo] = array();
            }
            $grupos[$grupo][] = $row;
        }
        $pasos = array(
            'todos' => 'En curso',
            'por-elaborar' => 'Por elaborar',
            'elaborado' => 'Elaborado',
            'empacado' => 'Empacado',
            'enviado' => 'Enviado',
        );
        ?>
        <h2>Pedidos</h2>
        <p>Marca el check del paso que ya hiciste. Al marcar Enviado escribes la guía. Cuando marcas Entregado, el pedido pasa al historial.</p>
        <?php if (!function_exists('wc_get_orders')) : ?>
          <p class="nj-prenda-note">La tienda todavía no puede leer los pedidos.</p>
        <?php else : ?>
          <div class="nj-pasos-filtro">
            <?php foreach ($pasos as $clave => $titulo) :
                $activo = $consulta['vista'] === 'cola' && $consulta['estado'] === $clave;
                $url = add_query_arg(array(
                    'page' => 'nj-tienda',
                    'nj_vista' => 'cola',
                    'nj_filtro' => $clave,
                ), admin_url('admin.php')) . '#pedidos';
                $cantidad = $clave === 'todos' ? array_sum($cuentas) : (isset($cuentas[$clave]) ? $cuentas[$clave] : 0);
                ?>
              <a class="nj-paso-chip<?php echo $activo ? ' is-active' : ''; ?>" href="<?php echo esc_url($url); ?>">
                <strong><?php echo (int) $cantidad; ?></strong>
                <span><?php echo esc_html($titulo); ?></span>
              </a>
            <?php endforeach; ?>
            <a class="nj-paso-chip<?php echo $consulta['vista'] !== 'cola' ? ' is-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=nj-tienda&nj_vista=dia&nj_filtro=entregado#pedidos')); ?>">Historial</a>
          </div>
          <?php if ($consulta['vista'] !== 'cola') : ?>
            <form class="nj-pedidos-filtro" method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
              <input type="hidden" name="page" value="nj-tienda">
              <label>Período
                <select name="nj_vista" data-pedidos-vista>
                  <option value="dia" <?php selected($consulta['vista'], 'dia'); ?>>Día</option>
                  <option value="mes" <?php selected($consulta['vista'], 'mes'); ?>>Mes</option>
                  <option value="ano" <?php selected($consulta['vista'], 'ano'); ?>>Año</option>
                </select>
              </label>
              <label data-pedidos-dia <?php echo $consulta['vista'] === 'dia' ? '' : 'hidden'; ?>>Fecha
                <input type="date" name="nj_fecha" value="<?php echo esc_attr($consulta['vista'] === 'dia' ? $consulta['fecha'] : $consulta['inicio']); ?>" <?php echo $consulta['vista'] === 'dia' ? '' : 'disabled'; ?>>
              </label>
              <label data-pedidos-mes <?php echo $consulta['vista'] === 'mes' ? '' : 'hidden'; ?>>Mes
                <input type="month" name="nj_fecha" value="<?php echo esc_attr($consulta['vista'] === 'mes' ? $consulta['fecha'] : substr($consulta['inicio'], 0, 7)); ?>" <?php echo $consulta['vista'] === 'mes' ? '' : 'disabled'; ?>>
              </label>
              <label data-pedidos-ano <?php echo $consulta['vista'] === 'ano' ? '' : 'hidden'; ?>>Año
                <input type="number" name="nj_fecha" min="2020" max="2100" value="<?php echo esc_attr($consulta['vista'] === 'ano' ? $consulta['fecha'] : substr($consulta['inicio'], 0, 4)); ?>" <?php echo $consulta['vista'] === 'ano' ? '' : 'disabled'; ?>>
              </label>
              <label>Estado
                <select name="nj_filtro">
                  <option value="todos" <?php selected($consulta['estado'], 'todos'); ?>>Todos</option>
                  <option value="elaborado" <?php selected($consulta['estado'], 'elaborado'); ?>>Elaborado</option>
                  <option value="empacado" <?php selected($consulta['estado'], 'empacado'); ?>>Empacado</option>
                  <option value="enviado" <?php selected($consulta['estado'], 'enviado'); ?>>Enviado</option>
                  <option value="entregado" <?php selected($consulta['estado'], 'entregado'); ?>>Entregado</option>
                </select>
              </label>
              <button type="submit" class="button button-primary">Ver pedidos</button>
            </form>
          <?php endif; ?>
          <?php if ($tablero) : ?>
            <div class="nj-tablero">
              <?php foreach ($columnas as $clave => $columna) : ?>
                <section class="nj-columna nj-columna--<?php echo esc_attr($clave); ?>">
                  <header>
                    <h3><?php echo esc_html($columna['titulo']); ?></h3>
                    <p><?php echo esc_html($columna['nota']); ?></p>
                  </header>
                  <?php if (!$columna['rows']) : ?>
                    <p class="nj-columna__vacio">Nada en este paso.</p>
                  <?php endif; ?>
                  <?php foreach ($columna['rows'] as $row) { nj_pedidos_tarjeta($row, $consulta); } ?>
                </section>
              <?php endforeach; ?>
            </div>
          <?php elseif (!$rows) : ?>
            <p class="nj-prenda-note">En este período no hay pedidos.</p>
          <?php else : ?>
            <?php foreach ($grupos as $clave => $grupo) : ?>
              <?php if ($consulta['vista'] !== 'dia') : ?>
                <h3 class="nj-pedidos-grupo"><?php echo esc_html(nj_pedidos_grupo_titulo($clave, $consulta['vista'])); ?></h3>
              <?php endif; ?>
              <div class="nj-pedidos">
                <?php foreach ($grupo as $row) { nj_pedidos_tarjeta($row, $consulta); } ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
          <script>
            document.addEventListener("change", function (event) {
              var select = event.target.closest("[data-pedidos-vista]");
              if (!select || !select.form) {
                return;
              }
              ["dia", "mes", "ano"].forEach(function (key) {
                var field = select.form.querySelector("[data-pedidos-" + key + "]");
                if (!field) {
                  return;
                }
                var on = select.value === key;
                field.hidden = !on;
                var input = field.querySelector("input");
                if (input) {
                  input.disabled = !on;
                }
              });
            });
          </script>
        <?php endif; ?>
        <?php
    }
}

if (!function_exists('nj_pedidos_tarjeta')) {
    function nj_pedidos_tarjeta($row, $consulta) {
        $puede = current_user_can('manage_woocommerce');
        $pasos = $row['etapa'] === 'otro' ? array() : nj_pedidos_pasos($row['etapa'], !empty($row['personalizado']));
        $listo = '';
        if (!empty($row['listo']) && function_exists('nj_personalizado_date_label')) {
            $listo = nj_personalizado_date_label($row['listo']);
        }
        $carriers = nj_pedidos_transportadoras();
        ?>
        <article class="nj-pedido nj-pedido--<?php echo esc_attr($row['etapa']); ?>">
          <header>
            <strong>Pedido <?php echo esc_html($row['number']); ?></strong>
            <span><?php echo esc_html($row['when']); ?></span>
            <em><?php echo esc_html($row['etapa_nombre'] !== '' ? $row['etapa_nombre'] : $row['status_name']); ?></em>
          </header>
          <p class="nj-pedido__cliente"><?php echo esc_html($row['name'] !== '' ? $row['name'] : 'Sin nombre'); ?></p>
          <p class="nj-pedido__contacto">
            <?php if ($row['email'] !== '') : ?><a href="mailto:<?php echo esc_attr($row['email']); ?>"><?php echo esc_html($row['email']); ?></a><?php endif; ?>
            <?php if ($row['phone'] !== '') : ?><a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $row['phone'])); ?>"><?php echo esc_html($row['phone']); ?></a><?php endif; ?>
            <?php if ($row['whatsapp'] !== '') : ?><span>WhatsApp <?php echo esc_html($row['whatsapp']); ?></span><?php endif; ?>
          </p>
          <p class="nj-pedido__cedula<?php echo $row['cedula'] === '' ? ' is-missing' : ''; ?>"><?php echo esc_html($row['cedula'] !== '' ? 'Cédula ' . $row['cedula'] : 'Falta la cédula'); ?></p>
          <?php if ($row['address'] !== '') : ?>
            <p class="nj-pedido__dir"><?php echo esc_html($row['address']); ?></p>
          <?php endif; ?>
          <?php if ($row['items']) : ?>
            <ul class="nj-pedido__prendas">
              <?php foreach ($row['items'] as $item) :
                  if (!is_array($item)) {
                      continue;
                  }
                  ?>
                <li class="nj-prenda">
                  <?php if ($item['image'] !== '') : ?>
                    <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['name']); ?>" width="72" height="90">
                  <?php else : ?>
                    <span class="nj-prenda__foto" aria-hidden="true"></span>
                  <?php endif; ?>
                  <div>
                    <strong><?php echo esc_html($item['name'] !== '' ? $item['name'] : 'Prenda'); ?></strong>
                    <span class="nj-prenda__codigo"><?php echo esc_html($item['sku'] !== '' ? 'Código ' . $item['sku'] : 'Sin código'); ?></span>
                    <span class="nj-prenda__specs">
                      <span class="is-talla"><?php echo esc_html($item['talla'] !== '' ? 'Talla ' . $item['talla'] : 'Sin talla'); ?></span>
                      <?php if ($item['color'] !== '') : ?><span class="is-color">Color <?php echo esc_html($item['color']); ?></span><?php endif; ?>
                      <span class="is-qty">× <?php echo (int) $item['qty']; ?></span>
                    </span>
                    <?php if ($item['bordado'] !== '') : ?>
                      <em>Bordar <?php echo esc_html($item['bordado']); ?></em>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <?php if ($listo !== '' && $row['etapa'] === 'por-elaborar') : ?>
            <p class="nj-pedido__listo">Queda listo el <?php echo esc_html($listo); ?>.</p>
          <?php endif; ?>
          <p class="nj-pedido__total"><?php echo wp_kses_post($row['total']); ?></p>
          <?php if (in_array($row['etapa'], array('enviado', 'entregado'), true) && $row['guia'] !== '') : ?>
            <p class="nj-pedido__guia"><?php echo esc_html($row['transportadora_nombre'] !== '' ? $row['transportadora_nombre'] . ' · ' : ''); ?>Guía <?php echo esc_html($row['guia']); ?></p>
          <?php endif; ?>
          <?php if ($pasos) :
              $siguiente = null;
              foreach ($pasos as $paso) {
                  if ($paso['state'] === 'now') {
                      $siguiente = $paso;
                  }
              }
              ?>
            <?php if ($puede && $siguiente) : ?>
              <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php nj_pedidos_form_base($row, $consulta); ?>
                <?php if ($siguiente['action'] === 'completed') : ?>
                  <div class="nj-pedido__envio">
                    <label>Transportadora
                      <select name="nj_transportadora">
                        <?php foreach ($carriers as $clave => $nombre) : ?>
                          <option value="<?php echo esc_attr($clave); ?>" <?php selected($row['transportadora'] !== '' ? $row['transportadora'] : 'servientrega', $clave); ?>><?php echo esc_html($nombre); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </label>
                    <label>Número de guía
                      <input type="text" name="nj_guia" maxlength="40" required value="<?php echo esc_attr($row['guia']); ?>" placeholder="Ej. 1234567890">
                    </label>
                  </div>
                <?php endif; ?>
                <div class="nj-checks">
                  <?php foreach ($pasos as $paso) : ?>
                    <?php if ($paso['state'] === 'now') : ?>
                      <button class="nj-check is-now" type="submit" name="nj_estado" value="<?php echo esc_attr($paso['action']); ?>"><i></i><?php echo esc_html($paso['label']); ?></button>
                    <?php else : ?>
                      <span class="nj-check is-<?php echo esc_attr($paso['state']); ?>"><i></i><?php echo esc_html($paso['label']); ?></span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </form>
            <?php else : ?>
              <div class="nj-checks">
                <?php foreach ($pasos as $paso) : ?>
                  <span class="nj-check is-<?php echo esc_attr($paso['state']); ?>"><i></i><?php echo esc_html($paso['label']); ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </article>
        <?php
    }
}

if (!function_exists('nj_pedidos_form_base')) {
    function nj_pedidos_form_base($row, $consulta) {
        ?>
        <input type="hidden" name="action" value="nj_pedidos">
        <?php wp_nonce_field('nj_pedidos', 'nj_pedidos_nonce'); ?>
        <input type="hidden" name="pedido" value="<?php echo (int) $row['id']; ?>">
        <input type="hidden" name="nj_vista" value="<?php echo esc_attr($consulta['vista']); ?>">
        <input type="hidden" name="nj_fecha" value="<?php echo esc_attr($consulta['fecha']); ?>">
        <input type="hidden" name="nj_filtro" value="<?php echo esc_attr($consulta['estado']); ?>">
        <?php
    }
}

if (!function_exists('nj_pedidos_grupo_titulo')) {
    function nj_pedidos_grupo_titulo($clave, $vista) {
        if ($clave === 'sin-fecha') {
            return 'Sin fecha';
        }
        $stamp = strtotime($vista === 'ano' ? $clave . '-01 12:00:00' : $clave . ' 12:00:00');
        if (!$stamp) {
            return $clave;
        }
        if ($vista === 'ano' && function_exists('wp_date')) {
            return wp_date('F Y', $stamp);
        }
        if (function_exists('wp_date')) {
            return wp_date('j F Y', $stamp);
        }
        return date($vista === 'ano' ? 'F Y' : 'j F Y', $stamp);
    }
}

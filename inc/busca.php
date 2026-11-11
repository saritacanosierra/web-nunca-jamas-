<?php
/**
 * Coincidencias de la tienda: la frase, los sinónimos y el filtro de la consulta.
 */

if (!function_exists('nj_busca_valor')) {
    function nj_busca_valor() {
        if (!isset($_GET['nj'])) {
            return '';
        }
        $value = $_GET['nj'];
        if (function_exists('wp_unslash')) {
            $value = call_user_func('wp_unslash', $value);
        }
        if (function_exists('sanitize_text_field')) {
            $value = sanitize_text_field($value);
        } else {
            $value = trim(strip_tags((string) $value));
        }
        if (function_exists('mb_substr')) {
            $value = mb_substr($value, 0, 60);
        } else {
            $value = substr($value, 0, 60);
        }
        return trim($value);
    }
}

if (!function_exists('nj_busca_llave')) {
    function nj_busca_llave($value) {
        $value = function_exists('nj_menu_key') ? nj_menu_key($value) : strtolower((string) $value);
        $value = preg_replace('/[^a-z0-9]/', '', $value);
        $value = preg_replace('/(.)\1+/', '$1', (string) $value);
        return $value;
    }
}

if (!function_exists('nj_busca_grupos')) {
    function nj_busca_grupos() {
        $raw = array(
            array('pantalon', 'pantalones', 'legging', 'leggings', 'leggin', 'legins', 'leegins', 'licra', 'licras', 'calza', 'calzas', 'jogger'),
            array('short', 'shorts', 'bermuda', 'bermudas'),
            array('vestido', 'vestidos', 'tutu', 'tutus'),
            array('blusa', 'blusas', 'camiseta', 'camisetas'),
            array('falda', 'faldas'),
            array('pijama', 'pijamas'),
            array('conjunto', 'conjuntos'),
            array('accesorio', 'accesorios', 'tocado', 'bolso'),
            array('babyshower', 'babyshowers'),
            array('escolar', 'colegio', 'uniforme', 'uniformes'),
            array('vestidodebano', 'vestidosdebano', 'bano', 'bikini', 'banador'),
            array('personalizado', 'personalizados'),
        );
        $groups = array();
        foreach ($raw as $group) {
            $clean = array();
            foreach ($group as $word) {
                $key = nj_busca_llave($word);
                if ($key !== '') {
                    $clean[] = $key;
                }
            }
            $groups[] = array_values(array_unique($clean));
        }
        return $groups;
    }
}

if (!function_exists('nj_busca_cerca')) {
    function nj_busca_cerca($left, $right) {
        if ($left === '' || $right === '') {
            return false;
        }
        if ($left === $right || strpos($left, $right) !== false || strpos($right, $left) !== false) {
            return true;
        }
        if (strlen($left) < 5 || strlen($right) < 5 || abs(strlen($left) - strlen($right)) > 2) {
            return false;
        }
        return levenshtein($left, $right) <= 2;
    }
}

if (!function_exists('nj_busca_agujas')) {
    function nj_busca_agujas($texto) {
        $words = preg_split('/\s+/', trim((string) $texto));
        $groups = array();
        if (!is_array($words)) {
            return $groups;
        }
        foreach ($words as $word) {
            $key = nj_busca_llave($word);
            if (strlen($key) < 3) {
                continue;
            }
            $needles = array($key);
            foreach (nj_busca_grupos() as $group) {
                foreach ($group as $item) {
                    if (nj_busca_cerca($key, $item)) {
                        $needles = array_merge($needles, $group);
                        break;
                    }
                }
            }
            $groups[] = array_values(array_unique($needles));
            if (count($groups) >= 4) {
                break;
            }
        }
        return $groups;
    }
}

if (!function_exists('nj_busca_coincide')) {
    function nj_busca_coincide($texto, $consulta) {
        $groups = nj_busca_agujas($consulta);
        if (!$groups) {
            return true;
        }
        $key = nj_busca_llave($texto);
        $words = preg_split('/[^a-z0-9]+/', $key);
        if (!is_array($words)) {
            $words = array();
        }
        foreach ($groups as $needles) {
            $found = false;
            foreach ($needles as $needle) {
                if ($needle !== '' && strpos($key, $needle) !== false) {
                    $found = true;
                    break;
                }
                foreach ($words as $word) {
                    if (nj_busca_cerca($word, $needle)) {
                        $found = true;
                        break 2;
                    }
                }
            }
            if (!$found) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('nj_busca_sql_col')) {
    function nj_busca_sql_col($column) {
        $sql = 'LOWER(' . $column . ')';
        $map = array('á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n', 'ü' => 'u');
        foreach ($map as $from => $to) {
            $sql = "REPLACE($sql, '$from', '$to')";
        }
        return $sql;
    }
}

if (!function_exists('nj_busca_regex')) {
    function nj_busca_regex($key) {
        $chars = preg_split('//', $key, -1, PREG_SPLIT_NO_EMPTY);
        if (!$chars) {
            return '';
        }
        $parts = array();
        foreach ($chars as $char) {
            $parts[] = preg_quote($char, '/') . '+';
        }
        return implode('', $parts);
    }
}

if (!function_exists('nj_busca_consulta')) {
    function nj_busca_consulta($query) {
        if (is_admin() || !is_object($query) || !method_exists($query, 'is_main_query') || !$query->is_main_query()) {
            return;
        }
        $shop = (function_exists('is_shop') && is_shop()) || (function_exists('is_product_taxonomy') && is_product_taxonomy());
        if (!$shop) {
            return;
        }
        $texto = nj_busca_valor();
        if ($texto === '' || !nj_busca_agujas($texto)) {
            return;
        }
        $query->set('nj_busca', $texto);
    }
}
if (function_exists('add_action')) {
    add_action('pre_get_posts', 'nj_busca_consulta');
}

if (!function_exists('nj_busca_clausulas')) {
    function nj_busca_clausulas($clauses, $query) {
        if (!is_array($clauses) || !is_object($query) || !method_exists($query, 'get')) {
            return $clauses;
        }
        $texto = $query->get('nj_busca');
        $groups = is_string($texto) ? nj_busca_agujas($texto) : array();
        if (!$groups) {
            return $clauses;
        }
        global $wpdb;
        if (!isset($wpdb->posts)) {
            return $clauses;
        }
        if (strpos($clauses['join'], 'njtr') === false) {
            $clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} AS njtr ON njtr.object_id = {$wpdb->posts}.ID";
            $clauses['join'] .= " LEFT JOIN {$wpdb->term_taxonomy} AS njtt ON njtt.term_taxonomy_id = njtr.term_taxonomy_id AND njtt.taxonomy = 'product_cat'";
            $clauses['join'] .= " LEFT JOIN {$wpdb->terms} AS njt ON njt.term_id = njtt.term_id";
        }
        $columns = array(
            nj_busca_sql_col("{$wpdb->posts}.post_title"),
            nj_busca_sql_col("{$wpdb->posts}.post_excerpt"),
            nj_busca_sql_col("{$wpdb->posts}.post_content"),
            nj_busca_sql_col('njt.name'),
        );
        $ands = array();
        foreach ($groups as $needles) {
            $ors = array();
            foreach ($needles as $needle) {
                $pattern = nj_busca_regex($needle);
                if ($pattern === '') {
                    continue;
                }
                $quoted = method_exists($wpdb, 'prepare') ? $wpdb->prepare('%s', $pattern) : ("'" . addslashes($pattern) . "'");
                foreach ($columns as $column) {
                    $ors[] = $column . ' REGEXP ' . $quoted;
                }
            }
            if ($ors) {
                $ands[] = '(' . implode(' OR ', $ors) . ')';
            }
        }
        if ($ands) {
            $clauses['where'] .= ' AND ' . implode(' AND ', $ands);
            $clauses['groupby'] = "{$wpdb->posts}.ID";
        }
        return $clauses;
    }
}
if (function_exists('add_filter')) {
    add_filter('posts_clauses', 'nj_busca_clausulas', 10, 2);
}

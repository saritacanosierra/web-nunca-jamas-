<?php
/**
 * El blog de cuentos: la lista, cada publicación y el enlace desde la portada.
 */

if (!function_exists('nj_blog_url')) {
    function nj_blog_url() {
        if (function_exists('get_option') && function_exists('get_permalink')) {
            $page_id = (int) get_option('nj_page_blog');
            if ($page_id > 0) {
                $url = get_permalink($page_id);
                if (is_string($url) && $url !== '') {
                    return $url;
                }
            }
        }
        if (function_exists('home_url')) {
            return home_url('/blog/');
        }
        return '/blog/';
    }
}

if (!function_exists('nj_blog_minutes')) {
    function nj_blog_minutes($post_id) {
        $text = function_exists('get_post_field') ? (string) get_post_field('post_content', $post_id) : '';
        $plain = function_exists('wp_strip_all_tags') ? wp_strip_all_tags($text) : trim(strip_tags($text));
        $words = str_word_count($plain);
        $minutes = (int) max(1, ceil($words / 180));
        return $minutes === 1 ? '1 minuto' : $minutes . ' minutos';
    }
}

if (!function_exists('nj_blog_image')) {
    function nj_blog_image($post_id) {
        if ($post_id && function_exists('get_the_post_thumbnail_url')) {
            $url = get_the_post_thumbnail_url($post_id, 'large');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        return '';
    }
}

if (!function_exists('nj_blog_query')) {
    function nj_blog_query() {
        $page = 1;
        if (function_exists('get_query_var')) {
            $paged = (int) get_query_var('paged');
            $page = $paged > 0 ? $paged : (int) get_query_var('page');
            if ($page < 1) {
                $page = 1;
            }
        }
        if (!class_exists('WP_Query')) {
            return null;
        }
        return new WP_Query(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 9,
            'paged' => $page,
            'ignore_sticky_posts' => true,
        ));
    }
}

if (!function_exists('nj_blog_related')) {
    function nj_blog_related($post_id) {
        if (!class_exists('WP_Query')) {
            return array();
        }
        $categories = function_exists('wp_get_post_categories') ? wp_get_post_categories($post_id) : array();
        $query = new WP_Query(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 3,
            'post__not_in' => array((int) $post_id),
            'category__in' => $categories,
            'ignore_sticky_posts' => true,
        ));
        if (!$query->have_posts() && $categories) {
            $query = new WP_Query(array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 3,
                'post__not_in' => array((int) $post_id),
                'ignore_sticky_posts' => true,
            ));
        }
        return $query->posts;
    }
}

if (!function_exists('nj_blog_schema')) {
    function nj_blog_schema($post_id) {
        if (!function_exists('get_permalink') || !function_exists('get_the_title')) {
            return;
        }
        $image = nj_blog_image($post_id);
        $data = array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => get_the_title($post_id),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => function_exists('get_the_modified_date') ? get_the_modified_date('c', $post_id) : '',
            'description' => function_exists('get_the_excerpt') ? wp_strip_all_tags(get_the_excerpt($post_id)) : '',
            'mainEntityOfPage' => get_permalink($post_id),
            'author' => array(
                '@type' => 'Organization',
                'name' => 'Nunca Jamás',
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => 'Nunca Jamás',
            ),
        );
        if ($image !== '') {
            $data['image'] = array($image);
        }
        $json = function_exists('wp_json_encode') ? wp_json_encode($data) : json_encode($data);
        echo '<script type="application/ld+json">' . $json . '</script>';
    }
}

if (!function_exists('nj_menu_blog')) {
    function nj_menu_blog($menu) {
        if (!is_array($menu) || empty($menu['departments']) || !function_exists('nj_menu_key')) {
            return $menu;
        }
        $link = array(
            'label' => 'Leer cuentos',
            'href' => nj_blog_url(),
        );
        foreach ($menu['departments'] as $index => $department) {
            if (nj_menu_key($department['label']) !== 'cuentos') {
                continue;
            }
            $children = isset($department['children']) && is_array($department['children']) ? $department['children'] : array();
            foreach ($children as $child) {
                if (isset($child['href']) && $child['href'] === $link['href']) {
                    continue 2;
                }
            }
            array_unshift($children, $link);
            $menu['departments'][$index]['children'] = $children;
        }
        return $menu;
    }
}

if (!function_exists('nj_blog_stories')) {
    function nj_blog_stories() {
        return array(
            array(
                'title' => 'Por qué un cuento antes de dormir cambia la noche',
                'excerpt' => 'La voz de quien lee baja el ritmo del día. Un cuento corto, repetido, le da a la niña un lugar seguro para cerrar los ojos.',
                'content' => '<p>El día de una niña está lleno de ruidos, prisas y pantallas. El cuento de la noche hace lo contrario: alguien se sienta, abre un libro y la voz baja. Ese cambio de ritmo es el que el cuerpo reconoce como “ya podemos descansar”.</p><h2>La repetición no es aburrimiento</h2><p>Pedir el mismo cuento muchas noches no es falta de imaginación. Es la forma en que la infancia confirma que el mundo sigue en su sitio. La misma frase, la misma pausa y el mismo final le dicen que mañana también habrá quien lea.</p><h2>Qué conviene leer</h2><p>Un cuento breve, con una sola idea, basta. Puede ser un animal que busca su casa, una niña que presta un abrigo o un camino que termina en la cama. Lo que importa es la voz tranquila y el final claro. Si el cuento asusta, la noche se alarga.</p><p>Leer diez minutos juntos enseña más calma que alargar el día con otra actividad. El cuento no reemplaza el sueño: lo prepara.</p>',
            ),
            array(
                'title' => 'Leer en voz alta enseña más que las palabras',
                'excerpt' => 'Cuando una niña escucha un cuento, aprende cómo suena una frase, cómo se espera el turno y cómo se nombra lo que siente.',
                'content' => '<p>Un cuento leído en voz alta no es una clase. Aun así, la niña se lleva el ritmo de las frases, las palabras nuevas y la costumbre de escuchar hasta el final. Eso llega antes que saber leer sola.</p><h2>Lo que practica sin darse cuenta</h2><p>Sigue una historia de principio a fin. Anticipa lo que va a pasar. Pregunta cuando algo no cierra. Esas tres cosas son la base de comprender un texto más adelante, y también de conversar con otras personas.</p><h2>Cómo leer sin convertir el cuento en tarea</h2><p>Señala el dibujo, haz una pausa y deja que ella complete una frase que ya conoce. Si se distrae, vuelve a la imagen, no al regaño. El interés nace cuando el cuento sigue siendo un rato compartido.</p><p>La importancia del cuento en la niñez está en ese rato: alguien dedica atención completa a una historia y a la niña que la escucha.</p>',
            ),
            array(
                'title' => 'Cómo elegir un cuento según la edad',
                'excerpt' => 'No hace falta una biblioteca enorme. Hace falta un cuento que quepa en la atención de esa edad y que se pueda volver a leer.',
                'content' => '<p>Elegir un cuento no es buscar el más premiado. Es buscar el que esa niña puede seguir hoy. La edad cambia la duración, no la necesidad de que alguien lea.</p><h2>Los primeros años</h2><p>Antes de los tres, funcionan los cuentos con una acción por página: se lava, se pone los zapatos, se despide. El texto puede ser de una línea. La niña mira la cara de quien lee tanto como el libro.</p><h2>Cuando ya sostiene una historia</h2><p>Entre los tres y los seis, una trama corta con un problema y una solución alcanza. Un personaje que pierde algo y lo encuentra, o que tiene miedo y pide compañía. Conviene evitar finales tristes si la lectura es de noche.</p><h2>Cuando pregunta por qué</h2><p>Después, el cuento puede tener un dilema pequeño: compartir, decir la verdad, esperar un turno. Una pregunta al final, hecha con calma, basta. No hace falta una moraleja recitada.</p><p>Un estante corto, releído, sirve más que una pila de libros que no se abren.</p>',
            ),
        );
    }
}

if (!function_exists('nj_blog_ensure')) {
    function nj_blog_ensure() {
        if (!function_exists('wp_insert_post') || !function_exists('get_page_by_path')) {
            return;
        }
        $page = get_page_by_path('blog');
        $page_id = $page ? (int) $page->ID : 0;
        if (!$page_id) {
            $page_id = (int) wp_insert_post(array(
                'post_title' => 'Cuentos',
                'post_name' => 'blog',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
            ));
        }
        if ($page_id > 0) {
            update_option('nj_page_blog', $page_id);
        }
        if (get_option('nj_blog_stories') === '1') {
            return;
        }
        $sample = get_page_by_path('hello-world', OBJECT, 'post');
        if ($sample && strpos((string) $sample->post_content, 'Welcome to WordPress. This is your first post.') !== false && function_exists('wp_update_post')) {
            wp_update_post(array(
                'ID' => (int) $sample->ID,
                'post_status' => 'draft',
            ));
        }
        $term_id = 0;
        if (function_exists('wp_insert_term')) {
            $term = wp_insert_term('Cuentos para leer', 'category', array('slug' => 'leer-cuentos'));
            if (is_wp_error($term) && function_exists('get_term_by')) {
                $existing = get_term_by('slug', 'leer-cuentos', 'category');
                $term_id = $existing ? (int) $existing->term_id : 0;
            } elseif (is_array($term) && isset($term['term_id'])) {
                $term_id = (int) $term['term_id'];
            }
        }
        foreach (nj_blog_stories() as $story) {
            $already = get_page_by_title($story['title'], OBJECT, 'post');
            if ($already) {
                continue;
            }
            $post_id = (int) wp_insert_post(array(
                'post_title' => $story['title'],
                'post_excerpt' => $story['excerpt'],
                'post_content' => $story['content'],
                'post_status' => 'publish',
                'post_type' => 'post',
            ));
            if ($post_id > 0 && $term_id > 0 && function_exists('wp_set_post_categories')) {
                wp_set_post_categories($post_id, array($term_id));
            }
        }
        update_option('nj_blog_stories', '1');
    }
}
add_action('init', 'nj_blog_ensure', 30);

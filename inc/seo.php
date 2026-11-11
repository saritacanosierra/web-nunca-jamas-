<?php
/**
 * Títulos, descripción, canónico, ficha para compartir y datos estructurados.
 */

if (!function_exists('nj_seo_clip')) {
    function nj_seo_clip($text, $limit = 155) {
        $plain = function_exists('wp_strip_all_tags') ? wp_strip_all_tags((string) $text) : trim(strip_tags((string) $text));
        $plain = preg_replace('/\s+/', ' ', trim($plain));
        if ($plain === '') {
            return '';
        }
        if (function_exists('mb_strlen') && mb_strlen($plain) > $limit) {
            $plain = mb_substr($plain, 0, $limit - 1);
            $plain = preg_replace('/\s+\S*$/', '', $plain);
            $plain .= '…';
        }
        return $plain;
    }
}

if (!function_exists('nj_seo_absolute')) {
    function nj_seo_absolute($url) {
        $url = (string) $url;
        if ($url === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }
        if (function_exists('home_url')) {
            return home_url($url);
        }
        return $url;
    }
}

if (!function_exists('nj_seo_noindex')) {
    function nj_seo_noindex() {
        if (function_exists('is_404') && is_404()) {
            return true;
        }
        if (function_exists('is_search') && is_search()) {
            return true;
        }
        if (function_exists('is_author') && is_author()) {
            return true;
        }
        if (function_exists('is_attachment') && is_attachment()) {
            return true;
        }
        if (function_exists('is_cart') && is_cart()) {
            return true;
        }
        if (function_exists('is_checkout') && is_checkout()) {
            return true;
        }
        if (function_exists('is_account_page') && is_account_page()) {
            return true;
        }
        if (function_exists('is_page') && is_page('favoritos')) {
            return true;
        }
        return false;
    }
}

if (!function_exists('nj_seo_image')) {
    function nj_seo_image() {
        if (function_exists('is_singular') && is_singular('product') && function_exists('get_post_thumbnail_id')) {
            $url = wp_get_attachment_image_url(get_post_thumbnail_id(), 'large');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        if (function_exists('is_singular') && is_singular('post') && function_exists('nj_blog_image')) {
            $url = nj_blog_image(function_exists('get_queried_object_id') ? get_queried_object_id() : 0);
            if ($url !== '') {
                return nj_seo_absolute($url);
            }
        }
        if (function_exists('is_front_page') && is_front_page() && function_exists('nj_hero_rows') && function_exists('nj_hero_src')) {
            $rows = array_values(array_filter(nj_hero_rows(), 'nj_hero_is_usable'));
            if ($rows) {
                $src = nj_hero_src($rows[0]);
                if ($src !== '') {
                    return nj_seo_absolute($src);
                }
            }
        }
        if (function_exists('nj_tema_logo_url')) {
            return nj_seo_absolute(nj_tema_logo_url());
        }
        return nj_seo_absolute(function_exists('nj_img') ? nj_img('logo.png') : '');
    }
}

if (!function_exists('nj_seo_url')) {
    function nj_seo_url() {
        if (function_exists('is_singular') && is_singular()) {
            $url = function_exists('wp_get_canonical_url') ? wp_get_canonical_url() : get_permalink();
            return is_string($url) ? $url : '';
        }
        if (function_exists('is_front_page') && is_front_page()) {
            return home_url('/');
        }
        if (function_exists('is_shop') && is_shop() && function_exists('wc_get_page_permalink')) {
            return wc_get_page_permalink('shop');
        }
        if (function_exists('is_tax') && (is_tax() || is_category() || is_tag())) {
            $term = null;
            if (function_exists('get_queried_object')) {
                $term = get_queried_object();
            }
            $link = (is_object($term) && isset($term->term_id) && function_exists('get_term_link')) ? get_term_link($term) : '';
            if (is_wp_error($link) || !is_string($link)) {
                return '';
            }
            $paged = function_exists('get_query_var') ? (int) get_query_var('paged') : 1;
            if ($paged > 1) {
                return user_trailingslashit(untrailingslashit($link) . '/page/' . $paged);
            }
            return $link;
        }
        if (function_exists('is_home') && is_home() && function_exists('nj_blog_url')) {
            return nj_blog_url();
        }
        return home_url('/');
    }
}

if (!function_exists('nj_seo_description')) {
    function nj_seo_description() {
        $brand = 'Ropa de cuento para niñas, con envío a Colombia.';
        if (function_exists('is_front_page') && is_front_page()) {
            return nj_seo_clip('Nunca Jamás. ' . $brand . ' Vestidos, pijamas, accesorios y colecciones para estrenar.');
        }
        if (function_exists('is_shop') && is_shop()) {
            return nj_seo_clip('Tienda Nunca Jamás. Elige la prenda y la talla. ' . $brand);
        }
        if (function_exists('is_product_category') && is_product_category()) {
            $term = null;
            if (function_exists('get_queried_object')) {
                $term = get_queried_object();
            }
            $text = '';
            if (is_object($term) && isset($term->term_id) && function_exists('term_description')) {
                $text = term_description($term->term_id, 'product_cat');
            }
            if (nj_seo_clip($text) === '' && is_object($term) && isset($term->name) && $term->name !== '') {
                $text = 'Compra ' . $term->name . ' en Nunca Jamás. ' . $brand;
            }
            return nj_seo_clip($text);
        }
        if (function_exists('is_singular') && is_singular('product') && function_exists('wc_get_product')) {
            $product = wc_get_product(get_queried_object_id());
            if ($product) {
                $text = $product->get_short_description();
                if (nj_seo_clip($text) === '') {
                    $text = $product->get_description();
                }
                if (nj_seo_clip($text) === '') {
                    $text = $product->get_name() . '. ' . $brand;
                }
                return nj_seo_clip($text);
            }
        }
        if (function_exists('is_singular') && is_singular('post')) {
            return nj_seo_clip(get_the_excerpt(get_queried_object_id()));
        }
        if (function_exists('is_page') && is_page('blog')) {
            return nj_seo_clip('Cuentos para leer juntos y notas sobre por qué la lectura acompaña la niñez.');
        }
        if (function_exists('is_singular') && is_singular()) {
            $excerpt = get_the_excerpt(get_queried_object_id());
            if (nj_seo_clip($excerpt) === '') {
                $excerpt = get_post_field('post_content', get_queried_object_id());
            }
            if (nj_seo_clip($excerpt) === '') {
                $excerpt = get_the_title(get_queried_object_id()) . '. Nunca Jamás.';
            }
            return nj_seo_clip($excerpt);
        }
        return nj_seo_clip('Nunca Jamás. ' . $brand);
    }
}

if (!function_exists('nj_seo_title_parts')) {
    function nj_seo_title_parts($parts) {
        $parts['site'] = 'Nunca Jamás';
        if (function_exists('is_front_page') && is_front_page()) {
            $parts['title'] = function_exists('nj_espacio_texto') ? nj_espacio_texto('seo_title') : 'Ropa de cuento para niñas';
            unset($parts['tagline']);
        }
        if (isset($parts['title']) && nj_seo_clip($parts['title'], 55) !== '') {
            $parts['title'] = nj_seo_clip($parts['title'], 55);
        }
        return $parts;
    }
}

if (!function_exists('nj_seo_robots')) {
    function nj_seo_robots($robots) {
        if (nj_seo_noindex()) {
            return array(
                'noindex' => true,
                'follow' => true,
            );
        }
        $robots['max-image-preview'] = 'large';
        return $robots;
    }
}

if (!function_exists('nj_seo_head')) {
    function nj_seo_head() {
        if (!function_exists('is_front_page')) {
            return;
        }
        $description = nj_seo_description();
        $url = nj_seo_url();
        $image = nj_seo_image();
        $title = function_exists('wp_get_document_title') ? wp_get_document_title() : 'Nunca Jamás';
        $type = 'website';
        if (function_exists('is_singular') && is_singular('post')) {
            $type = 'article';
        }
        if (function_exists('is_singular') && is_singular('product')) {
            $type = 'product';
        }
        if ($image !== '' && function_exists('is_front_page') && (is_front_page() || (function_exists('is_singular') && is_singular('product')))) {
            echo '<link rel="preload" as="image" href="' . esc_url($image) . '" fetchpriority="high">' . "\n";
        }
        if ($description !== '') {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }
        if ($url !== '') {
            echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
            echo '<link rel="alternate" hreflang="es-CO" href="' . esc_url($url) . '">' . "\n";
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($url) . '">' . "\n";
            echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        }
        echo '<meta property="og:locale" content="es_CO">' . "\n";
        echo '<meta property="og:site_name" content="Nunca Jamás">' . "\n";
        echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        if ($description !== '') {
            echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        }
        if ($image !== '') {
            echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        if ($description !== '') {
            echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        }
        if ($type === 'product' && function_exists('wc_get_product') && function_exists('get_queried_object_id')) {
            $product = wc_get_product(get_queried_object_id());
            if ($product && $product->get_price() !== '') {
                echo '<meta property="product:price:amount" content="' . esc_attr($product->get_price()) . '">' . "\n";
                echo '<meta property="product:price:currency" content="' . esc_attr(function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'COP') . '">' . "\n";
            }
        }
    }
}

if (!function_exists('nj_seo_crumbs')) {
    function nj_seo_crumbs() {
        $crumbs = array(array('name' => 'Inicio', 'url' => home_url('/')));
        if (function_exists('is_front_page') && is_front_page()) {
            return $crumbs;
        }
        if (function_exists('is_singular') && is_singular('post')) {
            $crumbs[] = array('name' => 'Cuentos', 'url' => function_exists('nj_blog_url') ? nj_blog_url() : home_url('/blog/'));
            $crumbs[] = array('name' => get_the_title(), 'url' => get_permalink());
            return $crumbs;
        }
        if ((function_exists('is_shop') && is_shop()) || (function_exists('is_product_category') && is_product_category()) || (function_exists('is_singular') && is_singular('product'))) {
            $shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
            $crumbs[] = array('name' => 'Tienda', 'url' => $shop);
        }
        if (function_exists('is_product_category') && is_product_category()) {
            $term = null;
            if (function_exists('get_queried_object')) {
                $term = get_queried_object();
            }
            if (is_object($term) && isset($term->term_id) && function_exists('get_ancestors')) {
                $ancestors = array_reverse(get_ancestors($term->term_id, 'product_cat'));
                foreach ($ancestors as $ancestor_id) {
                    $ancestor = function_exists('get_term') ? get_term($ancestor_id, 'product_cat') : null;
                    $link = ($ancestor && !is_wp_error($ancestor) && function_exists('get_term_link')) ? get_term_link($ancestor) : '';
                    if ($ancestor && !is_wp_error($ancestor) && is_string($link)) {
                        $crumbs[] = array('name' => $ancestor->name, 'url' => $link);
                    }
                }
                $self = function_exists('get_term_link') ? get_term_link($term) : '';
                $crumbs[] = array('name' => $term->name, 'url' => is_string($self) ? $self : '');
            }
            return $crumbs;
        }
        if (function_exists('is_singular') && is_singular('product')) {
            $terms = get_the_terms(get_queried_object_id(), 'product_cat');
            if (is_array($terms)) {
                foreach ($terms as $term) {
                    if (in_array($term->slug, array('uncategorized', 'sin-categorizar'), true)) {
                        continue;
                    }
                    $link = get_term_link($term);
                    if (is_string($link)) {
                        $crumbs[] = array('name' => $term->name, 'url' => $link);
                    }
                    break;
                }
            }
            $crumbs[] = array('name' => get_the_title(), 'url' => get_permalink());
            return $crumbs;
        }
        if (function_exists('is_page') && is_page('blog')) {
            $crumbs[] = array('name' => 'Cuentos', 'url' => nj_seo_url());
            return $crumbs;
        }
        if (function_exists('is_singular') && is_singular('page')) {
            $crumbs[] = array('name' => get_the_title(), 'url' => get_permalink());
        }
        return $crumbs;
    }
}

if (!function_exists('nj_seo_product')) {
    function nj_seo_product() {
        if (!function_exists('is_singular') || !is_singular('product') || !function_exists('wc_get_product')) {
            return null;
        }
        $product = wc_get_product(get_queried_object_id());
        if (!$product) {
            return null;
        }
        $data = array(
            '@type' => 'Product',
            'name' => $product->get_name(),
            'description' => nj_seo_description(),
            'url' => get_permalink($product->get_id()),
            'brand' => array(
                '@type' => 'Brand',
                'name' => 'Nunca Jamás',
            ),
        );
        $images = array();
        $image = nj_seo_image();
        if ($image !== '') {
            $images[] = $image;
        }
        if (method_exists($product, 'get_gallery_image_ids') && function_exists('wp_get_attachment_image_url')) {
            foreach ($product->get_gallery_image_ids() as $attachment_id) {
                $gallery = wp_get_attachment_image_url($attachment_id, 'large');
                if (is_string($gallery) && $gallery !== '' && !in_array($gallery, $images, true)) {
                    $images[] = $gallery;
                }
                if (count($images) >= 6) {
                    break;
                }
            }
        }
        if ($images) {
            $data['image'] = $images;
        }
        $sku = $product->get_sku();
        if ($sku !== '') {
            $data['sku'] = $sku;
        }
        $currency = function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'COP';
        $stock = $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';
        $offer = array(
            'priceCurrency' => $currency,
            'availability' => $stock,
            'itemCondition' => 'https://schema.org/NewCondition',
            'url' => get_permalink($product->get_id()),
            'seller' => array(
                '@type' => 'Organization',
                'name' => 'Nunca Jamás',
            ),
            'shippingDetails' => nj_seo_shipping(),
        );
        if ($product->is_type('variable')) {
            $low = $product->get_variation_price('min');
            $high = $product->get_variation_price('max');
            if ($low !== '') {
                $head = array(
                    '@type' => 'AggregateOffer',
                    'lowPrice' => $low,
                    'highPrice' => $high,
                );
                $children = $product->get_children();
                if ($children) {
                    $head['offerCount'] = count($children);
                }
                $data['offers'] = array_merge($head, $offer);
            }
        } else {
            $price = $product->get_price();
            if ($price !== '') {
                $data['offers'] = array_merge(array(
                    '@type' => 'Offer',
                    'price' => $price,
                ), $offer);
            }
        }
        return $data;
    }
}

if (!function_exists('nj_seo_shipping')) {
    function nj_seo_shipping() {
        $cost = 15000;
        if (function_exists('nj_slots_data')) {
            $bag = nj_slots_data()['bag'];
            if (isset($bag['shipping_cost'])) {
                $cost = (int) $bag['shipping_cost'];
            }
        }
        return array(
            '@type' => 'OfferShippingDetails',
            'shippingDestination' => array(
                '@type' => 'DefinedRegion',
                'addressCountry' => 'CO',
            ),
            'shippingRate' => array(
                '@type' => 'MonetaryAmount',
                'value' => (string) $cost,
                'currency' => 'COP',
            ),
        );
    }
}

if (!function_exists('nj_seo_faq')) {
    function nj_seo_faq() {
        if (!function_exists('nj_pages_help_current') || !function_exists('is_page') || !is_page()) {
            return null;
        }
        $help = nj_pages_help_current();
        if (!is_array($help) || empty($help['items']) || !is_array($help['items'])) {
            return null;
        }
        $entities = array();
        foreach ($help['items'] as $item) {
            $question = isset($item['q']) ? trim(wp_strip_all_tags((string) $item['q'])) : '';
            $answer = isset($item['a']) ? trim(wp_strip_all_tags((string) $item['a'])) : '';
            if ($question === '' || $answer === '') {
                continue;
            }
            $entities[] = array(
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $answer,
                ),
            );
        }
        if (count($entities) < 2) {
            return null;
        }
        return array(
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        );
    }
}

if (!function_exists('nj_seo_same_as')) {
    function nj_seo_same_as() {
        if (!function_exists('nj_tema_footer')) {
            return array();
        }
        $footer = nj_tema_footer();
        $links = array();
        if (empty($footer['socials']) || !is_array($footer['socials'])) {
            return $links;
        }
        foreach ($footer['socials'] as $social) {
            $href = isset($social['href']) ? (string) $social['href'] : '';
            if (preg_match('#^https?://#i', $href)) {
                $links[] = $href;
            }
        }
        return $links;
    }
}

if (!function_exists('nj_seo_article')) {
    function nj_seo_article() {
        if (!function_exists('is_singular') || !is_singular('post')) {
            return null;
        }
        $id = get_queried_object_id();
        $data = array(
            '@type' => 'BlogPosting',
            'headline' => get_the_title($id),
            'description' => nj_seo_description(),
            'datePublished' => get_the_date('c', $id),
            'dateModified' => get_the_modified_date('c', $id),
            'mainEntityOfPage' => get_permalink($id),
            'author' => array('@type' => 'Organization', 'name' => 'Nunca Jamás'),
            'publisher' => array('@type' => 'Organization', 'name' => 'Nunca Jamás'),
        );
        $image = nj_seo_image();
        if ($image !== '') {
            $data['image'] = array($image);
        }
        return $data;
    }
}

if (!function_exists('nj_seo_graph')) {
    function nj_seo_graph() {
        if (!function_exists('home_url') || nj_seo_noindex()) {
            return;
        }
        $logo = function_exists('nj_tema_logo_url') ? nj_seo_absolute(nj_tema_logo_url()) : nj_seo_image();
        $organization = array(
            '@type' => 'Organization',
            'name' => 'Nunca Jamás',
            'url' => home_url('/'),
            'logo' => $logo,
            'email' => 'saritacanosierra@gmail.com',
            'areaServed' => 'CO',
        );
        $same = nj_seo_same_as();
        if ($same) {
            $organization['sameAs'] = $same;
        }
        $graph = array(
            $organization,
            array(
                '@type' => 'WebSite',
                'name' => 'Nunca Jamás',
                'url' => home_url('/'),
                'inLanguage' => 'es-CO',
                'potentialAction' => array(
                    '@type' => 'SearchAction',
                    'target' => home_url('/?s={search_term_string}&post_type=product'),
                    'query-input' => 'required name=search_term_string',
                ),
            ),
        );
        $crumbs = nj_seo_crumbs();
        if (count($crumbs) > 1) {
            $items = array();
            foreach ($crumbs as $index => $crumb) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'],
                );
            }
            $graph[] = array(
                '@type' => 'BreadcrumbList',
                'itemListElement' => $items,
            );
        }
        $product = nj_seo_product();
        if ($product) {
            $graph[] = $product;
        }
        $article = nj_seo_article();
        if ($article) {
            $graph[] = $article;
        }
        $faq = nj_seo_faq();
        if ($faq) {
            $graph[] = $faq;
        }
        $json = function_exists('wp_json_encode') ? wp_json_encode(array('@context' => 'https://schema.org', '@graph' => $graph)) : json_encode(array('@context' => 'https://schema.org', '@graph' => $graph));
        echo '<script type="application/ld+json">' . $json . '</script>' . "\n";
    }
}

if (!function_exists('nj_seo_sitemap_types')) {
    function nj_seo_sitemap_types($types) {
        unset($types['mailpoet_page']);
        return $types;
    }
}

if (!function_exists('nj_seo_sitemap_provider')) {
    function nj_seo_sitemap_provider($provider, $name) {
        if ($name === 'users') {
            return false;
        }
        return $provider;
    }
}

if (!function_exists('nj_seo_sitemap_pages')) {
    function nj_seo_sitemap_pages($args, $post_type) {
        if ($post_type !== 'page' || !function_exists('wc_get_page_id')) {
            return $args;
        }
        $skip = array();
        foreach (array('cart', 'checkout', 'myaccount') as $key) {
            $id = (int) wc_get_page_id($key);
            if ($id > 0) {
                $skip[] = $id;
            }
        }
        if ($skip) {
            $args['post__not_in'] = isset($args['post__not_in']) ? array_merge((array) $args['post__not_in'], $skip) : $skip;
        }
        return $args;
    }
}

if (!function_exists('nj_seo_private_paths')) {
    function nj_seo_private_paths() {
        $paths = array('/favoritos/');
        if (function_exists('wc_get_page_id') && function_exists('get_permalink')) {
            foreach (array('cart', 'checkout', 'myaccount') as $key) {
                $id = (int) wc_get_page_id($key);
                if ($id < 1) {
                    continue;
                }
                $link = get_permalink($id);
                $path = is_string($link) && function_exists('wp_parse_url') ? wp_parse_url($link, PHP_URL_PATH) : '';
                if (!is_string($path) || $path === '' || $path === '/') {
                    continue;
                }
                $paths[] = '/' . trim($path, '/') . '/';
            }
        }
        return array_values(array_unique($paths));
    }
}

if (!function_exists('nj_seo_robots_txt')) {
    function nj_seo_robots_txt($output, $public) {
        if ((string) $public === '0') {
            return $output;
        }
        $lines = array();
        foreach (nj_seo_private_paths() as $path) {
            $lines[] = 'Disallow: ' . $path;
        }
        $lines[] = 'Disallow: /*?add-to-cart=';
        $lines[] = 'Disallow: /*?orderby=';
        $lines[] = 'Disallow: /*?*min_price=';
        $lines[] = 'Disallow: /*?*max_price=';
        $block = implode("\n", $lines);
        if (strpos($output, 'Sitemap:') !== false) {
            return preg_replace('/\nSitemap:/', "\n" . $block . "\nSitemap:", $output, 1);
        }
        return rtrim($output) . "\n" . $block . "\n";
    }
}

if (function_exists('add_filter')) {
    add_filter('document_title_separator', function () {
        return '|';
    });
    add_filter('document_title_parts', 'nj_seo_title_parts');
    add_filter('wp_robots', 'nj_seo_robots');
    add_filter('wp_sitemaps_post_types', 'nj_seo_sitemap_types');
    add_filter('wp_sitemaps_add_provider', 'nj_seo_sitemap_provider', 10, 2);
    add_filter('wp_sitemaps_posts_query_args', 'nj_seo_sitemap_pages', 10, 2);
    add_filter('robots_txt', 'nj_seo_robots_txt', 20, 2);
    remove_action('wp_head', 'rel_canonical');
    add_action('wp_head', 'nj_seo_head', 1);
    add_action('wp_head', 'nj_seo_graph', 2);
}

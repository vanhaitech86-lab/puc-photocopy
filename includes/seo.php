<?php
// seo.php

function render_meta_tags($title, $description, $keywords, $canonical_url, $image = null) {
    $site_name = defined('SITE_NAME') ? SITE_NAME : 'PUC';
    $full_title = $title . ' | ' . $site_name;
    
    $html = '<title>' . htmlspecialchars($full_title) . '</title>' . PHP_EOL;
    $html .= '<meta name="description" content="' . htmlspecialchars($description) . '">' . PHP_EOL;
    $html .= '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">' . PHP_EOL;
    $html .= '<link rel="canonical" href="' . htmlspecialchars($canonical_url) . '">' . PHP_EOL;
    
    return $html;
}

function render_og_tags($title, $description, $url, $image = null, $type = 'website') {
    $site_name = defined('SITE_NAME') ? SITE_NAME : 'PUC';
    $default_image = defined('SITE_URL') ? SITE_URL . '/assets/images/logo.png' : '/assets/images/logo.png';
    $og_image = $image ? $image : $default_image;
    
    $html = '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . PHP_EOL;
    $html .= '<meta property="og:description" content="' . htmlspecialchars($description) . '">' . PHP_EOL;
    $html .= '<meta property="og:url" content="' . htmlspecialchars($url) . '">' . PHP_EOL;
    $html .= '<meta property="og:site_name" content="' . htmlspecialchars($site_name) . '">' . PHP_EOL;
    $html .= '<meta property="og:type" content="' . htmlspecialchars($type) . '">' . PHP_EOL;
    $html .= '<meta property="og:image" content="' . htmlspecialchars($og_image) . '">' . PHP_EOL;
    
    return $html;
}

function render_schema_product($product) {
    $site_url = defined('SITE_URL') ? SITE_URL : '';
    
    $schema = [
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $product['name'],
        'image' => $site_url . '/' . $product['image'],
        'description' => strip_tags($product['description'] ?? $product['name']),
        'brand' => [
            '@type' => 'Brand',
            'name' => $product['brand_name'] ?? 'PUC'
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => $site_url . '/san-pham/' . $product['slug'],
            'priceCurrency' => 'VND',
            'price' => $product['price'] ?? 0,
            'availability' => 'https://schema.org/InStock',
            'itemCondition' => 'https://schema.org/NewCondition'
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . PHP_EOL;
}

function render_schema_organization() {
    $site_url = defined('SITE_URL') ? SITE_URL : '';
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'PUC',
        'url' => $site_url,
        'logo' => $site_url . '/assets/images/logo.png',
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => '0907586969',
            'contactType' => 'customer service',
            'email' => 'phuong86.annguyen@gmail.com'
        ],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Số 21 ngõ 75 Cầu Đất - Cửa Nam',
            'addressLocality' => 'Hà Nội',
            'addressCountry' => 'VN'
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . PHP_EOL;
}

function render_schema_breadcrumb($items) {
    $itemListElement = [];
    $position = 1;
    
    foreach ($items as $item) {
        $itemListElement[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $item['name'],
            'item' => $item['url']
        ];
        $position++;
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $itemListElement
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . PHP_EOL;
}

function render_schema_website() {
    $site_url = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
    
    $schema = [
        '@context' => 'https://schema.org/',
        '@type' => 'WebSite',
        'name' => 'PUC',
        'url' => $site_url . '/',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => $site_url . '/tim-kiem?q={search_term_string}',
            'query-input' => 'required name=search_term_string'
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . PHP_EOL;
}
?>

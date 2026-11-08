<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopEaseLoading
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Standalone Blade pages share this shell without changing API/download responses.
        if (! $response instanceof \Illuminate\Http\Response
            || ! str_contains(strtolower($response->headers->get('Content-Type', '')), 'text/html')
            || $response->headers->has('Content-Disposition')) {
            return $response;
        }

        $html = $response->getContent();
        if (! preg_match('/<head\b[^>]*>/i', $html) || ! preg_match('/<body\b[^>]*>/i', $html)
            || str_contains($html, 'data-shopease-loading-shell')) {
            return $response;
        }

        $version = max(filemtime(public_path('js/shopease-loading.js')), filemtime(public_path('js/shopease-navigation.js')), filemtime(public_path('css/shopease-loading.css')));
        $css = e(asset('css/shopease-loading.css').'?v='.$version);
        $js = e(asset('js/shopease-loading.js').'?v='.$version);
        $navigation = e(asset('js/shopease-navigation.js').'?v='.$version);
        $logo = e(asset('icons/logos/bag.png'));
        // The PNG has a large transparent canvas. Crop its viewport without altering the asset.
        $outline = 'M720 610V594C720 534 767 486 827 486S935 534 935 594V610H962Q994 610 998 642L1044 921Q1052 978 994 978H672Q615 978 621 920L660 642Q664 610 695 610Z';
        $hasSidebar = (bool) preg_match('/<aside\b[^>]*\bid=["\'](?:admin-sidebar|sellerSidebar|lgSidebar)["\']/i', $html);
        $hasLogisticsSidebar = (bool) preg_match('/<aside\b[^>]*\bid=["\']lgSidebar["\']/i', $html);
        if ($hasLogisticsSidebar) {
            $tableCss = e(asset('css/logistics-tables.css').'?v='.filemtime(public_path('css/logistics-tables.css')));
            // Load after each page's styles, including standalone inline styles.
            $html = preg_replace_callback('/<\/head>/i', fn ($match) => '<link rel="stylesheet" href="'.$tableCss.'">'.$match[0], $html, 1);
            $html = preg_replace('/<body\b/i', '<body data-se-logistics-tables', $html, 1);
        }
        $head = '<meta name="se-page-kind" content="'.($hasSidebar ? 'account' : 'public').'"><link rel="stylesheet" href="'.$css.'"><script src="'.$navigation.'"></script><script src="'.$js.'"></script>';
        if ($hasSidebar) {
            // Vite's development CSS arrives through modules. Supply the shared
            // utilities as a normal blocking stylesheet before the first paint.
            $manifestPath = public_path('build/manifest.json');
            $manifest = is_file($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
            $utilities = $manifest['resources/css/app.css']['file'] ?? null;
            if ($utilities && is_file(public_path('build/'.$utilities))) {
                $head = '<link rel="stylesheet" href="'.e(asset('build/'.$utilities)).'">'.$head;
            }
            $head = '<link rel="expect" href="#se-sidebar-ready" blocking="render">'.$head;
            // All interfaces use the same local font, independent of each
            // page's Google Fonts imports or fallback body font.
            $fontCss = '';
            foreach ($manifest as $entry) {
                $file = $entry['file'] ?? '';
                if (preg_match('/instrument-sans-(400|500|600)-normal-.*\.woff2$/', $file, $match) && is_file(public_path('build/'.$file))) {
                    $fontCss .= '@font-face{font-family:ShopEaseSidebar;font-style:normal;font-weight:'.$match[1].';font-display:block;src:url("data:font/woff2;base64,'.base64_encode(file_get_contents(public_path('build/'.$file))).'") format("woff2")}';
                }
            }
            if ($fontCss !== '') {
                $head = '<style>'.$fontCss.' :is(#admin-sidebar,#sellerSidebar,#lgSidebar),:is(#admin-sidebar,#sellerSidebar,#lgSidebar) *{font-family:ShopEaseSidebar,Arial,sans-serif!important}</style>'.$head;
            }
        }
        // Small sidebar images travel with the HTML, avoiding an empty icon
        // slot while a separate image request completes on each document visit.
        $html = preg_replace_callback('/<aside\b[^>]*\bid=["\'](?:admin-sidebar|sellerSidebar|lgSidebar)["\'][^>]*>.*?<\/aside>/is', function ($aside) {
            return preg_replace_callback('/\bsrc=(["\'])(.*?)\1/i', function ($image) {
                $url = html_entity_decode($image[2], ENT_QUOTES, 'UTF-8');
                $base = rtrim(asset(''), '/').'/';
                if (! str_starts_with($url, $base.'icons/')) {
                    return $image[0];
                }
                $relative = rawurldecode(explode('?', substr($url, strlen($base)), 2)[0]);
                $path = realpath(public_path($relative));
                $icons = realpath(public_path('icons'));
                if (! $path || ! $icons || ! str_starts_with(strtolower($path), strtolower($icons).DIRECTORY_SEPARATOR)
                    || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'png' || filesize($path) > 131072) {
                    return $image[0];
                }
                return 'src="data:image/png;base64,'.base64_encode(file_get_contents($path)).'" decoding="sync" loading="eager"';
            }, $aside[0]);
        }, $html);
        $shell = '<div data-shopease-loading-shell id="se-page-loader" role="status" aria-live="polite" aria-label="Loading ShopEase">'
            .'<div class="se-loader-card"><div class="se-loader-logo"><svg class="se-loader-bag" viewBox="480 460 600 550" width="110" height="101" aria-hidden="true">'
            .'<image href="'.$logo.'" x="0" y="0" width="1600" height="1600"/>'
            .'<path class="se-bag-trail" d="'.$outline.'" pathLength="100"/>'
            .'<path class="se-bag-outline" d="'.$outline.'" pathLength="100"/></svg></div>'
            .'</div></div>'
            .'<div id="se-request-progress" role="status" aria-label="Working" hidden><span></span></div>'
            .'<div id="se-page-skeleton" role="status" aria-label="Loading page"><div class="se-skeleton-heading"></div><div class="se-skeleton-caption"></div>'
            .'<div class="se-skeleton-toolbar"></div><div class="se-skeleton-cards"><i></i><i></i><i></i><i></i></div>'
            .'<div class="se-skeleton-panel"><div class="se-skeleton-heading"></div><i></i><i></i><i></i><i></i><i></i></div></div>';
        $html = preg_replace_callback('/<head\b[^>]*>/i', fn ($m) => $m[0].$head, $html, 1);
        $html = preg_replace_callback('/<body\b[^>]*>/i', fn ($m) => $m[0].$shell, $html, 1);
        $response->setContent($html);
        $response->headers->remove('Content-Length');

        return $response;
    }
}

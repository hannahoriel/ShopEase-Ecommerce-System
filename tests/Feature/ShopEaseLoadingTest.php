<?php

namespace Tests\Feature;

use App\Http\Middleware\ShopEaseLoading;
use Illuminate\Http\Request;
use Tests\TestCase;

class ShopEaseLoadingTest extends TestCase
{
    public function test_web_route_automatically_receives_the_loader(): void
    {
        config(['session.driver' => 'array']);
        \Illuminate\Support\Facades\Route::middleware('web')->get('/_loading-check', fn () => response('<html><head></head><body>ShopEase</body></html>'));
        $this->get('/_loading-check')->assertOk()->assertSee('data-shopease-loading-shell', false);
    }

    public function test_standalone_html_receives_one_shared_loading_shell(): void
    {
        $middleware = new ShopEaseLoading;
        $request = Request::create('/logistics/reports');
        $html = '<!doctype html><html><head><title>Reports</title></head><body><button id="save">Save</button></body></html>';
        $response = $middleware->handle($request, fn () => response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']));
        $this->assertStringContainsString('shopease-loading.js', $response->getContent());
        $this->assertStringContainsString('id="se-page-loader"', $response->getContent());
        $this->assertStringContainsString('icons/logos/bag.png', $response->getContent());
        $this->assertStringContainsString('class="se-bag-outline"', $response->getContent());
        $this->assertStringContainsString('<button id="save">Save</button>', $response->getContent());
        $again = $middleware->handle($request, fn () => $response);
        $this->assertSame(1, substr_count($again->getContent(), 'id="se-page-loader"'));
    }

    public function test_api_download_redirect_and_html_fragments_are_untouched(): void
    {
        $middleware = new ShopEaseLoading;
        $request = Request::create('/test');
        $responses = [
            response()->json(['ok' => true]),
            redirect('/login'),
            response('<div>Partial modal content</div>'),
            response('<html><head></head><body>Export</body></html>')->header('Content-Disposition', 'attachment; filename=report.html'),
        ];
        foreach ($responses as $response) {
            $before = $response->getContent();
            $result = $middleware->handle($request, fn () => $response);
            $this->assertSame($before, $result->getContent());
        }
    }

    public function test_sidebar_icons_are_included_in_html_and_public_pages_are_marked(): void
    {
        $middleware = new ShopEaseLoading;
        $request = Request::create('/admin/dashboard');
        $src = e(asset('icons/admin/dashboard/sidebar&navbar/dashboard.png'));
        $html = '<html><head></head><body><aside id="admin-sidebar"><img src="'.$src.'"></aside></body></html>';
        $response = $middleware->handle($request, fn () => response($html, 200, ['Content-Type' => 'text/html']));
        $this->assertStringContainsString('data:image/png;base64,', $response->getContent());
        $this->assertStringContainsString('name="se-page-kind" content="account"', $response->getContent());
        $public = $middleware->handle($request, fn () => response('<html><head></head><body>Login</body></html>', 200, ['Content-Type' => 'text/html']));
        $this->assertStringContainsString('name="se-page-kind" content="public"', $public->getContent());
    }
}

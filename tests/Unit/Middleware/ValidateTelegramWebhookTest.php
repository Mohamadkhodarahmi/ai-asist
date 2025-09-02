<?php

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use App\Models\TelegramBot;
use App\Http\Middleware\ValidateTelegramWebhook;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateTelegramWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_passes_with_valid_active_bot()
    {
        $bot = TelegramBot::factory()->create(['is_active' => true]);
        
        $request = Request::create('/webhook/telegram/' . $bot->bot_token, 'POST');
        $route = new Route('POST', '/webhook/telegram/{token}', []);
        $route->bind($request);
        $route->setParameter('token', $bot->bot_token);
        $request->setRouteResolver(fn () => $route);

        $middleware = new ValidateTelegramWebhook();
        
        $response = $middleware->handle($request, function ($req) {
            $this->assertNotNull($req->telegram_bot);
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_middleware_fails_with_inactive_bot()
    {
        $bot = TelegramBot::factory()->create(['is_active' => false]);
        
        $request = Request::create('/webhook/telegram/' . $bot->bot_token, 'POST');
        $route = new Route('POST', '/webhook/telegram/{token}', []);
        $route->bind($request);
        $route->setParameter('token', $bot->bot_token);
        $request->setRouteResolver(fn () => $route);

        $middleware = new ValidateTelegramWebhook();
        
        $response = $middleware->handle($request, function ($req) {
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_middleware_fails_with_invalid_token()
    {
        $request = Request::create('/webhook/telegram/invalid-token', 'POST');
        $route = new Route('POST', '/webhook/telegram/{token}', []);
        $route->bind($request);
        $route->setParameter('token', 'invalid-token');
        $request->setRouteResolver(fn () => $route);

        $middleware = new ValidateTelegramWebhook();
        
        $response = $middleware->handle($request, function ($req) {
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(403, $response->getStatusCode());
    }
}
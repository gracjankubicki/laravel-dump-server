<?php

namespace BeyondCode\DumpServer\Tests;

use BeyondCode\DumpServer\RequestContextProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;

class RequestContextProviderTest extends TestCase
{
    public function test_no_request_produces_no_context(): void
    {
        self::assertNull((new RequestContextProvider)->getContext());
    }

    public function test_request_without_route_has_uri_and_method(): void
    {
        $request = Request::create('https://example.test/debug?q=one', 'POST');
        $context = (new RequestContextProvider($request))->getContext();
        self::assertSame('POST', $context['method']);
        self::assertSame('https://example.test/debug?q=one', $context['uri']);
        self::assertSame(spl_object_hash($request), $context['identifier']);
        self::assertNull($context['controller']->getValue());
    }

    public function test_closure_route_context_is_serializable(): void
    {
        $request = Request::create('/closure');
        $route = new Route('GET', '/closure', static fn () => 'ok');
        $request->setRouteResolver(static fn () => $route);
        $context = (new RequestContextProvider($request))->getContext();
        self::assertSame('Closure', $context['controller']->getValue());
        self::assertIsString(serialize($context));
    }

    public function test_controller_object_context_uses_class_basename(): void
    {
        $request = Request::create('/controller');
        $route = new Route('GET', '/controller', ['uses' => 'ExampleController@index']);
        $route->controller = new \stdClass;
        $request->setRouteResolver(static fn () => $route);
        $context = (new RequestContextProvider($request))->getContext();
        self::assertSame('stdClass', $context['controller']->getValue());
    }

    public function test_invalid_controller_has_clear_error(): void
    {
        $request = Request::create('/invalid');
        $route = new Route('GET', '/invalid', ['uses' => 'ExampleController@index']);
        $route->controller = 123;
        $request->setRouteResolver(static fn () => $route);
        $this->expectException(\UnexpectedValueException::class);
        (new RequestContextProvider($request))->getContext();
    }
}

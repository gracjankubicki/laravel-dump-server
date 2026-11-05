<?php

namespace BeyondCode\DumpServer\Tests;

use BeyondCode\DumpServer\RequestContextProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;

class RequestContextProviderTest extends TestCase
{
    public function testNoRequestProducesNoContext(): void
    {
        self::assertNull((new RequestContextProvider)->getContext());
    }

    public function testRequestWithoutRouteHasUriAndMethod(): void
    {
        $request = Request::create('https://example.test/debug?q=one', 'POST');
        $context = (new RequestContextProvider($request))->getContext();
        self::assertSame('POST', $context['method']);
        self::assertSame('https://example.test/debug?q=one', $context['uri']);
        self::assertSame(spl_object_hash($request), $context['identifier']);
        self::assertNull($context['controller']->getValue());
    }

    public function testClosureRouteContextIsSerializable(): void
    {
        $request = Request::create('/closure');
        $route = new Route('GET', '/closure', static fn () => 'ok');
        $request->setRouteResolver(static fn () => $route);
        $context = (new RequestContextProvider($request))->getContext();
        self::assertSame('Closure', $context['controller']->getValue());
        self::assertIsString(serialize($context));
    }
}

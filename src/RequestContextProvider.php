<?php

namespace BeyondCode\DumpServer;

use Illuminate\Http\Request;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;
use UnexpectedValueException;

class RequestContextProvider implements ContextProviderInterface
{
    private ?Request $currentRequest = null;

    private VarCloner $cloner;

    public function __construct(?Request $currentRequest = null)
    {
        $this->currentRequest = $currentRequest;
        $this->cloner = new VarCloner;
        $this->cloner->setMaxItems(0);
    }

    /**
     * Get the context.
     *
     * @return array{uri: string, method: string, controller: Data, identifier: string}|null
     */
    public function getContext(): ?array
    {
        if ($this->currentRequest === null) {
            return null;
        }

        $controller = null;

        if ($route = $this->currentRequest->route()) {
            $controller = $route->controller;

            if (! $controller && ! is_string($route->action['uses'])) {
                $controller = $route->action['uses'];
            }
        }

        if ($controller && ! is_object($controller) && ! is_string($controller)) {
            throw new UnexpectedValueException('The route controller must be an object or a class name.');
        }

        return [
            'uri' => $this->currentRequest->getUri(),
            'method' => $this->currentRequest->getMethod(),
            'controller' => $controller ? $this->cloner->cloneVar(class_basename($controller)) : $this->cloner->cloneVar(null),
            'identifier' => spl_object_hash($this->currentRequest),
        ];
    }
}

<?php

namespace App\Infrastructure\Shared\Http;

use App\Domain\Shared\Http\HttpResponseFactoryInterface;
use App\Domain\Shared\Http\HttpResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class SymfonyHttpResponseFactory implements HttpResponseFactoryInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;

    public function __construct(UrlGeneratorInterface $urlGenerator, Environment $twig)
    {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function create($data, int $statusCode = 200, array $headers = []): HttpResponseInterface
    {
        if (is_array($data) || is_object($data)) {
            $response = new JsonResponse($data, $statusCode, $headers);
        } else {
            $response = new Response((string) $data, $statusCode, $headers);
        }

        return new SymfonyHttpResponse($response);
    }

    public function redirect(string $url, int $statusCode = 302): HttpResponseInterface
    {
        // Check if $url is a route name or a full URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            // It's a route name, generate the URL
            $url = $this->urlGenerator->generate($url);
        }

        $response = new RedirectResponse($url, $statusCode);
        return new SymfonyHttpResponse($response);
    }

    public function render(string $template, array $parameters = [], int $statusCode = 200): HttpResponseInterface
    {
        $content = $this->twig->render($template, $parameters);
        $response = new Response($content, $statusCode);

        return new SymfonyHttpResponse($response);
    }
}
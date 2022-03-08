<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ActiveLinkExtension
 * 
 * ActiveLinkExtension handles active links.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class ActiveLinkExtension extends AbstractExtension
{

    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) { }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_link', [$this, 'isActive']),
        ];
    }

    /**
     * Determines if a link is active.
     */
    public function isActive(Request $request, string $routeName, bool $strict = true): string
    {
        $route = $this->urlGenerator->generate($routeName);
        $currentUrl = $request->getPathInfo();

        if ($strict) {
            $isActive = $currentUrl === $route;
        } else {
            $isActive = $route === substr($currentUrl, 0, strlen($route));
        }

        return ($isActive) ? 'active' : '';
    }
}

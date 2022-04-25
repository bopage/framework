<?php

namespace Framework\Renderer;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory
{

    
    /**
     * Permet de construire le twigRenderer qui est renderer utilisé dans la configuration
     *
     * @param  ContainerInterface $containerInterface
     * @return TwigRenderer
     */
    public function __invoke(ContainerInterface $containerInterface): TwigRenderer
    {
        $viewPath = $containerInterface->get('views.path');
        $loader = new FilesystemLoader($viewPath);
        $twig = new Environment($loader);
        if ($containerInterface->has('twig.extensions')) {
            foreach ($containerInterface->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
        return new TwigRenderer($loader, $twig);
    }
}

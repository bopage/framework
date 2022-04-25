<?php

namespace Framework\Renderer;

/**
 * Renderer
 * Permet d'ajouter et rendre les vues
 */
interface RendererInterface
{
   

    /**
     * Permet d'ajouter le chemin qui correspond à une vue
     *
     * @param  string $namespace
     * @param  string $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;
    
    /**
     * render
     * Permet de rendre une vue
     * Le chemin peut être précisé avec des namespace rajoutés via addPath()
     * $this->render('@blog/view')
     * $this->render('view')
     *
     * @param  string $view
     * @param  array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

      /**
     * Permet de rajouter les variables globales à toutes les vues
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function addGlobal(string $key, mixed $value): void;
}

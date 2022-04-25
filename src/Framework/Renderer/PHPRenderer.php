<?php

namespace Framework\Renderer;

/**
 * Renderer
 * Permet d'ajouter et rendre les vues
 */
class PHPRenderer implements RendererInterface
{
    const DEFAUTL_PATH = '__MAIN';

    /**
     * les chemins
     *
     * @var array
     */
    private $paths = [];

    
    /**
     * Les variables accessible dans toutes les vues
     *
     * @var array
     */
    private $globals = [];




    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Permet d'ajouter le chemin qui correspond à une vue
     *
     * @param  string $namespace
     * @param  string $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAUTL_PATH] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }
    
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
    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAUTL_PATH] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        extract($this->globals);
        extract($params);
        $renderer = $this;
        require $path;
        return ob_get_clean();
    }
    
    /**
     * Permet de rajouter les variables globales à toutes les vues
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function addGlobal(string $key, mixed $value): void
    {
        $this->globals[$key] = $value;
    }

    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/')- 1);
    }

    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@'. $namespace, $this->paths[$namespace], $view);
    }
}

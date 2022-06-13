<?php
namespace Framework\Twig;

use Framework\App;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModuleExtension extends AbstractExtension
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('module_enabled', [$this, 'moduleEnabled'])
        ];
    }

    public function moduleEnabled(string $moduleName): bool
    {
        foreach ($this->app->getModules() as $module) {
            if ($module::NAME === $moduleName) {
                return true;
            }
        }

        return false;
    }
}

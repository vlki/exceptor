<?php 
namespace Jp\FrontendBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Definition;
    
/**
* 
*/
class FrontendExtension extends Extension
{
    public function twigLoad($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition("basic_twig_extension")) {
            $definition = new Definition("Jp\FrontendBundle\Extension\BasicTwigExtension");
            $definition->addTag("twig.extension");
            $container->setDefinition("basic_twig_extension", $definition);
        }
    }
    
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/';
    }
    
    public function getNamespace()
    {
        return 'https://www.example.com/symfony/schema/';
    }
    
    public function getAlias()
    {
        return "frontend";
    }
}

<?php 
namespace Jp\FrontendBundle\Extension;

/**
* 
*/
class BasicTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'var_export'    => new \Twig_Filter_Function("var_export"),
        );
    }
    
    public function getName()
    {
        return "basic_twig_extension";
    }
}

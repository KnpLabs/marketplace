<?php

namespace Marketplace\Twig;

class MarketplaceExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'marketplace';
    }

    public function getFilters()
    {
        return array('username' => new \Twig_Filter_Method($this, 'username', array('is_safe' => array('html'))));
    }

    public function username($string)
    {
        $firstname = substr($string, 0, strpos($string, '.'));

        return ucfirst($firstname);
    }
}
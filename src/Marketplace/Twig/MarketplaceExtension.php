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

    public function getFunctions()
    {
        return array('gravatar' => new \Twig_Function_Method($this, 'gravatar'));
    }

    public function gravatar($email, $size = 50, $default = 'mm')
    {
        $hash = md5(strtolower($email));

        return sprintf('http://www.gravatar.com/avatar/'.$hash.'?s='.$size.'&d='.$default);
    }

    public function username($string)
    {
        $firstname = substr($string, 0, strpos($string, '.'));

        return ucfirst($firstname);
    }
}
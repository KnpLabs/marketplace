<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\Mink\Behat\Context\MinkContext;

require_once __DIR__.'/../../../../app/bootstrap.php';

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{

    /**
     * @var Silex\Application
     */
    private $app;

    public function __construct(array $parameters = array())
    {
        parent::__construct(array_merge(array(
            'base_url' => 'http://ideamarketplace.local'
        ), $parameters));

        // We remove the cache file 
        // Todo : find a more dynamic way
        if (file_exists($dbfile = __DIR__.'/../../../../cache/app.db')) {
            unlink($dbfile);
        }
    }

}

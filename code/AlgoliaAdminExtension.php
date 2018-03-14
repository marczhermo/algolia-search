<?php

namespace Marcz\Algolia;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class AlgoliaAdminExtension extends Extension
{
    public function init()
    {
        Requirements::javascript('marczhermo/algolia-search: client/dist/js/bundle.js');
        Requirements::css('marczhermo/algolia-search: client/dist/styles/bundle.css');
    }
}

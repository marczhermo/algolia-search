<?php

namespace Marcz\Algolia\Modifiers;

use SilverStripe\Core\Injector\Injectable;
use Marcz\Search\Client\ModifyFilterable;

class GreaterThan implements ModifyFilterable
{
    use Injectable;

    public function apply($key, $value)
    {
        return sprintf('%s > %s', $key, $value);
    }
}

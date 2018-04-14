<?php

namespace Marcz\Algolia\Modifiers;

interface ModifyFilterable
{
    public function apply($key, $value);
}

<?php

namespace Marcz\Algolia\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataObject;
use Marcz\Algolia\AlgoliaClient;

class Exporter extends Extension
{
    public function updateExport(&$data, &$clientClassName)
    {
        if ($clientClassName === AlgoliaClient::class) {
            //Algolia Free Plan 10KB Limit Per Record
            $nineKB = 1024 * 9; // 1KB for other columns
            if (isset($data['Content']) && strlen($data['Content']) > $nineKB) {
                $data['Content'] = substr($data['Content'], 0, $nineKB);
            }

            $fields = DataObject::getSchema()
                ->databaseFields($data['ClassName'], $aggregate = false);

            foreach ($fields as $column => $fieldType) {
                if (in_array($fieldType, ['DBDatetime', 'Date', 'DBDate'])) {
                    $data[$column] = strtotime($data[$column]);
                }
            }
        }
    }
}

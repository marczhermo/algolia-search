<?php

namespace Marcz\Algolia;

use SilverStripe\Core\Injector\Injectable;
use AlgoliaSearch\Client;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Config\Configurable;
use Symbiote\QueuedJobs\Services\QueuedJobService;
use Marcz\Algolia\Jobs\JsonBulkExport;
use Marcz\Algolia\Jobs\JsonExport;
use SilverStripe\ORM\DataList;
use Marcz\Search\Config;
use Marcz\Search\Client\SearchClientAdaptor;
use Marcz\Algolia\Jobs\DeleteRecord;

class AlgoliaClient implements SearchClientAdaptor
{
    use Injectable, Configurable;

    protected $clientIndex;
    protected $clientAPI;

    private static $batch_length = 100;

    public function createClient()
    {
        if (!$this->clientAPI) {
            $this->clientAPI = new Client(
                Environment::getEnv('SS_ALGOLIA_APP_NAME'),
                Environment::getEnv('SS_ALGOLIA_SEARCH_KEY')
            );
        }

        return $this->clientAPI;
    }

    public function initIndex($indexName)
    {
        $client = $this->createClient();

        $this->clientIndex = $client->initIndex($indexName);

        return $this->clientIndex;
    }

    public function createIndex($indexName)
    {
        $index = $this->initIndex($indexName);

        // Set the default ranking
        $index->setSettings([
            'ranking' => [
                'typo',
                'geo',
                'words',
                'filters',
                'proximity',
                'attribute',
                'exact',
                'custom'
            ]
        ]);

        return $index;
    }

    public function update($data)
    {
        $this->clientIndex->saveObject($data, 'ID');
    }

    public function bulkUpdate($list)
    {
        $this->clientIndex->saveObjects($list, 'ID');
    }

    public function deleteRecord($recordID)
    {
        $this->clientIndex->deleteObject($recordID);
    }

    public function createBulkExportJob($indexName, $className)
    {
        $list        = new DataList($className);
        $total       = $list->count();
        $batchLength = self::config()->get('batch_length') ?: Config::config()->get('batch_length');
        $totalPages  = ceil($total / $batchLength);

        $this->initIndex($indexName);

        for ($offset = 0; $offset < $totalPages; $offset++) {
            $job = Injector::inst()->createWithArgs(
                    JsonBulkExport::class,
                    [$indexName, $className, $offset * $batchLength]
                );

            singleton(QueuedJobService::class)->queueJob($job);
        }
    }

    public function createExportJob($indexName, $className, $recordId)
    {
        $job = Injector::inst()->createWithArgs(
                JsonExport::class,
                [$indexName, $className, $recordId]
            );

        singleton(QueuedJobService::class)->queueJob($job);
    }

    public function createDeleteJob($indexName, $className, $recordId)
    {
        $job = Injector::inst()->createWithArgs(
                DeleteRecord::class,
                [$indexName, $className, $recordId]
            );

        singleton(QueuedJobService::class)->queueJob($job);
    }

    public function search($term = '')
    {
        $query = [
            'facetFilters' => ['Brand:Apple'],
        ];

        return $this->clientIndex->search($term, $query);
    }
}

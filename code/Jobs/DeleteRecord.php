<?php

namespace Marcz\Algolia\Jobs;

use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use Symbiote\QueuedJobs\Services\QueuedJob;
use Marcz\Algolia\AlgoliaClient;
use Exception;

class DeleteRecord extends AbstractQueuedJob implements QueuedJob
{
    protected $client;

    /**
     * @param string $className
     * @param int $recordID
     */
    public function __construct($indexName = null, $className = null, $recordID = 0)
    {
        $this->indexName = $indexName;
        $this->className = $className;
        $this->recordID  = (int) $recordID;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Record deletion: "' . $this->className . '" with ID ' . $this->recordID;
    }

    /**
     * @return string
     */
    public function getJobType()
    {
        return QueuedJob::QUEUED;
    }

    public function process()
    {
        if (!$this->indexName) {
            throw new Exception('Missing indexName defined on the constructor');
        }

        if (!$this->className) {
            throw new Exception('Missing className defined on the constructor');
        }

        if (!$this->recordID) {
            throw new Exception('Missing recordID defined on the constructor');
        }

        $client = $this->createClient();
        $client->deleteRecord($this->recordID);

        $this->isComplete = true;
    }

    /**
     * Called when the job is determined to be 'complete'
     * Clean-up object properties
     */
    public function afterComplete()
    {
        $this->indexName = null;
        $this->className = null;
        $this->recordID  = 0;
    }

    public function createClient($client = null)
    {
        if (!$client) {
            $this->client = AlgoliaClient::create();
        }

        $this->client->initIndex($this->indexName);

        return $this->client;
    }
}

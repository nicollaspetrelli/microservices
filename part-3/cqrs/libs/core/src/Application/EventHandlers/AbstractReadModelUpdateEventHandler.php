<?php

namespace Framework\Application\EventHandlers;

use MongoDB\Collection;

abstract class AbstractReadModelUpdateEventHandler
{
    /**
     * @param  Collection  $collection
     */
    public function __construct(
        private Collection $collection
    ) {
    }

    /**
     * @param  string  $id
     * @param  array   $data
     *
     * @return $this
     */
    protected function updateReadModel(string $id, array $data): self
    {
        $this->collection->replaceOne(
            ['_id' => $id],
            $data,
            ['upsert' => true]
        );
        return $this;
    }
}

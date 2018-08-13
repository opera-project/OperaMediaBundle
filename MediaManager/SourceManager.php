<?php

namespace Opera\MediaBundle\MediaManager;

class SourceManager
{
    private $sources = [];

    public function registerSource(Source $source)
    {
        $this->sources[$source->getName()] = $source;
    }

    public function getSources() : ?array
    {
        return $this->sources;
    }

    public function getSource(string $sourceSlug)
    {
        return $this->sources[$sourceSlug];
    }

    public function hasSource(string $sourceSlug)
    {
        return isset($this->sources[$sourceSlug]);
    }

}
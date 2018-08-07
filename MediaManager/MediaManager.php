<?php

namespace Opera\MediaBundle\MediaManager;

class MediaManager
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
}
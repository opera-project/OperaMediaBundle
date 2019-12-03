<?php

namespace Opera\MediaBundle\SearchManager;

use Opera\MediaBundle\MediaManager\Source;
use Opera\MediaBundle\Entity\Folder;

class Search
{
    // string
    private $what = null;

    const SEARCH_WHAT_ALL = "Search Everything";
    const SEARCH_WHAT_ONLY_FOLDER = "Only search Folders";
    const SEARCH_WHAT_ONLY_MEDIA = "Only search Media";

    // string
    private $where = null;

    const SEARCH_WHERE_SOURCE = "In current Source";
    const SEARCH_WHERE_FOLDER = "In current Folder";
    const SEARCH_WHERE_FOLDERS = "In current Folder and sub folders";

    // string query
    private $search = null;

    public function getWhat(): ?string
    {
        return $this->what;
    }

    public function getWhere(): ?string
    {
        return $this->where;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setWhat(string $what)
    {
        $this->what = $what;
    }

    public function setWhere(string $where)
    {
        $this->where = $where;
    }

    public function setSearch(string $search)
    {
        $this->search = $search;
    }
}
<?php

namespace Opera\MediaBundle\Tests\MediaManager;

use Opera\CoreBundle\Tests\TestCase;
use Opera\MediaBundle\MediaManager\Source;
use Gaufrette\Filesystem;
use Opera\MediaBundle\Repository\FolderRepository;
use Opera\MediaBundle\Repository\MediaRepository;
use Opera\MediaBundle\Entity\Media;

class SourceTest extends TestCase
{
    private $source;
    private $filesystem;

    protected function setUp()
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->filesystem->method('has')->will($this->returnValue(true));
        $this->filesystem->method('read')->will($this->returnValue("content"));

        $folderRepository = $this->createMock(FolderRepository::class);
        $mediaRepository = $this->createMock(MediaRepository::class);

        $this->source = new Source($this->filesystem, 'image', $folderRepository, $mediaRepository);
    }

    public function testGetName() {
        $this->assertEquals($this->source->getName(), 'image');
    }

    public function testHas() {
        $media = new Media();

        $this->assertEquals($this->source->has($media), true);

        // $this->source->has($media);
        // $this->filesystem->expects($this->once())
        //     ->method('has')
        //     ->with($media->getPath())
        //     ->willReturn(true);
    }

    public function testRead() {
        $media = new Media();

        $this->assertEquals($this->source->read($media), "content");

        // $this->source->read($media);
        // $this->filesystem->expects($this->once())
        //     ->method('read')
        //     ->with($media->getPath())
        //     ->willReturn(false);
    }

}
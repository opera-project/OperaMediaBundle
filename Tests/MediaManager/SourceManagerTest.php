<?php

namespace Opera\MediaBundle\Tests\MediaManager;

use Opera\CoreBundle\Tests\TestCase;
use Opera\MediaBundle\MediaManager\SourceManager;
use Opera\MediaBundle\MediaManager\Source;

class SourceManagerTest extends TestCase
{
    private $sourceManager;

    protected function setUp()
    {
        $this->sourceManager = new SourceManager();
    }

    public function testSourceManager()
    {
        $stubSourceImage = $this->createMock(Source::class);
        $stubSourceImage->method('getName')->will($this->returnValue('image'));

        $stubSourceOther = $this->createMock(Source::class);
        $stubSourceOther->method('getName')->will($this->returnValue('other'));

        $this->sourceManager->registerSource($stubSourceImage);
        $this->sourceManager->registerSource($stubSourceOther);

        $this->assertEquals(count($this->sourceManager->getSources()), 2);

        $this->assertTrue($this->sourceManager->hasSource('image'));
        $this->assertTrue($this->sourceManager->hasSource('other'));

        $this->assertEquals($this->sourceManager->getSource('image'), $stubSourceImage);
    }

}
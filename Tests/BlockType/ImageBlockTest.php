<?php

namespace Opera\MediaBundle\Tests\BlockType;

use Opera\CoreBundle\Tests\TestCase;
use Opera\MediaBundle\BlockType\ImageBlock;
use Opera\MediaBundle\Repository\MediaRepository;

class ImageBlockTest extends TestCase
{
    public function testGetType()
    {
        $mediaRepository = $this->createMock(MediaRepository::class);

        $blockType = new ImageBlock($mediaRepository);

        $this->assertEquals('image', $blockType->getType());
    }
}
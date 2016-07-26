<?php

namespace ride\library\image\transformation;

use ride\library\image\Image;

use \PHPUnit_Framework_TestCase;

class FlipTransformationTest extends PHPUnit_Framework_TestCase {

    public function testTransform() {
        $mode = Image::MODE_HORIZONTAL;
        $options = array('mode' => $mode);

        $image = $this->getMock(Image::class);
        $image->expects($this->once())
              ->method('flip')
              ->with($this->equalTo($mode))
              ->will($this->returnValue($image));

        $transformation = new FlipTransformation();
        $newImage = $transformation->transform($image, $options);

        $this->assertTrue($newImage instanceof Image);
    }

}

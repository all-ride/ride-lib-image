<?php

namespace ride\library\image\transformation;

use ride\library\image\Image;

use \PHPUnit_Framework_TestCase;

class GrayscaleTransformationTest extends PHPUnit_Framework_TestCase {

    public function testTransform() {
        $image = $this->getMock(Image::class);
        $image->expects($this->once())
              ->method('convertToGrayscale')
              ->will($this->returnValue($image));

        $transformation = new GrayscaleTransformation();
        $newImage = $transformation->transform($image, array());

        $this->assertTrue($newImage instanceof Image);
    }

}

<?php

namespace ride\library\image\transformation;

use ride\library\image\Image;

use \PHPUnit_Framework_TestCase;

class BlurTransformationTest extends PHPUnit_Framework_TestCase {

    public function testTransform() {
        $radius = 15;
        $options = array('radius' => $radius);

        $image = $this->getMockBuilder('ride\\library\\image\\Image')
                      ->getMock();
        $image->expects($this->once())
              ->method('blur')
              ->with($this->equalTo($radius))
              ->will($this->returnValue($image));

        $transformation = new BlurTransformation();
        $newImage = $transformation->transform($image, $options);

        $this->assertTrue($newImage instanceof Image);
    }

}

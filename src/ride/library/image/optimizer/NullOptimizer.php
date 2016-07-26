<?php

namespace ride\library\image\optimizer;

use ride\library\system\file\File;

/**
 * Image optimizer doing nothing
 */
class NullOptimizer implements Optimizer {

    /**
     * Performs a optimization on the provided image file
     * @param \ride\library\system\file\File $file Image file
     * @param array $options Extra options for the transformation
     * @return null
     */
    public function optimize(File $file, array $options = null) {

    }

}

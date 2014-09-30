<?php

namespace ride\library\image\optimizer;

use ride\library\system\exception\SystemException;
use ride\library\system\file\File;
use ride\library\system\System;

/**
 * Generic image optimizer
 */
class GenericOptimizer implements Optimizer {

    /**
     * Instance of the system
     * @var \ride\library\system\System
     */
    protected $system;

    /**
     * Constructs a new image optimizer
     */
    public function __construct(System $system) {
        $this->system = $system;
    }

    /**
     * Performs a optimization on the provided image file
     * @param \ride\library\system\file\File $file Image file
     * @param array $options Extra options for the transformation
     * @return null
     */
    public function optimize(File $file, array $options = null) {
        $extension = $file->getExtension();
        $file = $file->getAbsolutePath();

        switch ($extension) {
            case 'png':
                $this->executeCommand('pngcrush -nofilecheck -rem alla -bail -blacken -reduce -ow ' . $file);
                $this->executeCommand('optipng -o6 ' . $file);

                break;
            case 'jpg':
            case 'jpeg':
                $this->executeCommand('jpegoptim --quiet --strip-all --max=100 ' . $file);
                $this->executeCommand('jpegtran -optimize -copy none ' . $file);

                break;
            case 'gif':
                $this->executeCommand('gifsicle -O3 --careful --no-warnings ' . $file);

                break;
        }
    }

    /**
     * Executes a command and ignore exception when the command does not exist
     * @param string $command
     * @return null
     */
    protected function executeCommand($command) {
        try {
            $this->system->execute($command);
        } catch (SystemException $exception) {

        }
    }

}

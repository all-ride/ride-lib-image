<?php

namespace ride\library\image\io;

use ride\library\image\exception\ImageException;
use ride\library\system\file\File;

/**
 * Abstract image input output
 */
abstract class AbstractImageIO implements ImageIO {

    /**
     * Checks if transparency is supported by the image format
     * @return boolean
     */
    public function supportsTransparancy() {
        return false;
    }

    /**
     * Checks if alpha channel is supported by the image format
     * @return boolean
    */
    public function supportsAlphaChannel() {
        return false;
    }

    /**
     * Check if the read action is valid and supported by this ImageIO.
     * @param ride\library\system\file\File $file File to check
     * @return null
     * @throws ride\library\image\exception\ImageException when the image is
     * not readable
     */
    protected function checkIfReadIsPossible(File $file) {
        $this->checkIfFileIsSupported($file);

        if (!$file->isReadable()) {
            throw new ImageException('Could not read ' . $file . ': file not readable');
        }
    }

    /**
     * Check if the write action is valid and supported by this ImageIO.
     * @param ride\library\system\file\File $file File to check
     * @param resource $resource Internal image resource to write
     * @return null
     * @throws ride\library\image\exception\ImageException when the image is
     * not writable
     */
    protected function checkIfWriteIsPossible(File $file, $resource) {
        $this->checkIfFileIsSupported($file);

        if (!$file->isWritable()) {
            throw new ImageException('Could not write ' . $file . ': file not writable');
        }

        if ($resource == null) {
            throw new ImageException('Could not write ' . $file . ': image resource is null');
        }
    }

    /**
     * Check if a file is supported by this ImageIO. This check is based on the
     * extension of the file.
     * @param ride\library\system\file\File $file File to check
     * @return boolean
     * @throws ride\library\image\exception\ImageException when the image is
     * not supported
     */
    protected function checkIfFileIsSupported(File $file) {
        if ($file->getExtension() && !$file->hasExtension($this->getExtensions())) {
            throw new ImageException('Could not process ' . $file . ': format not supported');
        }

        return true;
    }

}

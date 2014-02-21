<?php

namespace ride\library\image\color;

/**
 * Interface for a HTML color
 */
interface HtmlColor extends Color {

    /**
     * Gets this color in HTML format
     * @return string
     */
    public function getHtmlColor();

    /**
     * Creates a color object from a HTML color code
     * @param string $htmlColor HTML color code. Accepts the following formats:
     * #AABBCC, AABBCC, #ABC and ABC
     * @return HtmlColor
     */
    public static function createFromHtmlColor($htmlColor);

}
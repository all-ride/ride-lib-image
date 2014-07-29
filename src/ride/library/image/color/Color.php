<?php

namespace ride\library\image\color;

/**
 * Interface for a color
 */
interface Color {

    /**
     * Gets a string representation of this color
     * @return string
     */
    public function __toString();

    /**
     * Gets this color in HTML format
     * @return string Color in HTML format (#FFFFFF)
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

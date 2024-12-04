<?php

if (!function_exists('convert_special_characters')) {
    /**
     * Convert special characters to their URL-encoded equivalents
     *
     * @param string $content The string to convert
     * @return string
     */
    function convert_special_characters(string $content): string
    {
        $converter = new class {
            use \Wacky159\TelenorMM\Support\HasSpecialCharacterConversion;
        };
        return $converter->convertSpecialCharacters($content);
    }
}

<?php

if (!function_exists('convert_special_characters')) {
    /**
     * Convert special characters to their URL-encoded equivalents
     *
     * @param string $content The string to convert
     * @param array<string, string>|null $mapping Optional custom character mapping
     * @return string
     */
    function convert_special_characters(string $content, ?array $mapping = null): string
    {
        $converter = new class {
            use \Wacky159\TelenorMM\Support\HasSpecialCharacterConversion;
        };

        if ($mapping !== null) {
            $converter->setSpecialCharacterMapping($mapping);
        }

        return $converter->convertSpecialCharacters($content);
    }
}

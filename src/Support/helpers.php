<?php

if (!function_exists('convert_special_characters')) {
    /**
     * Create a special character converter instance
     *
     * @param string $content The string to convert
     * @return \Wacky159\TelenorMM\Support\SpecialCharacterConverter
     */
    function convert_special_characters(string $content): \Wacky159\TelenorMM\Support\SpecialCharacterConverter
    {
        return new \Wacky159\TelenorMM\Support\SpecialCharacterConverter($content);
    }
}

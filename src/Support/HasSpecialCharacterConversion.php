<?php

declare(strict_types=1);

namespace Wacky159\TelenorMM\Support;

trait HasSpecialCharacterConversion
{
    public function convertSpecialCharacters(string $content): string
    {
        $specialChars = [
            "\n" => '%0A',   // new line
            ' ' => '%20',    // Space
            '!' => '%21',    // exclamation point
            '"' => '%22',    // double quotes
            '#' => '%23',    // number sign
            '$' => '%24',    // dollar sign
            '%' => '%25',    // percent sign
            '&' => '%26',    // Ampersand
            "'" => '%27',    // single quote
            '(' => '%28',    // opening parenthesis
            ')' => '%29',    // closing parenthesis
            '*' => '%2A',    // Asterisk
            '+' => '%2B',    // plus sign
            ',' => '%2C',    // Comma
            '-' => '%2D',    // minus sign/hyphen
            '.' => '%2E',    // Period
            '/' => '%2F',    // Slash
            ':' => '%3A',    // colon
            ';' => '%3B',    // semicolon
            '<' => '%3C',    // less than sign
            '=' => '%3D',    // equal sign
            '>' => '%3E',    // greater than sign
            '?' => '%3F',    // question mark
            '@' => '%40',    // at symbol
            '[' => '%5B',    // opening bracket
            '\\' => '%5C',   // backslash
            ']' => '%5D',    // closing bracket
            '^' => '%5E',    // caret/circumflex
            '_' => '%5F',    // underscore
            '{' => '%7B',    // opening brace
            '|' => '%7C',    // vertical bar
            '}' => '%7D',    // closing brace
            '~' => '%7E',    // equivalency sign/tilde
        ];

        return strtr($content, $specialChars);
    }
}

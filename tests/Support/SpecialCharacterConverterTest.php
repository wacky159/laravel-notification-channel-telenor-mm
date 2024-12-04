<?php

namespace Wacky159\TelenorMM\Test\Support;

use PHPUnit\Framework\TestCase;
use Wacky159\TelenorMM\Support\SpecialCharacterConverter;

class SpecialCharacterConverterTest extends TestCase
{
    /** @test */
    public function it_can_convert_special_characters()
    {
        $result = convert_special_characters('Hello @ World!')->convert();
        $this->assertEquals('Hello %40 World%21', $result);
    }

    /** @test */
    public function it_can_use_custom_mapping()
    {
        $result = convert_special_characters('Hello @ World!')
            ->map([
                '@' => '[at]',
                ' ' => '_',
                '!' => '[exclaim]'
            ])
            ->convert();

        $this->assertEquals('Hello_[at]_World[exclaim]', $result);
    }

    /** @test */
    public function it_can_be_converted_to_string()
    {
        $result = (string) convert_special_characters('Hello @ World!');
        $this->assertEquals('Hello %40 World%21', $result);
    }

    /** @test */
    public function it_can_handle_multiple_special_characters()
    {
        $result = convert_special_characters('Hello @#$%^&*()')->convert();
        $this->assertEquals('Hello %40%23%24%25%5E%26%2A%28%29', $result);
    }

    /** @test */
    public function it_leaves_normal_characters_unchanged()
    {
        $result = convert_special_characters('HelloWorld')->convert();
        $this->assertEquals('HelloWorld', $result);
    }

    /** @test */
    public function it_can_handle_empty_string()
    {
        $result = convert_special_characters('')->convert();
        $this->assertEquals('', $result);
    }
}

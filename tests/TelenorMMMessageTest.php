<?php

namespace Wacky159\TelenorMM\Test;

use PHPUnit\Framework\TestCase;
use Wacky159\TelenorMM\Enums\MessageType;
use Wacky159\TelenorMM\Enums\ReceiverType;
use Wacky159\TelenorMM\Enums\SenderType;
use Wacky159\TelenorMM\TelenorMMMessage;

class TelenorMMMessageTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $message = new TelenorMMMessage();
        $this->assertInstanceOf(TelenorMMMessage::class, $message);
    }

    /** @test */
    public function it_can_set_content()
    {
        $message = (new TelenorMMMessage())
            ->content('Test message');

        $array = $message->toArray();
        $this->assertEquals('Test message', $array['content']);
    }

    /** @test */
    public function it_can_set_type()
    {
        $message = (new TelenorMMMessage())
            ->type(MessageType::TEXT);

        $array = $message->toArray();
        $this->assertEquals(MessageType::TEXT->value, $array['type']);
    }

    /** @test */
    public function it_can_set_sender()
    {
        $message = (new TelenorMMMessage())
            ->sender('TestSender', SenderType::ALPHANUMERIC->value);

        $array = $message->toArray();
        $this->assertEquals('TestSender', $array['sender']['name']);
        $this->assertEquals((string) SenderType::ALPHANUMERIC->value, $array['sender']['@type']);
    }

    /** @test */
    public function it_can_set_receiver()
    {
        $message = (new TelenorMMMessage())
            ->receiver('1234567890', ReceiverType::INTERNATIONAL->value);

        $array = $message->toArray();
        $this->assertEquals('1234567890', $array['receiver'][0]['phoneNumber']);
        $this->assertEquals((string) ReceiverType::INTERNATIONAL->value, $array['receiver'][0]['@type']);
    }

    /** @test */
    public function it_can_set_multiple_receivers()
    {
        $message = (new TelenorMMMessage())
            ->receiver('1234567890')
            ->receiver('0987654321');

        $array = $message->toArray();
        $this->assertCount(2, $array['receiver']);
        $this->assertEquals('1234567890', $array['receiver'][0]['phoneNumber']);
        $this->assertEquals('0987654321', $array['receiver'][1]['phoneNumber']);
    }

    /** @test */
    public function it_can_disable_special_character_encoding()
    {
        $content = 'Hello @ World!';
        $message = (new TelenorMMMessage())
            ->content($content, false);

        $array = $message->toArray();
        $this->assertEquals($content, $array['content']);
    }

    /** @test */
    public function it_encodes_special_characters_by_default()
    {
        $message = (new TelenorMMMessage())
            ->content('Hello @ World!');

        $array = $message->toArray();
        $this->assertEquals('Hello %40 World%21', $array['content']);
    }
}

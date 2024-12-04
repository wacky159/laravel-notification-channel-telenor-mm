<?php

namespace Wacky159\TelenorMM\Test;

use Mockery;
use Illuminate\Notifications\Notification;
use Wacky159\TelenorMM\TelenorMMChannel;
use Wacky159\TelenorMM\TelenorMMMessage;
use Wacky159\TelenorMM\TelenorMMService;

class TelenorMMChannelTest extends TestCase
{
    protected $service;
    protected $channel;
    protected $notification;
    protected $notifiable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = Mockery::mock(TelenorMMService::class);
        $this->channel = new TelenorMMChannel($this->service);
        $this->notification = Mockery::mock(Notification::class);
        $this->notifiable = new class {
            public function routeNotificationFor($channel)
            {
                return '1234567890';
            }
        };
    }

    /** @test */
    public function it_can_send_notification()
    {
        $message = (new TelenorMMMessage())
            ->content('Test message')
            ->receiver('1234567890');

        $this->notification->shouldReceive('toTelenorMM')
            ->with($this->notifiable)
            ->andReturn($message);

        $this->service->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($argument) {
                return $argument['content'] === 'Test message' &&
                    $argument['receiver'][0]['phoneNumber'] === '1234567890';
            }))
            ->andReturn(['status' => 'success']);

        $this->channel->send($this->notifiable, $this->notification);
    }

    /** @test */
    public function it_does_not_send_notification_when_to_telenor_mm_returns_null()
    {
        $this->notification->shouldReceive('toTelenorMM')
            ->with($this->notifiable)
            ->andReturn(null);

        $this->service->shouldNotReceive('send');

        $this->channel->send($this->notifiable, $this->notification);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

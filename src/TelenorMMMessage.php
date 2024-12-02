<?php

declare(strict_types=1);

namespace Wacky159\TelenorMM;

use Wacky159\TelenorMM\Enums\MessageType;
use Wacky159\TelenorMM\Enums\ReceiverType;
use Wacky159\TelenorMM\Enums\SenderType;

class TelenorMMMessage
{
    /** @var MessageType */
    protected $type = MessageType::TEXT->value;

    /** @var string */
    protected $content;

    /** @var string */
    protected $sendTime;

    /** @var array */
    protected $characteristic = [];

    /** @var array */
    protected $sender = [];

    /** @var array */
    protected $receiver = [];

    /** @var array */
    protected $customParameters = [];

    public function __construct() {}

    public function content($content)
    {
        $this->content = $content;
        return $this;
    }

    public function type(MessageType|string $type): self
    {
        $this->type = is_string($type) ? MessageType::from($type)->value : $type;
        return $this;
    }

    public function sendTime(string $sendTime = null): self
    {
        $this->sendTime = $sendTime ?? now()->format('Y-m-d\TH:i:sP');
        return $this;
    }

    public function sender(string $name, ?int $type = SenderType::ALPHANUMERIC->value): self
    {
        $this->sender['name'] = $name;
        $this->sender['@type'] = $type;
        return $this;
    }

    public function receiver(string $phoneNumber, ?int $type = ReceiverType::INTERNATIONAL->value): self
    {
        $this->receiver[] = [
            '@type' => $type,
            'phoneNumber' => $phoneNumber
        ];
        return $this;
    }

    public function characteristic(string $name, string $value): self
    {
        $this->characteristic[] = [
            'name' => $name,
            'value' => $value
        ];
        return $this;
    }

    public function addParameter(string $key, mixed $value): self
    {
        $this->customParameters[$key] = $value;
        return $this;
    }

    public function validate()
    {
        if (empty($this->type)) {
            throw new \InvalidArgumentException('Message type cannot be empty');
        }

        if (empty($this->content)) {
            throw new \InvalidArgumentException('Message content cannot be empty');
        }

        $requiredCharacteristics = ['UserName', 'Password'];
        $existingCharacteristics = array_column($this->characteristic, 'name');

        foreach ($requiredCharacteristics as $required) {
            if (!in_array($required, $existingCharacteristics)) {
                throw new \InvalidArgumentException("$required characteristic is required");
            }
        }

        if (empty($this->sender['@type'])) {
            throw new \InvalidArgumentException('Sender type cannot be empty');
        }

        if (empty($this->sender['name'])) {
            throw new \InvalidArgumentException('Sender name cannot be empty');
        }

        if (count($this->receiver) == 0) {
            throw new \InvalidArgumentException('Receiver cannot be empty');
        }

        foreach ($this->receiver as $receiver) {
            if (empty($receiver['@type']) && $receiver['@type'] !== ReceiverType::UNKNOWN->value) {
                throw new \InvalidArgumentException('Receiver type cannot be empty');
            }

            if (empty($receiver['phoneNumber'])) {
                throw new \InvalidArgumentException('Receiver phone number cannot be empty');
            }
        }

        return true;
    }

    public function toArray()
    {
        $this->validate();

        return array_filter(array_merge([
            'type' => $this->type,
            'content' => $this->content,
            'sendTime' => $this->sendTime,
            'characteristic' => !empty($this->characteristic) ? $this->characteristic : null,
            'sender' => $this->sender,
            'receiver' => $this->receiver
        ], $this->customParameters));
    }
}

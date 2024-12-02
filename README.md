# Laravel Telenor MM Notification Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/telenor-mm.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/telenor-mm)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/telenor-mm.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/telenor-mm)

This package makes it easy to send notifications via [Telenor MM](https://www.linkedin.com/company/telenor-group/) (now known as ATOM in Myanmar) with Laravel 10.x.

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Available Methods](#available-methods)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Installation

You can install this package via Composer:

```bash
composer require wacky159/laravel-notification-channel-telenor-mm
```

## Configuration

### Publish Configuration

```bash
php artisan vendor:publish --provider="Wacky159\TelenorMM\TelenorMMServiceProvider"
```

### Environment Variables

Add the following to your `.env` file:

```env
TELENOR_MM_CLIENT_ID=your-client-id
TELENOR_MM_CLIENT_SECRET=your-client-secret
TELENOR_MM_API_URL=https://prod-apigw.atom.com.mm  # ATOM (formerly Telenor MM) API endpoint in Myanmar
TELENOR_MM_CALLBACK_URL=your-callback-url

# Optional Settings
TELENOR_MM_TOKEN_TTL=3600
TELENOR_MM_AUTH_CODE_TTL=86400
TELENOR_MM_MAX_RETRY=3
TELENOR_MM_RETRY_DELAY=1
TELENOR_MM_LOG_ENABLED=false
TELENOR_MM_LOG_CHANNEL=stack
```

## Usage

### Create a Notification

```php
use Wacky159\TelenorMM\TelenorMMChannel;
use Wacky159\TelenorMM\TelenorMMMessage;
use Wacky159\TelenorMM\Enums\MessageType;

class InvoicePaid extends Notification
{
    public function via($notifiable)
    {
        return [TelenorMMChannel::class];
    }

    public function toTelenorMM($notifiable)
    {
        return (new TelenorMMMessage)
            ->content('Your invoice has been paid!')
            ->type(MessageType::TEXT)
            ->characteristic('UserName', 'your-username')
            ->characteristic('Password', 'your-password')
            ->sender('YourApp')
            ->receiver($notifiable->phone);
    }
}
```

### Required Fields

When creating a TelenorMMMessage, the following fields are required:

1. `content`: Message content
2. `type`: Message type
3. `characteristic`: Must include these two characteristics
   - `UserName`: Username
   - `Password`: Password
4. `sender`: 
   - `name`: Sender name
   - `@type`: Sender type
5. `receiver`: At least one receiver, and each receiver must include
   - `phoneNumber`: Receiver phone number
   - `@type`: Receiver type

### Send Notification

```php
$user->notify(new InvoicePaid());
```

## Available Methods

### TelenorMMMessage

- `content($message)`: Set message content
- `type($type)`: Set message type (TEXT, BINARY, MULTILINGUAL, FLASH)
- `sender($name, $type = 5)`: Set sender name and type
- `receiver($phoneNumber, $type = 1)`: Set receiver phone number and type
  - `$type = 0`: Unknown type
  - `$type = 1`: International number (default)
  - `$type = 2`: National number
  - `$type = 3`: Network specific number
  - `$type = 4`: Subscriber number
  - `$type = 5`: Alphanumeric
  - `$type = 6`: Short code

### Receiver Type Examples

```php
use Wacky159\TelenorMM\Enums\ReceiverType;

// Use international number as receiver (default)
$message->receiver('+886912345678', ReceiverType::INTERNATIONAL);

// Use national number as receiver
$message->receiver('0912345678', ReceiverType::NATIONAL);

// Use subscriber number as receiver
$message->receiver('SUB123', ReceiverType::SUBSCRIBER_NUMBER);

// Can add multiple receivers
$message
    ->receiver('+886912345678', ReceiverType::INTERNATIONAL)
    ->receiver('0912345678', ReceiverType::NATIONAL);
```

### Message Types

Use MessageType enum to specify message type:

```php
use Wacky159\TelenorMM\Enums\MessageType;

$message->type(MessageType::TEXT);    // Text message
$message->type(MessageType::BINARY);  // Binary message
$message->type(MessageType::MULTILINGUAL); // Multilingual message
$message->type(MessageType::FLASH);   // Flash message
```

### Debug Logging

Enable debug logging:

```env
TELENOR_MM_LOG_ENABLED=true
TELENOR_MM_LOG_CHANNEL=daily
```

## Testing

```bash
composer test
```

## Security

If you discover any security-related issues, please email [your-email@example.com](mailto:your-email@example.com) instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

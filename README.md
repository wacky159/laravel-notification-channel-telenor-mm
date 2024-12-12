# Laravel Telenor MM Notification Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wacky159/laravel-notification-channel-telenor-mm.svg?style=flat-square)](https://packagist.org/packages/wacky159/laravel-notification-channel-telenor-mm)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/wacky159/laravel-notification-channel-telenor-mm.svg?style=flat-square)](https://packagist.org/packages/wacky159/laravel-notification-channel-telenor-mm)

This package makes it easy to send notifications via [Telenor MM](https://www.linkedin.com/company/telenor-group/) (now known as ATOM in Myanmar) with Laravel 10.x.

## Contents

-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Usage](#usage)
-   [Available Methods](#available-methods)
-   [Special Character Conversion](#special-character-conversion)
-   [Testing](#testing)
-   [Security](#security)
-   [Contributing](#contributing)
-   [License](#license)

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

### Callback Setup

You need to implement a callback endpoint to handle the authorization response from Telenor MM. Here's an example:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Wacky159\TelenorMM\Traits\HandleTelenorAuthorization;

class TelenorAuthController extends Controller
{
    use HandleTelenorAuthorization;

    public function callback(Request $request)
    {
        return $this->handleTelenorCallback($request);
    }
}
```

Then add the route in your `routes/web.php`:

```php
Route::get('telenor/callback', [TelenorAuthController::class, 'callback'])->name('telenor.callback');
```

Make sure to set the `TELENOR_MM_CALLBACK_URL` in your `.env` file to match this route:

```env
TELENOR_MM_CALLBACK_URL=https://your-domain.com/telenor/callback
```

## Usage

### Create a Notification

```php

class TestNotification extends Notification
{
    public function toTelenorMM($notifiable): ?TelenorMMMessage
    {
        // Get phone number from routes array (for Notification::route usage)
        // or from the notifiable model
        $phoneNumber = $notifiable->routes['telenorMM']
            ?? $notifiable->routeNotificationForTelenorMM();

        // Remove '+' prefix from phone number
        $phoneNumber = ltrim($phoneNumber, '+');

        if (empty($phoneNumber)) {
            return null;
        }

        return (new TelenorMMMessage())
            ->content('Hello World!')
            ->type(MessageType::TEXT)
            ->sender('YourApp', SenderType::ALPHANUMERIC->value)
            ->characteristic('UserName', 'your-username')
            ->characteristic('Password', 'your-password')
            ->receiver($phoneNumber, ReceiverType::INTERNATIONAL->value);
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
$user->notify(new TestNotification());

\Illuminate\Support\Facades\Notification::route('telenorMM', 'customer-phone-number')->notify(new TestNotification);
```

## Available Methods

### TelenorMMMessage

-   `content($message, $encode = true)`: Set message content. The second parameter `$encode` is optional and defaults to `true`. If set to `false`, special characters will not be automatically encoded.
-   `type($type)`: Set message type (TEXT, BINARY, MULTILINGUAL, FLASH)
-   `sender($name, $type = 5)`: Set sender name and type
-   `receiver($phoneNumber, $type = 1)`: Set receiver phone number and type
    -   `$type = 0`: Unknown type
    -   `$type = 1`: International number (default)
    -   `$type = 2`: National number
    -   `$type = 3`: Network specific number
    -   `$type = 4`: Subscriber number
    -   `$type = 5`: Alphanumeric
    -   `$type = 6`: Short code

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

## Special Character Conversion

This package provides a helper function to handle special character conversion for SMS messages. You can use it in several ways:

### Basic Usage

```php
// Basic conversion with default character mapping
$converted = convert_special_characters('Hello World!')->convert();

// Using string casting
$converted = (string) convert_special_characters('Hello World!');
```

### Custom Character Mapping

You can customize the character mapping using the fluent interface:

```php
// Custom mapping for specific characters
$converted = convert_special_characters('Hello World!')
    ->map([
        ' ' => '_',
        '!' => '-EXCLAIM-'
    ])
    ->convert();
```

### Using in Your Notification

```php
public function toTelenorMM($notifiable)
{
    return TelenorMMMessage::create()
        ->content('Hello World! Special chars: @#$%')
        ->type(MessageType::TEXT);
}
```

### Extending the Converter

You can also extend the conversion logic by creating your own converter class:

```php
use Wacky159\TelenorMM\Support\HasSpecialCharacterConversion;

class MyCustomConverter
{
    use HasSpecialCharacterConversion;

    public function __construct()
    {
        // Define your custom mapping
        $this->setSpecialCharacterMapping([
            '@' => '[at]',
            '#' => '[hash]'
        ]);
    }

    // Optionally override the conversion logic
    public function convertSpecialCharacters(string $content): string
    {
        // Your custom conversion logic here
        return parent::convertSpecialCharacters($content);
    }
}
```

### Automatic Encoding in TelenorMMMessage

By default, `TelenorMMMessage::content()` will automatically encode special characters for TEXT type messages. You can disable this behavior by passing `false` as the second parameter: `->content($message, false)`.

```php
public function toTelenorMM($notifiable)
{
    return TelenorMMMessage::create()
        // Default behavior: automatically encode special characters
        ->content('Hello World! Special chars: @#$%')
        // Or disable automatic encoding
        ->content('Hello World! Special chars: @#$%', false)
        ->type(MessageType::TEXT);
}
```

## Testing

```bash
composer test
```

## Security

If you discover any security-related issues, please email [wacky159@gmail.com](mailto:wacky159@gmail.com) instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

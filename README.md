# FluxChat

A beautiful Laravel Livewire chat component built with Flux UI, supporting both standard polling and real-time messaging with Laravel Reverb.

![FluxChat Preview](https://github.com/user-attachments/assets/3fc916f-902e-35b-b192-952e35b14568)

## ✨ Features

- 🎨 **Beautiful UI** - Built with Flux UI components
- ⚡ **Real-time Support** - Optional Laravel Reverb integration
- 🔄 **Fallback Polling** - Works without WebSocket server
- 🌍 **Multi-language** - English and Norwegian included
- 📱 **Responsive Design** - Works on all devices
- 🔧 **Highly Configurable** - Customize everything
- 🚀 **Easy Installation** - One command setup

## 📋 Requirements

- PHP 8.3+
- Laravel 12.0+
- Livewire 3.0+
- Flux UI Pro 2.0+

## 🚀 Installation

Install via Composer:

```bash
composer require wirelabs/fluxchat
```

Run the installation command:

```bash
php artisan fluxchat:install
```

Run migrations:

```bash
php artisan migrate
```

## 🎯 Basic Usage

Add the component to your Blade view:

```blade
<livewire:fluxchat :contacts="$contacts" />
```

Where `$contacts` is a collection of users/contacts:

```php
// In your controller
$contacts = User::where('id', '!=', auth()->id())->get();

return view('chat', compact('contacts'));
```

## ⚙️ Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="fluxchat-config"
```

### Basic Configuration

```php
// config/fluxchat.php

return [
    'realtime' => [
        'enabled' => env('FLUXCHAT_REALTIME_ENABLED', false),
        'auto_refresh_interval' => env('FLUXCHAT_AUTO_REFRESH', 5), // seconds
    ],
    
    'ui' => [
        'theme' => env('FLUXCHAT_THEME', 'dark'),
        'avatar_size' => env('FLUXCHAT_AVATAR_SIZE', 'sm'),
    ],
];
```

### Environment Variables

Add to your `.env` file:

```env
# Standard mode (polling every 5 seconds)
FLUXCHAT_REALTIME_ENABLED=false
FLUXCHAT_AUTO_REFRESH=5

# Real-time mode (requires Reverb)
FLUXCHAT_REALTIME_ENABLED=true
BROADCAST_CONNECTION=reverb
```

## 🔥 Real-time Messaging

FluxChat supports two modes:

### 1. Standard Mode (Default)
- Polls for new messages every 5 seconds
- No additional server required
- Works everywhere

### 2. Real-time Mode
- Instant message delivery via WebSockets
- Requires Laravel Reverb
- Better user experience

To enable real-time messaging:

1. Install and configure Laravel Reverb:
```bash
php artisan install:broadcasting
```

2. Update your `.env`:
```env
FLUXCHAT_REALTIME_ENABLED=true
BROADCAST_CONNECTION=reverb
```

3. Start the Reverb server:
```bash
php artisan reverb:start
```

## 🎨 Customization

### Custom Contact Model

```blade
<livewire:fluxchat 
    :contacts="$contacts"
    contact-model="App\Models\Contact"
    contact-name-field="full_name"
    :contact-search-fields="['name', 'email']"
/>
```

### Custom Styling

Publish the views to customize:

```bash
php artisan vendor:publish --tag="fluxchat-views"
```

Views will be published to `resources/views/vendor/fluxchat/`.

### Language Customization

Publish language files:

```bash
php artisan vendor:publish --tag="fluxchat-lang"
```

Add your own translations in `resources/lang/vendor/fluxchat/`.

## 📚 Advanced Usage

### Programmatic Control

```php
// In your Livewire component
use Wirelabs\FluxChat\Models\Conversation;
use Wirelabs\FluxChat\Models\Message;

// Create a conversation
$conversation = Conversation::create([
    'type' => 'private',
    'is_group' => false,
]);

// Add participants
$conversation->addParticipant(auth()->user());
$conversation->addParticipant($contact);

// Send a message
$message = $conversation->messages()->create([
    'sendable_id' => auth()->id(),
    'sendable_type' => User::class,
    'body' => 'Hello!',
    'type' => 'text',
]);
```

### Events

Listen to FluxChat events:

```php
// EventServiceProvider
use Wirelabs\FluxChat\Events\MessageSent;

protected $listen = [
    MessageSent::class => [
        SendMessageNotification::class,
    ],
];
```

## 🧪 Testing

```bash
composer test
```

## 📖 API Reference

### Component Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `contacts` | Collection/Array | `[]` | Available contacts |
| `contactModel` | String | `User::class` | Contact model class |
| `contactNameField` | String | `'name'` | Contact name field |
| `contactSearchFields` | Array | `['name']` | Searchable fields |
| `maxContacts` | Integer | `10` | Max contacts to show |

### Models

#### Conversation
- `messages()` - Get all messages
- `participants()` - Get all participants
- `addParticipant($user)` - Add participant
- `markAsRead($user)` - Mark as read

#### Message
- `conversation()` - Get conversation
- `sendable()` - Get sender
- `isEdited()` - Check if edited

## 🛠️ Troubleshooting

### Real-time not working

1. Check Reverb is running:
```bash
php artisan reverb:start --debug
```

2. Verify configuration:
```bash
php artisan config:show broadcasting.default
```

3. Check browser console for WebSocket connections

### Messages not updating

1. Ensure auto-refresh is enabled:
```env
FLUXCHAT_AUTO_REFRESH=5
```

2. Check Livewire is working:
```blade
@livewireScripts
```

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🙏 Credits

- Built by [Wirelabs](https://insidenext.no)
- Powered by [Laravel](https://laravel.com)
- UI by [Flux UI](https://fluxui.dev)
- Real-time by [Laravel Reverb](https://laravel.com/docs/broadcasting)

---

**Made with ❤️ by Wirelabs**

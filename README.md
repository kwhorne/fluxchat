# FluxChat

A modern Laravel Livewire messaging app built with Flux UI for teams with private chats and group conversations.

## Features

- 🚀 **Modern UI** - Built with Livewire Flux v2 and Tailwind CSS v4
- 💬 **Real-time messaging** - WebSocket support via Laravel Reverb
- 👥 **Group conversations** - Create and manage team discussions
- 🔒 **Private chats** - Secure one-on-one conversations
- 🌐 **Multi-language support** - English and Norwegian (Bokmål) included
- 📱 **Responsive design** - Works on desktop and mobile devices
- 🎨 **Customizable** - Publish and modify views and translations

## Requirements

- PHP 8.1+
- Laravel 10.0, 11.0, or 12.0
- Livewire 3.2.3+
- Livewire Flux 2.0+
- Node.js & NPM (for asset compilation)

## Installation

### 1. Install the Package

```bash
composer require kwhorne/fluxchat
```

### 2. Run the Installation Command

```bash
php artisan fluxchat:install
```

**The following actions will be executed:**
- Publish configuration file
- Publish migration files
- Create a storage symlink

### 3. Enable UUIDs (Optional)

If you want FluxChat to use UUIDs instead of auto-incrementing integers for conversations, update the config before running migrations:

```php
// config/fluxchat.php
'uuids' => true,
```

### 4. Run Migrations

```bash
php artisan migrate
```

## Configuration

### Step 1: Enable Broadcasting

In newer Laravel installations, broadcasting is disabled by default. To enable it, run:

```bash
php artisan install:broadcasting
```

> **Note:** This command will prompt you to install Laravel Reverb and necessary front-end packages such as Echo. Accept if you don't yet have a WebSocket server set up.

Then, start your Reverb server:

```bash
php artisan reverb:start
```

For more details, refer to the [Laravel Broadcasting Documentation](https://laravel.com/docs/broadcasting), including information on integrating Laravel Echo for real-time updates.

### Step 2: Start Your Queue Worker

After configuring broadcasting, start a queue worker to handle message broadcasting and other queued tasks:

```bash
php artisan queue:work --queue=messages,default
```

> **Queue Prioritization:** The `messages` queue is prioritized to ensure real-time message delivery.

### Step 3: Start Development Server

To start your development server, run:

```bash
composer run dev
```

If you're not running the latest Laravel version, you can run these commands separately:

```bash
php artisan serve
npm run dev
```

## Customization

### Publishing Translations

If you need to customize the language files, you can publish them using:

```bash
php artisan vendor:publish --tag=fluxchat-translations
```

Available languages:
- English (`en`)
- Norwegian Bokmål (`nb`)

### Publishing Views

To modify FluxChat's Blade views, publish them with:

```bash
php artisan vendor:publish --tag=fluxchat-views
```

### Publishing Configuration

To customize the configuration, publish the config file:

```bash
php artisan vendor:publish --tag=fluxchat-config
```

## Usage

### Adding FluxChat to Your Layout

Add FluxChat to your Blade layout:

```blade
<!-- In your layout file -->
@fluxchatAssets
@fluxchatStyles
```

### Using the Chat Component

You can use FluxChat in your Blade views:

```blade
<livewire:fluxchat />
```

### Page Components

FluxChat provides dedicated page components:

```blade
<!-- Chat list page -->
<livewire:fluxchat.pages.index />

<!-- Individual chat page -->
<livewire:fluxchat.pages.view :conversation="$conversation" />
```

## Flux UI Integration

FluxChat is built with Livewire Flux v2, providing:

- **Modern Components** - Beautiful, accessible UI components
- **Dark Mode Support** - Automatic dark/light mode switching
- **Inter Font** - Clean, modern typography
- **Tailwind CSS v4** - Latest CSS framework features

## Development

### Running Tests

```bash
composer test
```

### Code Quality

```bash
composer analyse
composer format
```

### Building Assets

```bash
npm run build
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Support

For support, please open an issue on the [GitHub repository](https://github.com/kwhorne/fluxchat).

---

**Made with ❤️ by [Knut W. Horne](https://kwhorne.com)**

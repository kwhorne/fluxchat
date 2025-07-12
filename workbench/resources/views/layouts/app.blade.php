<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Inter font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS with Flux -->
    @vite(['resources/css/app.css'])

    @fluxAppearance
    <livewire:styles />
    @fluxchatStyles
</head>
<body>
    {{ $slot }}

    <livewire:scripts />
    @fluxScripts
    @fluxchatAssets

</body>
</html>
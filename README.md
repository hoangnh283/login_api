add to file config/services.php
'telegram' => [
    'bot' => env('TELEGRAM_BOT_NAME'),  // The bot's username
    'client_id' => null,
    'client_secret' => env('TELEGRAM_TOKEN'),
    'redirect' => env('TELEGRAM_REDIRECT_URI'),
],

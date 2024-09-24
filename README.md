## Configuration

First of all, you must create a bot by contacting [@BotFather](http://t.me/BotFather) (https://core.telegram.org/bots#6-botfather)

> Don't forget to set your website URL using `/setdomain`

Then, you need to add your bot's configuration to `config/services.php`. The bot username is required, `client_id` must be `null`. The provider will also ask permission for the bot to write to the user.

```php
'telegram' => [
    'bot' => env('TELEGRAM_BOT_NAME'),  // The bot's username
    'client_id' => null,
    'client_secret' => env('TELEGRAM_TOKEN'),
    'redirect' => env('TELEGRAM_REDIRECT_URI'),
],
```

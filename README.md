# ivbot

A VKontakte (VK) group chat bot written in PHP.

## Description

ivbot is a versatile chatbot designed for VKontakte group conversations. It provides schedule management, custom command storage, and various utility features to enhance group communication. The bot is particularly useful for educational institutions, student groups, or any community that needs to share schedules and maintain custom commands.

## Features

1. **Schedule Display** - Show daily schedules for current or next day
2. **Week Type Detection** - Determine odd/even weeks (useful for alternating schedules)
3. **Custom Commands** - Store and retrieve custom commands with text or images
4. **Command Management** - Add, update, and delete custom commands
5. **Help System** - Built-in help documentation for users
6. **Image Support** - Handle both text and image-based commands

## Requirements

- PHP 7.0 or higher
- cURL extension enabled
- Web server (Apache/Nginx)
- VKontakte API access token
- VKontakte Callback API setup

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/nayutalienx/ivbot.git
   cd ivbot
   ```

2. Configure the bot by editing `bot.php`:
   ```php
   define('CALLBACK_API_CONFIRMATION_TOKEN', 'your_confirmation_token');
   define('VK_API_ACCESS_TOKEN', 'your_access_token');
   ```

3. Set up your web server to serve the `bot.php` file

4. Configure VKontakte Callback API to point to your bot's URL

5. Add schedule images to the `images/` directory:
   - `Monday.png`
   - `Tuesday.png` 
   - `Wednesday.png`
   - `Thursday.png`
   - `Friday.png`
   - `Saturday.png`

## Commands

### Built-in Commands

| Command | Alternative | Description |
|---------|-------------|-------------|
| `!help` | `!хелп` | Show help message with available commands |
| `!р` | `!расписание` | Show today's schedule |
| `!р !з` | `!р !завтра` | Show tomorrow's schedule |
| `!w` | `!week` | Show current week type (odd/even) |
| `!добавить !command_name 'text'` | - | Add a new text command |
| `!добавить !command_name` + image | - | Add a new image command |
| `!удалить !command_name` | - | Delete a command |

### Custom Commands

Users can create custom commands that start with `!`. These can contain either:
- **Text responses**: Store any text that will be sent when the command is used
- **Image responses**: Store images that will be sent when the command is used

### Command Syntax

- **Add text command**: `!добавить !mycommand 'This is my response text'`
- **Add image command**: `!добавить !mycommand` (attach an image to the message)
- **Use command**: `!mycommand`
- **Delete command**: `!удалить !mycommand`

## File Structure

```
ivbot/
├── bot.php              # Main bot script
├── README.md            # This documentation
├── 1.png               # Example screenshot
├── commands/           # Directory for custom command storage
│   └── list.txt        # List of available custom commands
└── images/             # Directory for images
    ├── Monday.png      # Monday schedule
    ├── Tuesday.png     # Tuesday schedule
    ├── Wednesday.png   # Wednesday schedule
    ├── Thursday.png    # Thursday schedule
    ├── Friday.png      # Friday schedule
    ├── Saturday.png    # Saturday schedule
    └── image_*.png     # Custom command images
```

## Configuration

### VKontakte API Setup

1. Create a VKontakte community (group)
2. Go to community settings → API usage → Callback API
3. Set the server URL to point to your `bot.php` file
4. Configure the confirmation token
5. Get an access token with appropriate permissions
6. Enable "New message" event type

### Webhook Setup

The bot expects to receive POST requests from VKontakte's Callback API at the configured endpoint. Ensure your web server can handle incoming webhooks and that the bot script is accessible via HTTPS.

## Example Usage

<img src="https://github.com/nayutalienx/ivbot/blob/master/1.png" alt="ivbot" border="0">

## Technical Details

- **API Version**: VK API v5.89
- **Framework**: Pure PHP with cURL
- **Storage**: File-based storage for commands and command lists
- **Image Handling**: Automatic image upload and attachment via VK API

## Contributing

Feel free to submit issues and enhancement requests. When contributing code, please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is open source. Please check the repository for license details.

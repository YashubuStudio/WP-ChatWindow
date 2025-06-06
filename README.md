# ChatBotWindow Plugin

This project provides a WordPress plugin that embeds a React based ChatBot window. The React app uses **Material UI** and communicates with an external API via a WordPress REST endpoint. Settings such as the API URL, key and header name can be configured from the WordPress dashboard.

## Development

The frontend is created with Create React App. Install dependencies and run the development server:

```bash
npm install
npm start
```

To create the production build that will be packaged with the plugin:

```bash
npm run build
```
The build script automatically places the necessary files into `ChatBotWindow/assets`. Create a zip archive of the `ChatBotWindow` folder for uploading to WordPress.

## WordPress Installation

1. Build the React application as described above.
2. Create a zip file of the `ChatBotWindow` directory.
3. Upload the zip file from the WordPress plugin screen and activate the plugin.
4. Configure the API URL, API key and header name in **Settings > ChatBotWindow**.
5. Add the `[chatbot_window]` shortcode to a page where you want the chat interface.

## Repository Structure

- `src/` – React source code including the ChatBot component.
- `ChatBotWindow/` – WordPress plugin directory (PHP and built assets).
- `PluginBaseDesign.txt` – design reference for the plugin.

## License

MIT


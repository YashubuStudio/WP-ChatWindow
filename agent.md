# Agent Guidelines

- Run `npm test -- --watchAll=false` before committing to ensure tests pass.
- The WordPress plugin resides in the `ChatBotWindow` directory. Built React files should be placed under `ChatBotWindow/assets` when packaging but are not tracked in git.
- Do not remove existing documentation in `README.md` and `README.ja.md`.
- Keep plugin versioning in `chatbot-window.php` minimal; bump only when releasing.


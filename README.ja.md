# ChatBotWindow プラグイン

WordPress に React 製のチャットボットを組み込むプラグインです。UI には **Material UI** を使用し、外部 API との通信は WordPress の REST API を経由します。API URL と API キーはダッシュボードから設定可能です。

## 開発手順

1. 依存パッケージをインストールします。
   ```bash
   npm install
   ```
2. 開発サーバーを起動する場合は次を実行します。
   ```bash
   npm start
   ```
3. 本番用ビルドを作成します。
   ```bash
   npm run build
   ```
4. 作成された `build` フォルダを `ChatBotWindow/assets` にコピーし、`ChatBotWindow` フォルダを zip 圧縮して WordPress にアップロードします。

## WordPress への導入

1. 上記手順で作成した zip ファイルを WordPress のプラグイン画面からアップロードして有効化します。
2. **設定 > ChatBotWindow** から API URL と API キーを保存します。

## ディレクトリ構成

- `src/` – React のソースコード。
- `ChatBotWindow/` – WordPress プラグイン用のディレクトリ（PHP とビルド済みアセット）。
- `PluginBaseDesign.txt` – プラグイン設計の参考資料。

## ライセンス

MIT


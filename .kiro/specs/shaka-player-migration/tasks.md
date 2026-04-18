# Tasks: Shaka Player Migration

## Task List

- [x] 1. 安裝 Shaka Player 並更新 package.json 依賴
  - [x] 1.1 在 `package.json` 的 `dependencies` 中新增 `"shaka-player": "file:./shaka-player-5.1.0.tgz"`
  - [x] 1.2 從 `package.json` 的 `devDependencies` 移除 `video.js`
  - [x] 1.3 從 `package.json` 的 `dependencies` 移除所有 video.js 相關插件（videojs-*、@silvermine/videojs-*、@theonlyducks/videojs-zoom、@videojs/themes、@kokotree-inc/videojs-*、@adsignal/videojs-*、@api.video/videojs-*）
  - [x] 1.4 從 `package.json` 的 `dependencies` 移除 `dashjs`
  - [x] 1.5 執行 `npm install` 確認安裝成功

- [x] 2. 建立 shakaPlayer.js 初始化模組
  - [x] 2.1 建立 `resources/js/components/shakaPlayer.js`，import shaka-player
  - [x] 2.2 實作 `parseDataAttributes(element)` 函式，解析所有 `data-*` 屬性（含百分比寬高轉換）
  - [x] 2.3 實作 `base64ImageLogo()` 函式（從 videoJs.js 移植）
  - [x] 2.4 實作 `validateAndSetPoster(element, posterUrl)` 函式（axios 驗證 + fallback）
  - [x] 2.5 實作 `load(player, config)` 非同步函式，處理 DASH 與一般串流載入
  - [x] 2.6 實作音量持久化邏輯（localStorage 讀寫，含 try-catch 保護）
  - [x] 2.7 實作 `initShakaPlayers()` 主函式，掃描 `.shaka-player` 元素並初始化
  - [x] 2.8 設置 `element.cgplayer = player` 與 `element.classList.add('shaka-playered')`
  - [x] 2.9 監聽 `DOMContentLoaded`、`CG::Video_init`、`CG::Video` 事件
  - [x] 2.10 實作播放清單功能（`data-playerlist="true"` 與 `data-playerlistjson`）

- [x] 3. 更新 index.js 動態 import 邏輯
  - [x] 3.1 將 `.vjs` 偵測改為 `.shaka-player`
  - [x] 3.2 將 `import('./components/videoJs.js')` 改為 `import('./components/shakaPlayer.js')`
  - [x] 3.3 移除對 `dashvideo.js` 的任何參考（若存在）

- [x] 4. 更新 Blade 模板
  - [x] 4.1 更新 `resources/views/ShareTable/player.blade.php`：將 `class="vjs video-js vjs-theme-forest"` 改為 `class="shaka-player"`
  - [x] 4.2 更新 `resources/views/components/panel-field-card.blade.php`：將所有 `vjs video-js vjs-theme-forest` class 改為 `shaka-player`
  - [x] 4.3 更新 `resources/views/components/panel-field-card.blade.php`：將 `<div class="vjs-playlist">` 改為 `<div class="shaka-playlist">`

- [x] 5. 更新 CSS 樣式
  - [x] 5.1 更新 `resources/css/_include.scss`：移除所有 video.js 相關 CSS imports（video-js.min.css、videojs-zoom.css、videojs-playlist-ui.css、videojs-upnext、videojs-vjsdownload、@videojs/themes、silvermine-videojs-chromecast.css）
  - [x] 5.2 更新 `resources/css/profile.css`：移除相同的 video.js CSS imports
  - [x] 5.3 更新 `resources/css/components/_vidoeJs.scss`：移除 `.vjs-*` 選擇器，改為 `.shaka-player` 相關樣式（保留 `border-radius`、`overflow` 等通用樣式）

- [x] 6. 刪除舊檔案
  - [x] 6.1 刪除 `resources/js/components/videoJs.js`
  - [x] 6.2 刪除 `resources/js/components/dashvideo.js`
  - [x] 6.3 刪除 `resources/css/components/videojs-zoom.css`
  - [x] 6.4 若存在 `resources/js/components/chromecast.js`，一併刪除

- [-] 7. 驗證與測試
  - [x] 7.1 執行 `npm run build` 確認 build 成功，無編譯錯誤
  - [x] 7.2 確認 `player.blade.php` 頁面能正常載入並播放 DASH 串流
  - [x] 7.3 確認 `panel-field-card.blade.php` 中的播放器能正常初始化
  - [x] 7.4 確認音量持久化功能正常（調整音量後重新整理頁面，音量應保持）
  - [ ] 7.5 確認 bundle 中不包含 video.js 或 dashjs 相關程式碼

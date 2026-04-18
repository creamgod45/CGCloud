# Requirements Document: Shaka Player Migration

## Introduction

本需求文件描述將現有 video.js 播放器（含所有相關插件）及 dashjs 完全替換為 Google Shaka Player 5.x 的功能需求。此遷移旨在減少依賴數量、降低 bundle 體積，並以原生支援 DASH/HLS 的現代播放器取代舊有架構，同時保持所有現有 `data-*` 屬性 API 的向後相容性。

---

## Requirements

### Requirement 1: 移除 video.js 及所有相關依賴

**User Story**: 作為開發者，我希望完全移除 video.js 及其所有插件，以減少 bundle 體積並消除不必要的依賴。

#### Acceptance Criteria

1. **WHEN** 檢查 `package.json` **THEN** 不得包含以下任何套件：
   - `video.js`
   - `@adsignal/videojs-shuttle-controls`
   - `@api.video/videojs-player-analytics`
   - `@kokotree-inc/videojs-smooth-slider-plugin`
   - `@kokotree-inc/videojs-upnext-plugin`
   - `@silvermine/videojs-airplay`
   - `@silvermine/videojs-chromecast`
   - `@theonlyducks/videojs-zoom`
   - `@videojs/themes`
   - `videojs-contrib-dash`
   - `videojs-contrib-quality-menu`
   - `videojs-hls-quality-selector`
   - `videojs-landscape-fullscreen`
   - `videojs-persist`
   - `videojs-playlist`
   - `videojs-playlist-ui`
   - `videojs-vjsdownload`
   - `videojs-wavesurfer`

2. **WHEN** 檢查 `package.json` **THEN** 不得包含 `dashjs` 套件

3. **WHEN** 檢查專案檔案 **THEN** 以下檔案不得存在：
   - `resources/js/components/videoJs.js`
   - `resources/js/components/dashvideo.js`
   - `resources/js/components/chromecast.js`（若存在）
   - `resources/css/components/videojs-zoom.css`

---

### Requirement 2: 新增 Shaka Player 依賴

**User Story**: 作為開發者，我希望使用本地的 Shaka Player 5.1.0 tgz 檔案安裝播放器，以確保版本一致性。

#### Acceptance Criteria

1. **WHEN** 檢查 `package.json` 的 `dependencies` **THEN** 應包含 `shaka-player` 依賴，指向根目錄的 `shaka-player-5.1.0.tgz`

2. **WHEN** 執行 `npm install` **THEN** shaka-player 應成功安裝至 `node_modules`

---

### Requirement 3: 建立 Shaka Player 初始化模組

**User Story**: 作為開發者，我希望有一個新的 `shakaPlayer.js` 模組取代 `videoJs.js`，以統一管理所有播放器初始化邏輯。

#### Acceptance Criteria

1. **WHEN** 頁面載入且存在 `.shaka-player` 元素 **THEN** `shakaPlayer.js` 應被動態 import 並初始化所有播放器

2. **WHEN** `shakaPlayer.js` 初始化一個 `.shaka-player` 元素 **THEN** 該元素應：
   - 具有 `element.cgplayer` 屬性，值為 `shaka.Player` 實例
   - 具有 `shaka-playered` CSS class

3. **WHEN** `initShakaPlayers()` 被呼叫多次 **THEN** 已有 `shaka-playered` class 的元素不得被重複初始化

4. **WHEN** `shakaPlayer.js` 載入 **THEN** 應監聽以下事件並觸發初始化：
   - `DOMContentLoaded`
   - `CG::Video_init`
   - `CG::Video`

5. **WHEN** 瀏覽器不支援 Shaka Player（`shaka.Player.isBrowserSupported()` 為 `false`）**THEN** 應在 console 輸出警告並優雅退出，不拋出未捕獲例外

---

### Requirement 4: 保留 data-* 屬性 API（向後相容）

**User Story**: 作為開發者，我希望所有現有的 `data-*` 屬性在遷移後繼續有效，以避免修改所有使用播放器的 Blade 模板。

#### Acceptance Criteria

1. **WHEN** `<video>` 元素具有 `data-src` 屬性 **THEN** Shaka Player 應使用該 URL 載入媒體

2. **WHEN** `<video>` 元素具有 `data-width` 或 `data-height` 屬性且值包含 `%` **THEN** 應轉換為對應的像素值（相對於 `window.innerWidth` / `window.innerHeight`）

3. **WHEN** `<video>` 元素具有 `data-type="dash"` **THEN** 應使用 `application/dash+xml` MIME type 載入串流

4. **WHEN** `<video>` 元素具有 `data-controls` 屬性 **THEN** 應套用對應的 controls 設定（預設為 `true`）

5. **WHEN** `<video>` 元素具有 `data-autoplay="true"` **THEN** 播放器應自動播放

6. **WHEN** `<video>` 元素具有 `data-preload` 屬性 **THEN** 應套用對應的 preload 設定（預設為 `'none'`）

7. **WHEN** `<video>` 元素具有 `data-poster` 屬性 **THEN** 應設置對應的 poster 圖片

8. **WHEN** `<video>` 元素的 `data-poster` URL 無效（HTTP 錯誤）**THEN** 應 fallback 使用 base64 內嵌 logo 圖片

9. **WHEN** `<video>` 元素具有 `data-persist="true"` **THEN** 應從 `localStorage['volume']` 讀取音量並套用，且音量變更時應同步寫入 `localStorage`

10. **WHEN** `<video>` 元素不具有 `data-src` 屬性 **THEN** 應跳過該元素的初始化

---

### Requirement 5: 更新 Blade 模板 class 名稱

**User Story**: 作為開發者，我希望所有 Blade 模板中的 video 元素使用新的 class 名稱，以觸發 Shaka Player 初始化。

#### Acceptance Criteria

1. **WHEN** 檢查 `resources/views/ShareTable/player.blade.php` **THEN** `<video>` 元素應：
   - 包含 `shaka-player` class
   - 不包含 `vjs`、`video-js`、`vjs-theme-forest` class

2. **WHEN** 檢查 `resources/views/components/panel-field-card.blade.php` **THEN** 所有 `<video>` 元素應：
   - 包含 `shaka-player` class
   - 不包含 `vjs`、`video-js`、`vjs-theme-forest` class

3. **WHEN** 檢查 `resources/views/components/panel-field-card.blade.php` **THEN** 播放清單容器 div 應使用 `shaka-playlist` class（而非 `vjs-playlist`）

4. **WHEN** 檢查 `resources/views/ShareTable/view.blade.php` **THEN** 原生 HTML5 `<video>` 元素（不使用播放器的）應保持不變

---

### Requirement 6: 更新 index.js 動態 import 邏輯

**User Story**: 作為開發者，我希望 `index.js` 偵測 `.shaka-player` 元素而非 `.vjs`，以正確觸發 Shaka Player 的動態載入。

#### Acceptance Criteria

1. **WHEN** 頁面存在 `.shaka-player` 元素 **THEN** `index.js` 應動態 import `./components/shakaPlayer.js` 並觸發 `CG::Video_init` 事件

2. **WHEN** 頁面不存在 `.shaka-player` 元素 **THEN** `shakaPlayer.js` 不應被載入

3. **WHEN** 檢查 `index.js` **THEN** 不得包含對 `videoJs.js` 或 `dashvideo.js` 的 import 或參考

---

### Requirement 7: 更新 CSS 樣式

**User Story**: 作為開發者，我希望移除所有 video.js 相關 CSS imports，並新增 Shaka Player 所需的樣式，以確保播放器外觀正確。

#### Acceptance Criteria

1. **WHEN** 檢查 `resources/css/_include.scss` **THEN** 不得包含以下 imports：
   - `video.js/dist/video-js.min.css`
   - `components/videojs-zoom.css`
   - `videojs-playlist-ui/dist/videojs-playlist-ui.css`
   - `@kokotree-inc/videojs-upnext-plugin/upnext-styles.min.css`
   - `videojs-vjsdownload/dist/videojs-vjsdownload.css`
   - `@videojs/themes/dist/*/index.css`
   - `@silvermine/videojs-chromecast/dist/silvermine-videojs-chromecast.css`

2. **WHEN** 檢查 `resources/css/profile.css` **THEN** 同樣不得包含上述 video.js CSS imports

3. **WHEN** 檢查 `resources/css/components/_vidoeJs.scss` **THEN** 應更新為 Shaka Player 相容的樣式（移除 `.vjs-*` 選擇器，改用 `.shaka-player` 相關選擇器）

---

### Requirement 8: DASH 串流功能正確性

**User Story**: 作為使用者，我希望 DASH 串流影片能夠正常播放，包含自適應位元率切換。

#### Acceptance Criteria

1. **WHEN** 播放 `data-type="dash"` 的影片 **THEN** Shaka Player 應成功載入並播放 DASH 串流

2. **WHEN** 網路狀況變化 **THEN** Shaka Player 應自動切換適當的位元率（ABR）

3. **WHEN** DASH 串流載入失敗 **THEN** 應在 console 記錄錯誤，不影響頁面其他功能

---

### Requirement 9: 音量持久化功能

**User Story**: 作為使用者，我希望我的音量設定在頁面重新整理後仍然保留。

#### Acceptance Criteria

1. **WHEN** 使用者調整音量且 `data-persist="true"` **THEN** 新音量（0–100 整數）應寫入 `localStorage['volume']`

2. **WHEN** 播放器初始化且 `data-persist="true"` 且 `localStorage['volume']` 存在 **THEN** 應套用已儲存的音量值

3. **WHEN** `localStorage` 不可用（例如隱私模式）**THEN** 應靜默忽略錯誤，使用瀏覽器預設音量

---

### Requirement 10: 播放清單功能

**User Story**: 作為使用者，我希望播放清單功能在遷移後繼續正常運作。

#### Acceptance Criteria

1. **WHEN** `<video>` 元素具有 `data-playerlist="true"` 且 `data-playerlistjson` 包含有效 JSON **THEN** 應初始化播放清單功能

2. **WHEN** 播放清單初始化 **THEN** 應在 `.shaka-playlist` 容器中渲染播放清單 UI

3. **WHEN** 使用者點擊播放清單項目 **THEN** 播放器應切換至對應的媒體來源

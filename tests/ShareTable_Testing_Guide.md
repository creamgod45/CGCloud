# ShareTable 測試指南

## 概述

本測試套件專門針對 CGCloud 平台的 ShareTable 功能進行全面測試，包括單元測試和功能測試。

## 測試結構

### 單元測試 (`tests/Unit/ShareTableTest.php`)

測試 ShareTable 模型的核心功能：

- ✅ **基本 CRUD 操作**
  - 創建公開分享表
  - 創建私人分享表（含密碼）
  - 驗證必要欄位

- ✅ **關聯關係測試**
  - 與 Member 的關聯
  - 與 SharePermissions 的關聯
  - 與 VirtualFile 的關聯

- ✅ **權限管理**
  - 檢查擁有者權限
  - 檢查成員權限
  - 權限過期處理

- ✅ **檔案管理**
  - 附加虛擬檔案
  - 獲取所有關聯檔案
  - DASH 影片可用性檢查

- ✅ **URL 生成**
  - 分享連結生成
  - 短代碼驗證

### 功能測試 (`tests/Feature/ShareTableFeatureTest.php`)

測試 ShareTable 的 HTTP 端點和用戶互動：

- ✅ **訪問控制**
  - 公開分享表訪問
  - 私人分享表認證
  - 未授權訪問防護

- ✅ **API 端點**
  - 創建分享表 API
  - 添加檔案到分享表
  - 權限管理 API

- ✅ **特殊功能**
  - 過期分享表處理
  - 密碼保護分享
  - 檔案下載功能

- ✅ **數據驗證**
  - 輸入數據驗證
  - 搜索功能測試

## 執行測試

### 方法 1: 使用批次腳本（推薦）

```bash
# Windows
run-sharetable-tests.bat

# 或者手動執行
./vendor/bin/phpunit --configuration phpunit-sharetable.xml
```

### 方法 2: 個別執行測試

```bash
# 只執行單元測試
./vendor/bin/phpunit tests/Unit/ShareTableTest.php

# 只執行功能測試
./vendor/bin/phpunit tests/Feature/ShareTableFeatureTest.php

# 執行特定測試方法
./vendor/bin/phpunit tests/Unit/ShareTableTest.php --filter it_can_create_a_share_table
```

### 方法 3: 使用 Composer 腳本

```bash
# 如果在 composer.json 中定義了腳本
composer test:sharetable
```

## 測試覆蓋率

測試執行後會生成覆蓋率報告：

- **HTML 報告**: `coverage/sharetable-html/index.html`
- **文字報告**: `coverage/sharetable.txt`
- **XML 報告**: `coverage/sharetable-xml/`

## 測試數據

測試使用 Laravel Factory 生成測試數據：

- `MemberFactory` - 生成測試用戶
- `ShareTableFactory` - 生成分享表
- `VirtualFileFactory` - 生成虛擬檔案
- `SharePermissionsFactory` - 生成權限記錄

## 環境配置

測試使用獨立的測試環境：

- **資料庫**: SQLite 記憶體資料庫
- **快取**: Array 驅動
- **佇列**: 同步執行
- **郵件**: Array 驅動

## 常見問題

### 1. 測試失敗：資料庫連接錯誤

**解決方案**: 確保 `.env.testing` 檔案存在並配置正確：

```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### 2. Factory 相關錯誤

**解決方案**: 確保所有相關的 Factory 檔案存在：
- `database/factories/MemberFactory.php`
- `database/factories/ShareTableFactory.php`
- `database/factories/VirtualFileFactory.php`

### 3. 路由不存在錯誤

**解決方案**: 功能測試中的路由可能需要根據實際應用調整。檢查 `routes/web.php` 和 `routes/api.php`。

### 4. 權限測試失敗

**解決方案**: 確保 `SharePermissions` 模型和相關遷移檔案正確設置。

## 測試最佳實踐

1. **隔離性**: 每個測試都是獨立的，使用 `RefreshDatabase` trait
2. **可讀性**: 測試方法名稱清楚描述測試目的
3. **完整性**: 涵蓋正常流程和異常情況
4. **效能**: 使用記憶體資料庫提高測試速度

## 擴展測試

要添加新的測試案例：

1. 在相應的測試類別中添加新方法
2. 使用 `/** @test */` 註解或 `test_` 前綴
3. 遵循 AAA 模式：Arrange（準備）、Act（執行）、Assert（驗證）

```php
/** @test */
public function it_can_do_something_new()
{
    // Arrange - 準備測試數據
    $member = Member::factory()->create();
    
    // Act - 執行要測試的操作
    $result = $member->doSomething();
    
    // Assert - 驗證結果
    $this->assertTrue($result);
}
```

## 持續整合

建議將這些測試整合到 CI/CD 流程中：

```yaml
# GitHub Actions 範例
- name: Run ShareTable Tests
  run: |
    php artisan migrate --env=testing
    ./vendor/bin/phpunit --configuration phpunit-sharetable.xml
```

## 效能監控

定期檢查測試執行時間，如果測試變慢：

1. 檢查是否有不必要的資料庫操作
2. 考慮使用 `DatabaseTransactions` 而非 `RefreshDatabase`
3. 優化 Factory 生成的數據量

---

**注意**: 這些測試基於當前的模型結構。如果模型或資料庫結構有變更，請相應更新測試。
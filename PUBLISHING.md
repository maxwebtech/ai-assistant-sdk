# 發布指南

## 📦 如何發布到 Packagist

### 1. 準備 GitHub Repository

```bash
# 初始化 Git
cd /Users/maxwebtech/Sites/ai-assistant/packages/ai-assistant-sdk
git init
git add .
git commit -m "Initial commit: AI Assistant PHP SDK v1.0.0"

# 創建 GitHub Repository (在 GitHub 網站上創建)
# Repository 名稱: ai-assistant-sdk

# 添加遠端倉庫
git remote add origin https://github.com/maxwebtech/ai-assistant-sdk.git
git branch -M main
git push -u origin main
```

### 2. 創建版本標籤

```bash
# 創建版本標籤
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

### 3. 註冊到 Packagist

1. 訪問 [Packagist.org](https://packagist.org)
2. 用 GitHub 帳號登入
3. 點擊 "Submit"
4. 輸入 GitHub Repository URL: `https://github.com/maxwebtech/ai-assistant-sdk`
5. 點擊 "Check"

### 4. 設定自動更新

在 GitHub Repository 設定中：
1. 進入 Settings → Webhooks
2. 添加 Packagist webhook: `https://packagist.org/api/github?username=YOUR_USERNAME&apiToken=YOUR_API_TOKEN`

## 🏷️ 版本管理

### 語義化版本

- **MAJOR.MINOR.PATCH** (例如: 1.2.3)
- **MAJOR**: 不相容的 API 變更
- **MINOR**: 新功能，向後相容
- **PATCH**: Bug 修復，向後相容

### 發布新版本

```bash
# 1. 更新程式碼
# 2. 更新 CHANGELOG.md
# 3. 提交變更
git add .
git commit -m "feat: add new feature"

# 4. 創建新標籤
git tag -a v1.1.0 -m "Release version 1.1.0"
git push origin v1.1.0
```

## ✅ 發布前檢查清單

- [ ] 程式碼通過所有測試
- [ ] README.md 已更新
- [ ] CHANGELOG.md 已更新
- [ ] 版本號已更新
- [ ] 所有範例程式碼都能正常運行
- [ ] 文檔完整且正確

## 📋 測試指令

```bash
# 安裝依賴
composer install

# 執行測試
composer test

# 程式碼分析
composer analyse

# 檢查程式碼風格
composer cs-check
```

## 🌟 推廣套件

### 1. 文檔網站
建議建立專門的文檔網站，包含：
- 安裝指南
- API 參考
- 使用範例
- 最佳實踐

### 2. 範例專案
建立完整的範例專案：
- Laravel 整合範例
- WordPress 外掛範例
- 純 PHP 範例

### 3. 社群推廣
- 在相關論壇分享
- 撰寫技術文章
- 製作教學影片

## 🔧 維護

### 定期任務
- 更新依賴套件
- 修復安全性問題
- 回應 Issues
- 審查 Pull Requests

### 支援政策
- 最新的主要版本：完整支援
- 前一個主要版本：安全性修復
- 更早版本：不支援
# AI Assistant PHP SDK

超快整合 AI 小幫手的 PHP 套件。內建 JWT 支援、會員等級、對話與訊息額度，3 分鐘上線。

## 安裝

```bash
composer require maxwebtech/ai-assistant-sdk firebase/php-jwt
```

## 快速開始（3 步驟）

1) 初始化

```php
use MaxWebTech\AiAssistant\AiAssistantSDK;

$sdk = new AiAssistantSDK([
    'widget_token' => getenv('AI_ASSISTANT_WIDGET_TOKEN'),
    'iframe_token' => getenv('AI_ASSISTANT_IFRAME_TOKEN'), // 可選
    'api_url'      => getenv('AI_ASSISTANT_API_URL') ?: 'http://localhost:8000',
]);
```

2) 前端嵌入（Widget）

```php
$user = ['id' => $userId, 'name' => $name, 'email' => $email];

// 如果你的服務端已經會發 JWT，直接傳入即可（最推薦）；SDK 不會自產
// $jwt = issueJwtForUser($user); // 你的系統生成
echo $sdk->getWidgetHTML($user, [
    'jwt' => $jwt ?? null,               // 若提供，SDK 直接使用，不會自己產生
    'membership' => ['level' => 'free'], // 可選；若無 jwt，SDK 會退回 member-id 模式
    'title' => 'AI 助手',
]);
```

3) 查詢用量（自動避免匿名誤判）

```php
// 會優先使用 SDK config 的 tenant_id
// 若提供 membershipLevel，SDK 會先行同步（assign）會員等級再查詢
// 直接使用你服務端簽的 JWT（必要）
$quota = $sdk->checkUserQuota($userId, (int) getenv('AI_ASSISTANT_TENANT_ID'), null, null, $jwt);

$daily = $quota['daily_conversations'];
printf('今日對話：已用 %d，剩餘 %s', $daily['used'], $daily['unlimited'] ? '無限制' : (string) $daily['remaining']);
```

## Iframe 嵌入（可選）

```php
// 同理：若你已有 JWT，直接帶上
echo $sdk->getIframeHTML($user, [
    'jwt' => $jwt ?? null,
    'membership' => ['level' => 'basic'],
    'width' => '100%',
    'height' => '600px',
    'style' => 'border: 1px solid #e5e7eb; border-radius: 8px;',
    'title' => 'AI Assistant',
]);
```

## 會員與用量 API（可選）

```php
// 取得等級清單（需管理權限 JWT）
$tiers = $sdk->getMembershipTiers((int) getenv('AI_ASSISTANT_TENANT_ID'), $jwt);

// 指定用戶等級（直接提供管理 JWT）
$sdk->assignMembershipTier('user-123', 'premium', (int) getenv('AI_ASSISTANT_TENANT_ID'), $jwt);

// 重置用量快取（同上）
$sdk->resetUserQuota('user-123', (int) getenv('AI_ASSISTANT_TENANT_ID'), $jwt);
```

## 設定說明

- `widget_token`：後台建立助手後取得
- `jwt_secret`：後台租戶設定的 JWT secret（建議設，SDK 才能自動帶 JWT）
- `tenant_id`：你的租戶 ID（查用量與管理 API 需要）
- `api_url`：後端 API 位址（本機多為 `http://localhost:8000`）
- `issuer`：你的站點 URL（可選）

## 常見問題

- 顯示「無限制」但我有設定上限？
  - 你的用戶可能被視為匿名。使用 `checkUserQuota($userId, null, null, 'free')` 會先自動同步會員等級再查詢。
- Widget 沒有出現？
  - 檢查 `widget_token` 是否正確，且助手允許你的網域（allowed domains）。
- 出現 JWT 錯誤？
  - 安裝 `firebase/php-jwt`，並確認 `jwt_secret`、`issuer`、`api_url` 設定無誤。

## 授權

MIT

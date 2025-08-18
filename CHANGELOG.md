# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [7.1.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v7.0.1...v7.1.0) (2025-08-18)


### Features

* **sdk:** add monthly quota support and update documentation ([a722907](https://github.com/maxwebtech/ai-assistant-sdk/commit/a722907ade6f0f22f433305fc22afef81f5b53f7))

## [7.0.1](https://github.com/maxwebtech/ai-assistant-sdk/compare/v7.0.0...v7.0.1) (2025-08-15)


### Bug Fixes

* Update test assertion to use correct data-member-id attribute ([39b40b7](https://github.com/maxwebtech/ai-assistant-sdk/commit/39b40b72a0b0916122124774944e836adfb9ce4a))

## [7.0.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v6.1.3...v7.0.0) (2025-08-15)


### ⚠ BREAKING CHANGES

* **sdk:** The SDK no longer generates JWTs internally. Provide a server-signed JWT via options.jwt for widget/iframe and all quota/membership APIs. Removed generateJWT/generateUserJWT. checkUserQuota/getMembershipTiers/assignMembershipTier/resetUserQuota now require an external JWT.

### Features

* **sdk:** require external JWT; remove auto-generation ([e04e822](https://github.com/maxwebtech/ai-assistant-sdk/commit/e04e8220dbedbc3022fbf30b3eaeb9391be2d8ce))

## [6.1.3](https://github.com/maxwebtech/ai-assistant-sdk/compare/v6.1.2...v6.1.3) (2025-08-15)


### Bug Fixes

* Use limit instead of remaining to determine unlimited status ([42f0b84](https://github.com/maxwebtech/ai-assistant-sdk/commit/42f0b84eaa6848a2676ec980c45e2c452e30f93f))

## [6.1.2](https://github.com/maxwebtech/ai-assistant-sdk/compare/v6.1.1...v6.1.2) (2025-08-15)


### Bug Fixes

* Add null coalescing for remaining array keys ([9f6b3ea](https://github.com/maxwebtech/ai-assistant-sdk/commit/9f6b3ea8a42bdab37bac211b5edc25ffb74ba6c8))

## [6.1.1](https://github.com/maxwebtech/ai-assistant-sdk/compare/v6.1.0...v6.1.1) (2025-08-15)


### Bug Fixes

* Use API-provided remaining values instead of calculating manually ([664c381](https://github.com/maxwebtech/ai-assistant-sdk/commit/664c381ba2904eccd0713d73dee3acda8d94d082))

## [6.1.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v6.0.1...v6.1.0) (2025-08-15)


### Features

* Transform quota check API response to standardized SDK format ([0fd9c56](https://github.com/maxwebtech/ai-assistant-sdk/commit/0fd9c56da72af4378e170f8bb117f833afaae57f))

## [6.0.1](https://github.com/maxwebtech/ai-assistant-sdk/compare/v6.0.0...v6.0.1) (2025-08-15)


### Bug Fixes

* Add required 'sub' field to JWT payload for server validation ([48f8d01](https://github.com/maxwebtech/ai-assistant-sdk/commit/48f8d01f174dbc6bb3c788e36ffdfda5fc709eeb))

## [6.0.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v5.0.0...v6.0.0) (2025-08-15)


### ⚠ BREAKING CHANGES

* tenantId parameter is now required for quota and membership methods

### Bug Fixes

* Add required tenantId parameter to JWT authenticated methods ([2154518](https://github.com/maxwebtech/ai-assistant-sdk/commit/21545183dbd32db8aa9cf3b42363328a4a747387))

## [5.0.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v4.0.0...v5.0.0) (2025-08-15)


### ⚠ BREAKING CHANGES

* JWT secret is now required for quota and membership operations

### Bug Fixes

* Use JWT authentication for quota and membership API methods ([9f94f50](https://github.com/maxwebtech/ai-assistant-sdk/commit/9f94f500de2bb77ba6c7600b1f0cfd8e22fc2d5f))


### Miscellaneous Chores

* Remove release-please manifest and clean up versioning ([2deccf9](https://github.com/maxwebtech/ai-assistant-sdk/commit/2deccf916c456f024d23609d5594e30009c1e8a2))

## 4.0.0 (2025-08-15)


### ⚠ BREAKING CHANGES

* getUserUsageStatus method has been removed
* getMembershipTiers() now requires tenantId parameter and uses JWT authentication
* api_token parameter removed from constructor, all API operations now use widget_token

### Features

* Add JWT authentication for membership tier API calls ([d481785](https://github.com/maxwebtech/ai-assistant-sdk/commit/d4817857f6c0b672f7230133cb4a0dceee337886))
* Add membership tier and quota management APIs ([5ce28d9](https://github.com/maxwebtech/ai-assistant-sdk/commit/5ce28d91a38f96cc7e7b71b532dbb9fcf1186727))
* **ci:** Add release-please to automate semantic versioning and changelog generation ([6cb7670](https://github.com/maxwebtech/ai-assistant-sdk/commit/6cb76702e78aa5cfeb98313585b221038a9e654b))
* **ci:** Add release-please workflow for automated versioning ([356ffc4](https://github.com/maxwebtech/ai-assistant-sdk/commit/356ffc479894fcae88ff4b688028a995e37a79d6))
* **sdk:** Add getUserUsageStatus method to check usage limits ([a72132c](https://github.com/maxwebtech/ai-assistant-sdk/commit/a72132c3a0d81d861c30268a5d42f276baafc201))
* Simplify authentication to use single Widget Token ([8e865d0](https://github.com/maxwebtech/ai-assistant-sdk/commit/8e865d086586676f13cb5e6f8c7cc725c6692fcc))


### Bug Fixes

* **ci:** Add issues write permission for release-please to manage labels ([5349983](https://github.com/maxwebtech/ai-assistant-sdk/commit/53499837a31f5fd372e9b39aeb1d89d23181d484))
* **ci:** Install dev dependencies for running tests in release workflow ([431a7b5](https://github.com/maxwebtech/ai-assistant-sdk/commit/431a7b55ccb2fe516cc186e4e7d08b210ee7258e))
* **sdk:** Revert API URL to localhost for development ([fa9e7db](https://github.com/maxwebtech/ai-assistant-sdk/commit/fa9e7dbd51f92723c50a0f09eb5673dcad15d00a))
* **sdk:** Update default API URL to ngrok endpoint for testing ([ba23104](https://github.com/maxwebtech/ai-assistant-sdk/commit/ba231041c32dc3ca2ebf9d518443bba3554e816d))
* **sdk:** Update default API URL to production endpoint ([5c333e5](https://github.com/maxwebtech/ai-assistant-sdk/commit/5c333e53c0f87a84439e6fef54e5026255883c5f))


### Miscellaneous Chores

* **ci:** Remove manual release workflow, use release-please only ([d435d3d](https://github.com/maxwebtech/ai-assistant-sdk/commit/d435d3d6c21d7f985b14d4fb3866f876f53e962f))
* **composer:** Update package namespace from alan199501 to maxwebtech ([5b9575f](https://github.com/maxwebtech/ai-assistant-sdk/commit/5b9575fb99ed2963047831a9800945817b35a677))
* **composer:** Update package namespace from maxwebtech to alan199501 ([b507593](https://github.com/maxwebtech/ai-assistant-sdk/commit/b507593efbc3f20425fb03f932c60609d10c8206))
* **docs:** Remove support contact information ([ef05573](https://github.com/maxwebtech/ai-assistant-sdk/commit/ef0557309f4541358d3745af0f4a850f5542bccc))
* **main:** release 1.1.0 ([#1](https://github.com/maxwebtech/ai-assistant-sdk/issues/1)) ([9cd550f](https://github.com/maxwebtech/ai-assistant-sdk/commit/9cd550f019c1ac84993c6b35a5d6019ee135a492))
* **main:** release 1.1.1 ([#2](https://github.com/maxwebtech/ai-assistant-sdk/issues/2)) ([dc8e02d](https://github.com/maxwebtech/ai-assistant-sdk/commit/dc8e02d01baf487700c1a0932308a4ba7f970abd))
* **main:** release 1.1.2 ([#3](https://github.com/maxwebtech/ai-assistant-sdk/issues/3)) ([69515e9](https://github.com/maxwebtech/ai-assistant-sdk/commit/69515e9099eaeae20be701c6ec60ef3aebe3a983))
* **main:** release 1.2.0 ([#4](https://github.com/maxwebtech/ai-assistant-sdk/issues/4)) ([73defca](https://github.com/maxwebtech/ai-assistant-sdk/commit/73defca8baa4c57e4bf7e87c6cfbf5de9929e139))
* **main:** release 1.2.1 ([#5](https://github.com/maxwebtech/ai-assistant-sdk/issues/5)) ([d511c5f](https://github.com/maxwebtech/ai-assistant-sdk/commit/d511c5fb46cedac1305e77c654551b9d22080bbf))
* **main:** release 1.2.2 ([#6](https://github.com/maxwebtech/ai-assistant-sdk/issues/6)) ([3851511](https://github.com/maxwebtech/ai-assistant-sdk/commit/385151162ba0a10e5e053c9efe91760d80654dd7))
* **main:** release 1.2.3 ([#7](https://github.com/maxwebtech/ai-assistant-sdk/issues/7)) ([d490c18](https://github.com/maxwebtech/ai-assistant-sdk/commit/d490c18e7dfb907be9213ada11e4376b32147401))
* **main:** release 1.3.0 ([#8](https://github.com/maxwebtech/ai-assistant-sdk/issues/8)) ([dff0be0](https://github.com/maxwebtech/ai-assistant-sdk/commit/dff0be0d83db9bb86ade27d321e2d67fa2c6f2ac))
* **main:** release 2.0.0 ([#9](https://github.com/maxwebtech/ai-assistant-sdk/issues/9)) ([600ffef](https://github.com/maxwebtech/ai-assistant-sdk/commit/600ffefd144a6d28b68789644d3688ddab7e5393))
* **main:** release 3.0.0 ([#10](https://github.com/maxwebtech/ai-assistant-sdk/issues/10)) ([73f2bdf](https://github.com/maxwebtech/ai-assistant-sdk/commit/73f2bdfbd3c01cf4a1b04f0d4b21d409667af18b))
* **sdk:** Update default API URL to localhost:8000 ([c09f602](https://github.com/maxwebtech/ai-assistant-sdk/commit/c09f602b2fc0c3aaab59cb2276bdeda7656d6444))


### Code Refactoring

* Remove deprecated getUserUsageStatus method ([5c48a42](https://github.com/maxwebtech/ai-assistant-sdk/commit/5c48a42bb838bef3463b303e49ae98eb1b6dad4e))

## [Unreleased]

### ⚠ BREAKING CHANGES

* Removed deprecated getUserUsageStatus method - use checkUserQuota method instead

### Removed

* **sdk:** Remove deprecated getUserUsageStatus method and formatUsageResponse helper method

## [3.0.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v2.0.0...v3.0.0) (2025-08-14)


### ⚠ BREAKING CHANGES

* getMembershipTiers() now requires tenantId parameter and uses JWT authentication

### Features

* Add JWT authentication for membership tier API calls ([2a8ed0b](https://github.com/maxwebtech/ai-assistant-sdk/commit/2a8ed0b102752324e3116ad65b9bc410cdc4d406))

## [2.0.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.3.0...v2.0.0) (2025-08-14)


### ⚠ BREAKING CHANGES

* api_token parameter removed from constructor, all API operations now use widget_token

### Features

* Simplify authentication to use single Widget Token ([23efa0b](https://github.com/maxwebtech/ai-assistant-sdk/commit/23efa0bf39e20ad6db206b881de3c47e7f1d6cb2))

## [1.3.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.2.3...v1.3.0) (2025-08-14)


### Features

* Add membership tier and quota management APIs ([bc13a46](https://github.com/maxwebtech/ai-assistant-sdk/commit/bc13a46be3ed254d79449d7b5efff4b6438d6945))

## [1.2.3](https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.2.2...v1.2.3) (2025-08-14)


### Bug Fixes

* **sdk:** Update default API URL to ngrok endpoint for testing ([cd55c48](https://github.com/maxwebtech/ai-assistant-sdk/commit/cd55c48dc9ccd3efd86ff82957549babc1ff8e79))

## [1.2.2](https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.2.1...v1.2.2) (2025-08-14)


### Bug Fixes

* **sdk:** Revert API URL to localhost for development ([240c07f](https://github.com/maxwebtech/ai-assistant-sdk/commit/240c07f70c45ccc6e3fbe9da8e94bfc87cf83950))

## [1.2.1](https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.2.0...v1.2.1) (2025-08-14)


### Bug Fixes

* **sdk:** Update default API URL to production endpoint ([c4e6af1](https://github.com/maxwebtech/ai-assistant-sdk/commit/c4e6af1a9d71773219dd97a2a03a5ce2c8c6ad81))

## [1.2.0](https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.1.2...v1.2.0) (2025-08-14)


### Features

* **sdk:** Add getUserUsageStatus method to check usage limits ([97df2b7](https://github.com/maxwebtech/ai-assistant-sdk/commit/97df2b70ba26b12738356e0e7131c6d7e5538ea1))

## [1.1.2](https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.1.1...v1.1.2) (2025-08-13)


### Miscellaneous Chores

* **sdk:** Update default API URL to localhost:8000 ([f7477ad](https://github.com/maxwebtech/ai-assistant-sdk/commit/f7477ad3855ade1939d42f7ee7152a37a5295307))

## [1.1.1](https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.1.0...v1.1.1) (2025-08-13)


### Miscellaneous Chores

* **ci:** Remove manual release workflow, use release-please only ([c6f1a7a](https://github.com/maxwebtech/ai-assistant-sdk/commit/c6f1a7a5d3532781976134141f98346e4242af54))
* **composer:** Update package namespace from alan199501 to maxwebtech ([3be7af8](https://github.com/maxwebtech/ai-assistant-sdk/commit/3be7af8551b5faf31a68708d5621b3ba11a2b0b5))
* **docs:** Remove support contact information ([a70b674](https://github.com/maxwebtech/ai-assistant-sdk/commit/a70b6740bd46b772d53c9f6e7a435ab7c2c89e91))

## [1.1.0](https://github.com/alan199501/ai-assistant-sdk/compare/v1.0.1...v1.1.0) (2025-08-13)


### Features

* **ci:** Add release-please workflow for automated versioning ([356ffc4](https://github.com/alan199501/ai-assistant-sdk/commit/356ffc479894fcae88ff4b688028a995e37a79d6))


### Bug Fixes

* **ci:** Add issues write permission for release-please to manage labels ([5349983](https://github.com/alan199501/ai-assistant-sdk/commit/53499837a31f5fd372e9b39aeb1d89d23181d484))
* **ci:** Install dev dependencies for running tests in release workflow ([431a7b5](https://github.com/alan199501/ai-assistant-sdk/commit/431a7b55ccb2fe516cc186e4e7d08b210ee7258e))

## [Unreleased]

## [1.0.0] - 2024-01-XX

### Added
- 🎉 Initial release of AI Assistant PHP SDK
- 🔐 JWT authentication support with secure token generation
- 👥 Multi-tier membership system (guest, free, basic, premium, enterprise)
- 🎨 Multiple integration methods:
  - Widget HTML generation
  - iframe HTML generation  
  - Dynamic JavaScript loading
- 🛡️ Security features:
  - JWT signature verification
  - Replay attack prevention (jti)
  - Timestamp validation (exp, nbf, iat)
  - HTML escaping for XSS prevention
- 📊 Conversation and message limits per membership level
- 🔧 Comprehensive error handling and validation
- 📚 Complete documentation with examples
- ✅ Full test coverage with PHPUnit
- 🎯 PSR-4 autoloading support
- 📦 Composer package ready for distribution

### Features
- Generate secure JWT tokens for user authentication
- Create Widget HTML with customizable options
- Generate iframe embeddings with flexible sizing
- Dynamic JavaScript widget loading
- Validate JWT tokens on server side
- Get default limits for membership levels
- Support for custom membership configurations
- Laravel framework integration examples

### Security
- JWT tokens expire after 1 hour by default
- Unique JWT IDs (jti) prevent token replay attacks
- Input validation and sanitization
- HTML entity escaping for output safety
- Secure random token generation

### Documentation
- Comprehensive README with installation and usage examples
- API reference documentation
- Laravel integration guide
- Security best practices
- Troubleshooting guide
- Multiple language examples (PHP, Laravel, WordPress)

[Unreleased]: https://github.com/maxwebtech/ai-assistant-sdk/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/maxwebtech/ai-assistant-sdk/releases/tag/v1.0.0

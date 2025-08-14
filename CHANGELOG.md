# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

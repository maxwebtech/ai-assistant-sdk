# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

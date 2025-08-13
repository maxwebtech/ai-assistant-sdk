# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
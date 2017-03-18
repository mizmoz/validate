
v0.7.0

- Add IsReCaptcha to validate reCAPTCHA responses
- Fix code style issues

v0.6.0

- Add IsEmailDisposable validator to check for email addresses like bob@guerillamail.com
- Add option to invalidate IsEmail if the passed value is a disposable email
- Add `mizmoz update` helper for updating the disposable hosts  

v0.5.2

- Fix bug where tags default values always returned the default value

v0.5.1

- Fix bug where tags with callbacks couldn’t use multiple tag names such as #active|#inactive => callback

v0.5.0

- Add new filtering options to allow default tags

v0.4.7

- Fix bug where emails would be considered tags and split at the @ sign
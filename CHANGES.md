
v0.11.0

- Add ability to mock the Validator helper methods using ValidatorFactory::mock('isString')
- Update disposable email hosts file
- Update copyright notices to MIT

v0.10.3

- Fix INTL_IDNA_VARIANT_2003 deprecation warning in PHP 7.2

v0.10.2

- Add missing validators IsReCaptcha and IsIterable to the Validate helper & ValidateFactory

v0.10.1

- Fix bug where chained failures would break due to the error message being an array

v0.10.0

- Add Decimal type for handling decimal numbers correctly
- Add IsDecimal validator
- Breaking change to how the results are returned. When using the Chain only keys with errors are returned now.
- Fix IsLength failing when passed ValueNotSet()

v0.9.1

- Fix IsRange failing when passed ValueNotSet()
- Fix broken tests

v0.9.0

- Add Validator\Name interface to Chain to allow the definition of the name. This can be useful
when you are using the Description directly in a UI.
- Breaking Change to remove description property when it is empty from the
Description::getDescription
- Breaking Change rename IsArray to IsArrayOfType
- Create new IsArray that only validates against a list of items to be consistent with IsOneOf
- Breaking Change to remove allowed key from IsOneOf and just return the allowed items
- Breaking Change to remove allowed key from IsFilter and just return the allowed items
- Fix bug in isBoolean where validating false on a required chain will be invalid
- Fix bug in Description::getDescriptionForShapes where leaf nodes weren‘t being describe correctly
- Fix bug in IsOneOfType not returning the validation result value
- Update disposable email hosts file

v0.8.1

- Add Validator\Name interface to allow validators to define their own name for descriptions

v0.8.0

- Breaking Change for IsDate to accept an array of options in it‘s constructor
- Add ability to make IsDate not strict so it will treat an empty string as ValueNotSet

v0.7.3

- Add ability to resolve child data in IsArrayOf
- Add IsRange for checking numbers are within a range
- Update disposable email hosts file

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
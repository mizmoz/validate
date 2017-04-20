# [Mizmoz](https://www.mizmoz.com) / validate

Validation for PHP 7 that tries to suck less.

We've used a lot of different validation libraries over time and I've yet to be truly happy with any of them.

The main aim for this is to create a validator that can handle complex items, resolve them and also create nice 
descriptions of themselves. The dream is to create a validator that can handle REST API data, send useful error
messages to the user and also a nice description of the endpoint. This will be the face of the Mizmoz API.

## Table of Contents

- [Getting started](#getting-started)
  * [Composer installation](#composer-installation)
  * [Keeping the resources up to date](#keeping-the-resources-up-to-date)
  * [Basic validation](#basic-validation)
- [Validators](#validators)
  * [`IsArray`](#isarray)
  * [`IsArrayOf`](#isarrayof)
  * [`IsArrayOfShape`](#isarrayofshape)
  * [`IsArrayOfType`](#isarrayoftype)
  * [`IsBoolean`](#isboolean)
  * [`IsDate`](#isdate)
  * [`IsEmail`](#isemail)
  * [`IsEmailDisposable`](#isemaildisposable)
  * [`IsFilter`](#isfilter)
  * [`IsInteger`](#isinteger)
  * [`IsNumeric`](#isnumeric)
  * [`IsObject`](#isobject)
  * [`IsOneOf`](#isoneof)
  * [`IsOneOfType`](#isoneoftype)
  * [`IsReCaptcha`](#isrecaptcha)
  * [`IsRequired`](#isrequired)
  * [`IsSame`](#issame)
  * [`IsShape`](#isshape)
  * [`IsString`](#isstring)


# Getting started

## Composer installation

It's probably worth pointing out the API is really new and very likely to change.

```
composer require mizmoz/validate
```

## Keeping the resources up to date

If you‘re using the IsEmailDisposable validator you‘ll want to make sure you‘re using
an up to date list of disposable email host names.

Best practice is you create a cron job that executes the update below.

```
php bin/mizmoz update
```

See the resources folder for an example cron file that should be placed in /etc/cron.d.

## Basic validation

```php
Validate::isString()->validate('Hello there!'); // true

Validate::isObject()->validate(new DateTime); // true

Validate::isArray()->validate([]); // true
Validate::isArray()->validate(new ObjectWithToArray); // true

Validate::isOneOf(['on', 'off'])->validate('on'); // true
Validate::isOneOf(['on', 'off'])->validate('oops'); // false

// Validate a value and return it's object 
$result = Validate::isObjectOfType(DateTime::class)->validate('2016-01-01 00:00:00');
$result->isValid(); // true
$result->getValue(); // DateTime 2016-01-01 00:00:00
```

### Resolving a validator to a new value

```php
$result = Validate::isSame('me')
    ->toValue(\User::current()->userId)
    ->validate($userId);

// get the user id
$userId = $result->getValue();
```

### More complex and useful examples

```php
// Validate a set of items
$result = Validate::set([
    'name' => Validate::isString()
        ->setDescription('Subscriber name')
        ->isRequired(),
    'email' => Validate::isEmail()
        ->isRequired(),
    'sendNewsletter' => Validate::isBoolean()
        ->setDescription('Subscribe the email address to the newsletter?')
        ->setDefault(false)
])->validate($_POST);

// Get the sanitised data as an array.
$values = $result->getValue();
```

### Validate ACL

Whilst there are no validators for ACL (they might come later). There are exceptions that can be returned or thrown
in your application. This way you can use a standard set of Exceptions to handle ACL failures. For example we use
validation on our API and will catch the AclException to display an error message.

```php
// Add custom validator
ValidateFactory::setHelper('aclOwner', function () {
    // add your custom validator
    return new IsOwner();
});

Validate::aclAuthenticated(\User::current())->validate(\User::get(1));

```

# Validators

## IsArray

Check the value is an array

```php
(new IsArray())
    ->validate([1, 2, 3]); // true
```

## IsArrayOf

Check the array contains only the provided values. Useful for checking enums that are 
allowed multiple values. For only 1 value set [IsOneOf](#isoneof)

```php
$validate = (new IsArrayOf(['yes', 'no', 'maybe']));
$validate->validate(['yes']); // pass
$validate->validate(['no']); // pass
$validate->validate(['yes', 'no']); // pass
$validate->validate(['definitely']); // fail
```

## IsArrayOfShape

Same as [IsShape](#isshape) except the $value must be an array.

## IsArrayOfType

Check the array contains only items of the particular type

Checking against a single type

```php
$validate = (new IsArrayOfType(
    new IsString()
));

$validate->validate(['Hello']); // pass
$validate->validate([1, 'World']); // fail
```

Checking against multiple types

```php
$validate = (new IsArrayOfType([
    new IsString(),
    new IsInteger(),
]));

$validate->validate(['Hello']); // pass
$validate->validate([1, 'World']); // pass
```

## IsBoolean

Check the value is boolean-ish, that is 1, '1', true, 'true' & 0, '0', false, 'false'.

```php
(new IsBoolean())
    ->validate(true); // true
```

## IsDate

Check the value is a valid date in the format provided.

`$options`
- `string format` - the date format the value is expected to be in
- `bool setValueToDateTime` - set the value from the result to the value when the date is valid.
- `bool strict` - using strict will force empty strings to fail


```php
(new IsDate())
    ->validate('2016-01-01'); // true
    

(new IsDate(['format' => 'd/m/Y']))
    ->validate('01/01/2016'); // true    
```

### IsEmailDisposable

Check if the value is a disposable email address like guerillamail.com etc

```php
(new IsEmailDisposable())
    ->validate('bob@guerillamail.com'); // true
```

### IsEmail

Check if the value is a valid email address

```php
(new IsEmail())
    ->validate('support@mizmoz.com'); // true
```

Don‘t allow disposable email addreses 

```php
(new IsEmail(['allowDisposable' => false]))
```

### IsFilter

The filter is a pretty cool helper for parsing strings for filtering.

#### Basic hash tags with example usage

We use the filter to map to column names for things like statuses etc. Only `@tag` & `#tag` are
supported anything else will be returned in the filter key as plain text

```php
$validate = new IsFilter([
    '#active|#deleted' => 'userStatus'
]);

$result = $validate->validate('#deleted')->getValue(); // returns ['userStatus' => ['delete'], 'filter' => '']

$model = User::create();
foreach ($result as $column => $value) {
    // we have some magic attached to our models for filtering also but you get the idea of how this can be used ;)
    $model->where($column, $value);
}
$model->fetch();

```

Special `:isInteger` tagging

```php
$validate = new IsFilter([
    '@:isInteger' => 'userId'
]);

$result = $validate->validate('@123 @456')->getValue(); // returns returns ['userId' => [123, 456], 'filter' => '']
```

The filter value has any tags removed

```php
$validate = new IsFilter([
    '#subscribed' => 'userStatus'
]);

$result = $validate->validate('Bob')->getValue(); // returns ['filter' => 'Bob']

// or with tags

$result = $validate->validate('Bob #subscribed')->getValue(); // returns ['userStatus' => ['subscribed'], 'filter' => 'Bob']
```

Default tags when no tags are present using the *

```php
// active is marked as the default
$validate = new IsFilter([
    '#active*|#inactive' => 'status'
]);

$validate->validate('')->getValue(); // returns ['status' => ['active']]

// Or with a filter
$validate->validate('Bob')->getValue(); // returns ['status' => ['active'], 'filter' => 'Bob']
```

Defaults are for the defined group so you can have other tags without defaults

```php
// active is marked as the default
$validate = new IsFilter([
    '#active*|#inactive' => 'status',
    '#admin|#user' => 'role',
]);

$validate->validate('')->getValue(); // returns ['status' => ['active']]

// Or with a tag
$validate->validate('#admin')->getValue(); // returns ['status' => ['active'], 'role' => ['admin']]
```

## IsInteger

Check the value is an integer

`bool $strict` - set to strict to only allow integers and not number strings or floats.

```php
(new IsInteger())
    ->validate(1); // valid
```
## IsNumeric

Check the value is a number

```php
(new IsNumeric())
    ->validate(1); // valid
    
(new IsNumeric())
    ->validate('100'); // valid
```

### IsObject
### IsOneOf
### IsOneOfType

### IsReCaptcha

Validate a reCAPTCHA response

```php
(new IsReCaptcha($secret))
    ->validate($response);
```

### IsRequired
### IsSame
### IsShape

Check the value is a particular shape, sometimes is easier to explain with an example...

```php
(new IsShape([
    'name' => new IsString(),
    'age' => new IsInteger(),
]))->validate([
    'name' => 'Bob',
    'age' => 45,
]); // valid
```

Shapes can be nested to validate shapes of shapes.

Using the `Validate::set()` provides a helper to return nice descriptions of the shape.

```php
$validate = Validate::set([
    'name' => Validate::isString()
        ->setDescription('Full name')
        ->isRequired(),
]);

// return an array describing the set / shape.
$validate->getDescription();
```

### IsString

Check the value is a string

```php
(new IsString)
    ->validate('Hello world'); // valid
```

## Road map

## On the todo list

- Formalise the API
- Optional descriptions in OpenAPI format: https://github.com/OAI/OpenAPI-Specification
- Create validators as ReactJS components. Parse the description from Chain to form components.
- Add docs for all remaining validators... there are quite a few more than listed here so 
be sure to have a look in the src/Validator directory.
- Add more validators!

#### Tasks

- Create description for isOneOfType

### General

Allow positive or negative matching. Possibly like this:

```php
// Positive match
Validate::is()->email();

// Negative match
Validate::not()->email();
```

### Validators

#### IsPassword

Check a string matches the requirements for the password. 

- Minimum length
- Uppercase characters
- Lowercase characters
- Special characters
- Numbers

### Resolvers

#### ToHash

Create a hash of the given data with various techniques. MD5, SHA1, password_hash etc.

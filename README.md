# www.mizmoz.com / validate

Validation for PHP 7 that tries to suck less.

We've used a lot of different validation libraries over time and I've yet to be truly happy with any of them.

The main aim for this is to create a validator that can handle complex items, resolve them and also create nice 
descriptions of themselves. The dream is to create a validator that can handle REST API data, send useful error
messages to the user and also a nice description of the endpoint. This will be the face of the Mizmoz API.

## On the todo list

- Formalise the API
- Optional descriptions in OpenAPI format: https://github.com/OAI/OpenAPI-Specification
- Create validators as ReactJS components. Parse the description from Chain to form components.
- Add more validators!

# Composer installation

It's probably worth pointing out the API is really new and very likely to change.

```
composer require mizmoz/validate
```

# Basic validation

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
$result = Validate::isSame('me')->toValue(\User::current()->userId)->validate($userId);

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

### IsFilter

The filter is a pretty cool helper for parsing strings for filtering.

#### Basic hash tags with example usage

We use the filter to map to column names for things like statuses etc. Only @tag & #tag are supported anything else
will be returned in the filter key as plain text

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

Special :isInteger tagging

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

$result = $validate->validate('Bob #subscribed')->getValue(); // returns ['userStatus' => 'subscribed', 'filter' => 'Bob']
```
# www.mizmoz.com / validate

Validation that tries to suck less

We've used a lot of different validation libraries over time and I've yet to be truly happy with any of them.

The main aim for this is to create a validator that can handle complex items, resolve them and also create nice 
descriptions of themselves. The dream is to create a validator that can handle REST API data, send useful error
messages to the user and also a nice description of the endpoint. This will be the face of the Mizmoz API.

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
        ->isRequired(),
    'email' => Validate::isEmail()
        ->isRequired(),
    'sendNewsletter' => Validate::isBoolean()
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
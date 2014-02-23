# Apc

Small wrapper of some apc features.

## Instanciate
While you instanciate an object of this class, you must give at least one argument to set the key name:

```php
use \Malenki\Apc;
$apc = new Apc('key_id_you_have_selected'); // TTL = 0 by default here
```

You can give second argument, an positive integer, for the time to live duration in seconds:

```php
use \Malenki\Apc;
$apc = new Apc('key_id_you_have_selected', 60); // Given TTL is one minute
```

## Setting value

Very easy, you have the choice by using `set()` method or the magick setter `value`:

```php
use \Malenki\Apc;
$apc = new Apc('key_id_you_have_selected', 3600);
$apc->set('foo');
//or
$apc->value = 'foo';
```

## Getting value

As you have seen into previous case, you can use for getting value two ways: `get()` method or magic getter `value`.

```php
use \Malenki\Apc;
$apc = new Apc('key_id_you_have_selected', 3600);
var_dump($apc->get());
//or
var_dump($apc->value);
```

You may print content into string context too:

```php
use \Malenki\Apc;
$apc = new Apc('key_id_you_have_selected', 3600);
$apc->value = 'foo';
echo $apc; // will print "foo"
```

If the value is not a scalar, `print_r()` function is used.

## Deleting value

You can force removing value from APC cache using `delete()` method or magic `unset()`:

```php
use \Malenki\Apc;
$apc = new Apc('key_id_you_have_selected', 3600);
$apc->set('foo');
$apc->delete();
// or
unset($apc->value);
```

## Testing value

You can test if a value exists before doing something with it. You have to call `exists()` method or magic `isset()`:

```php
use \Malenki\Apc;
$apc = new Apc('key_id_you_have_selected', 3600);

// using method
if(!$apc->exists())
{
    $apc->value = 'foo';
}

// or magic isset()
if(!isset($apc->value))
{
    $apc->value = 'foo';
}
```


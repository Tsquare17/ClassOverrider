# Tsquare\ClassOverrider

Allows the customization of code that would otherwise be unable to be modified without being possibly overwritten.

Specify an overrides directory outside of the projects codebase, where customized classes can be used instead.

The directory structure in the overrides path should emulate the overridden namespace, beyond the base namespace specified.

Example:
```php
define('CLASSOVERRIDER_PATH', 'overrides_dir');
define('CLASSOVERRIDER_NS', 'Tsquare\\Overriding_Namespace');
define('CLASSOVERRIDER_BASE_NS', 'Tsquare\\Overridden_Namespace');

$foo = new MaybeOverride(Sample::class, 'arg1', 'arg2');
```

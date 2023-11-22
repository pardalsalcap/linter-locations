# Add on to Linter to manage Geolocations

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us


## Installation

You can install the package via composer:

```bash
composer require pardalsalcap/linter-locations
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="linter-locations-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="linter-locations-config"
```

This is the contents of the published config file:

```php
return [
    'available_locales' => [
        'ca' => 'Català',
        'es' => 'Castellano',
        'en' => 'English',
    ],
];
```

You can add a relationship to the User model

```php
use Pardalsalcap\LinterLocations\Models\Address;

public function addresses(): BelongsToMany
{
    return $this->belongsToMany(
    Address::class,  // Address model
    'address_user',  // Pivot table name
    'user_id',       // Foreign key on the pivot table for the User model
    'address_id'     // Foreign key on the pivot table for the Address model
    )->withPivot('address_type');
}
```

Explain how to extend with service provider

## Credits

- [pardalsalcap](https://github.com/pardalsalcap)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.



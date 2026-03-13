# Linter Locations

`pardalsalcap/linter-locations` adds a geographic data model and Filament resources for continents, countries, communities, states, cities, and addresses.

It is designed to work as an add-on for Laravel applications that already use Filament.

## Compatibility

- PHP `^8.2`
- Laravel `^11.0|^12.0`
- Filament `^5.0`

## Installation

Install the package:

```bash
composer require pardalsalcap/linter-locations
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="linter-locations-migrations"
php artisan migrate
```

Publish the config file if you want to adjust locales or search behavior:

```bash
php artisan vendor:publish --tag="linter-locations-config"
```

Default config:

```php
return [
    'available_locales' => [
        'ca' => 'Català',
        'es' => 'Castellano',
        'en' => 'English',
    ],
    'use_scoped_search' => true,
];
```

## Install Command

The package ships with an install command:

```bash
php artisan linter-locations:install
```

The command asks for confirmation before each optional step:

1. Populate the database with starter geographic data.
2. Create the Filament resource wrappers inside your application.

If you confirm the resource step, the command creates these files in `app/Filament/Resources` when they do not already exist:

- `ContinentResource.php`
- `CountryResource.php`
- `CommunityResource.php`
- `StateResource.php`
- `CityResource.php`
- `AddressResource.php`

The generated classes extend the package resources, so you can start using them immediately and still customize them later in your app.

## Filament Resources

The package provides base Filament resources for:

- Continents
- Countries
- Communities
- States
- Cities
- Addresses

The generated wrapper classes are intentionally minimal:

```php
<?php

namespace App\Filament\Resources;

class ContinentResource extends \Pardalsalcap\LinterLocations\Resources\ContinentResource
{
}
```

If you prefer to create them manually, examples are available in `resources/stubs`.

## Database Seeder Data

The install command can preload:

- Continents
- Countries
- Spanish communities
- Spanish states
- Spanish cities

This gives a practical starting point for projects that need location data immediately.

## Traits

### `HasAddresses`

Use `Pardalsalcap\LinterLocations\Traits\HasAddresses` on any Eloquent model that should be linked to addresses.

Example:

```php
use Illuminate\Database\Eloquent\Model;
use Pardalsalcap\LinterLocations\Traits\HasAddresses;

class User extends Model
{
    use HasAddresses;
}
```

Once applied, the model can attach, detach, and sync addresses through the provided relationship helpers.

## Address Relation Manager

The package also includes `AddressableRelationManager`, which can be attached to your own Filament resources when the model uses `HasAddresses`.

Example:

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Pardalsalcap\LinterLocations\Resources\AddressResource\RelationManagers\AddressableRelationManager;

class UserResource extends Resource
{
    public static function getRelations(): array
    {
        return [
            AddressableRelationManager::class,
        ];
    }
}
```

## Translations

You can publish the translation files with:

```bash
php artisan vendor:publish --tag="linter-locations-translations"
```

Published translations will be available under:

```text
resources/lang/vendor/linter-locations
```

The package currently includes translation files for English and Spanish.

## Credits

- [pardalsalcap](https://github.com/pardalsalcap)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).

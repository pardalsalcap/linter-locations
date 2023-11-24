# LinterLocations Documentation

## Overview

The LinterLocations package is a comprehensive solution for managing geolocations within Laravel applications. It offers a structured hierarchy of geographical entities, ranging from continents to specific addresses, and facilitates their management through a database schema. 

This package relies on **FilamentPHP**, a powerful admin panel, for its graphical user interface (GUI). The dependency on FilamentPHP enables a user-friendly experience for managing these geographical entities through a sophisticated and intuitive interface.

## Version Compatibility
### Laravel Compatibility
Our package is designed to work seamlessly with Laravel. Here are the Laravel versions that are compatible with our package:

**Laravel 10.x**

### FilamentPHP Compatibility
Since our package depends on FilamentPHP for its GUI capabilities, it's essential to ensure compatibility. Our package is compatible with the following versions of FilamentPHP:

**FilamentPHP 3.x**

### PHP Version
Additionally, our package requires PHP version 8.1 or higher.

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
    'use_scoped_search' => true,
];
```

### Configuration Details

After publishing the configuration file, you can customize the following settings:

- `available_locales`: Define the list of locales that your application will support. This is crucial for the translation features of the package.
- `use_scoped_search`: This boolean setting enables scoped search functionality. When set to true, searches will be more focused on the json fields.

These settings allow you to tailor the package's behavior to fit the specific needs of your application.


## FilamentPHP Dependency and GUI Management

### Dependency on FilamentPHP

Our package requires the FilamentPHP admin panel for its GUI capabilities. FilamentPHP provides an elegant administrative interface for managing the geographical entities.

The package will install [filamentphp](https://filamentphp.com/) into your application

### Managing Entities

The package provides an intuitive GUI to manage various entities like continents, countries, communities, states, cities, and addresses. This GUI is part of the FilamentPHP admin panel, offering a user-friendly experience for data management.

## Database Structure

### Tables and Fields

#### Continents

- `id`: Primary key.
- `lat` & `lon`: Decimal fields for the geographical coordinates (latitude and longitude).
- `iso`: A two-letter ISO code uniquely identifying the continent.
- `name`: The name of the continent.
- `translations`: JSON field for storing name translations.
- `created_at` & `updated_at`: Timestamps.

#### Countries

- `id`: Primary key.
- `continent_id`: Foreign key to the continents table.
- `lat` & `lon`: Geographical coordinates.
- `iso`: Two-letter ISO country code.
- `iso3`: Three-letter ISO country code.
- `name`: Country name.
- `translations`: JSON field for name translations.
- Timestamps.

#### Communities

- `id`: Primary key.
- `country_id`: Foreign key to the countries table.
- Geographical coordinates, ISO code, name, translations.
- Timestamps.

#### States

- `id`: Primary key.
- `country_id` and `community_id`: Foreign keys.
- Geographical coordinates, ISO code, name, translations.
- Timestamps.

#### Cities

- `id`: Primary key.
- `state_id`: Foreign key to the states table.
- Geographical coordinates, postal code, name, translations.
- Timestamps.

#### Addresses

- `id`: Primary key.
- `city_id`: Foreign key to the cities table.
- Geographical coordinates, postal code, detailed address fields.
- Timestamps.

#### Addressables

- `address_id`: Foreign key to the addresses table.
- `addressable_id` and `addressable_type`: For polymorphic relations.
- `address_type`: Type of address (e.g., 'home', 'work').
- Foreign key constraints.

### Relationships

- **Continents** have many **Countries**.
- **Countries** belong to a **Continent** and have many **Communities**.
- **Communities** belong to a **Country** and have many **States**.
- **States** belong to both a **Country** and a **Community**, and have many **Cities**.
- **Cities** belong to a **State** and have many **Addresses**.
- **Addresses** belong to a **City** and can be associated with various models (users, businesses, etc.) through the **Addressables** polymorphic relationship.

### Usage Notes

- The `translations` field in various tables should be used to store name translations in different languages.
- When inserting data into these tables, ensure that the values, especially for ISO codes and geographical coordinates, are validated according to their respective standards.
- The `addressables` table facilitates a flexible way to associate addresses with different types of entities. It's a polymorphic relationship, allowing for diverse use cases.

## Example Usage

```php
// Example of associating a user with an address
$user = User::find(1);
$address = Address::find(1);

$user->addresses()->attach($address->id, ['address_type' => 'home']);
```

## HasAddresses Trait

### Overview

The `HasAddresses` trait provided by the `pardalsalcap/linter-locations` package is designed to be used with Eloquent models in Laravel applications. It enables models to interact with the `addresses` table, allowing for the association, attachment, and detachment of addresses to any Eloquent model.

### Usage

To use the `HasAddresses` trait, simply include it in any Eloquent model that should have an addressable relationship. This allows the model to manage associated addresses easily.

### Methods

- `addresses()`: A polymorphic many-to-many relationship method that returns the addresses associated with the model.

- `attachAddress(Address $address)`: Attaches an address to the model. If the address is already attached, it won't create a duplicate entry.

- `detachAddress(Address $address)`: Detaches an existing address from the model.

- `syncAddresses(...$addresses)`: Synchronizes the given list of addresses with the model, attaching new ones and detaching those not in the provided list.

### Example

Here's how to use the `HasAddresses` trait in a User model:

```php
use Illuminate\Database\Eloquent\Model;
use Pardalsalcap\LinterLocations\Traits\HasAddresses;

class User extends Model
{
    use HasAddresses;

    // User model methods and properties
}

$user = User::find(1);
$address = Address::find(1);

// Attach an address to the user
$user->attachAddress($address);

// Detach an address from the user
$user->detachAddress($address);

// Sync addresses (attach new ones and detach others)
$addresses = Address::whereIn('id', [1, 2, 3])->get();
$user->syncAddresses($addresses);
```

# Customizing and Translating GUI in Filament

## Overview

The `pardalsalcap/linter-locations` package allows you to manage geographical entities such as continents, countries, etc., via the Filament admin panel. To cater to different languages or specific wording preferences, you can publish and customize the translation files for these entities.

## Publishing Translation Files

To customize the translations for the continent management interface (or any other entity), follow these steps:

1. **Publish Translation Files**:
   Use the following Artisan command to publish the translation files to your application's **resources/lang/vendor/linter-locations** directory:

   ```bash
   php artisan vendor:publish --tag=linter-locations-translations

This command copies the default translation files from the package to your application, allowing you to modify them. 

At the moment we have translations for english, spanish and catalan languages.

### Modify Translations:
Once published, you can find the translation files under resources/lang/vendor/linter-locations. Here's an example of the file structure for continents:

```
/resources
└── /lang
    └── /vendor
        └── /linter-locations
            └── /es
                └── continents.php
```
You can edit these files to change existing translations or add new languages.

### Example: Customizing Continent Translations
Here's a snippet from the continents.php translation file:
```
<?php

return [
    'navigation' => 'Continentes',
    'model_label' => 'Continente',
    'model_label_plural' => 'Continentes',
    'iso_field' => 'Código ISO2',
    'name_field' => 'Nombre',
    'lat_field' => 'Latitud',
    'lon_field' => 'Longitud',
    'translations_field' => 'Traducción al :l',
    'iso_column' => 'Código ISO',
    'name_column' => 'Nombre',
    'lat_column' => 'Latitud',
    'lon_column' => 'Longitud',
    'navigation_group' => 'Localizaciones',
];
```
Edit these translations as needed. For example, to translate to French, create a fr folder inside **resources/lang/vendor/linter-locations** and duplicate the continents.php file with French translations.

Translation contributions are welcome.

# LinterLocations Installation Command

## Overview

The `linter-locations:install` command provided by the `pardalsalcap/linter-locations` package automates the process of setting up and populating the database with essential geographical data. This includes continents, countries, communities (focusing on Spanish regions), states (Spanish provinces), and cities (Spanish municipalities).
By running this command, you can easily set up your database with a structured hierarchy of geographical data, essential for applications dealing with location-based information.

## Command Usage

To execute the installation command, run the following in your Laravel project:

```bash
php artisan linter-locations:install
```

## What the Command Does
When run, this command performs several key operations:

1. **Load Configuration:** It loads the package's configuration settings, including available locales and the fallback locale.

2. **Database Population:**
   3. Prompts you to confirm if you want to populate the database.
   4. Installs and populates the continents table with data from a JSON file.
   5. Installs and populates the countries table, including a mapping to their respective continents.
   6. Installs Spanish communities, states, and cities by populating the communities, states, and cities tables respectively.
   

# Setting Up Filament GUI for Entity Management

## Overview

To utilize the GUI provided by Filament for managing geographical entities in the `pardalsalcap/linter-locations` package, users must create a resource file for each entity they wish to manage. These resource files should be placed in the `app/Filament/Resources` directory of their Laravel application.

## Creating a Filament Resource

Here's a step-by-step guide to creating a resource file for an entity:

1. **Navigate to the Resources Directory**:
   First, go to the `app/Filament/Resources` directory in your Laravel application. If the directory does not exist, create it.

2. **Create a Resource File**:
   For each entity you want to manage through Filament, create a new PHP class that extends the corresponding resource class from the `pardalsalcap/linter-locations` package.

### Example: Creating a Continent Resource

Here is an example of creating a resource file for the `Continent` entity:

```php
<?php
namespace App\Filament\Resources;

class ContinentResource extends \Pardalsalcap\LinterLocations\Resources\ContinentResource
{
    // Customizations (if any) go here
}
```

You can find examples of all the available resources you can extend in the <package-folder>/resources/stubs

## AddressableRelationManager

### Overview
`AddressableRelationManager` is a custom relation manager from the Linter Locations package, designed to handle address-related functionalities in Filament PHP resources. It is used in conjunction with models that implement the `HasAddresses` trait.

### Features
- **Form Schema**: Provides a form for managing address details including type, street address, number, stair, floor, door, country, state, and city.
- **Table Configuration**: Displays addresses associated with a model in a table view, along with options for creating, editing, attaching, detaching, and deleting addresses.
- **Dynamic Select Fields**: The state and city fields dynamically update based on the selected country.
- **Custom Actions**: Includes actions like creating a new address, attaching an existing address, editing, and deleting.
- **Bulk Actions**: Supports bulk delete actions.

### Usage
To use `AddressableRelationManager` in a Filament resource, ensure that your model uses the `HasAddresses` trait. This trait provides methods to attach, detach, and sync addresses to the model.

In your Filament resource, you can then include `AddressableRelationManager` to manage addresses linked to your model.

### Example
Here's a basic example of how to use `AddressableRelationManager` in a Filament resource:

```php
namespace App\Filament\Resources;

use App\Models\YourModel;
use Pardalsalcap\LinterLocations\Resources\AddressResource\RelationManagers\AddressableRelationManager;

class YourModelResource extends \Filament\Resources\Resource
{
    public static function getRelations(): array
    {
        return [
            AddressableRelationManager::class,
        ];
    }

    // Other resource configurations...
}
```

## Credits

- [pardalsalcap](https://github.com/pardalsalcap)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.



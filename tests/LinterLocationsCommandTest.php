<?php

use Illuminate\Support\Facades\File;

const LINTER_LOCATIONS_RESOURCE_FILES = [
    'ContinentResource.php',
    'CountryResource.php',
    'CommunityResource.php',
    'StateResource.php',
    'CityResource.php',
    'AddressResource.php',
];

beforeEach(function () {
    $resourcePath = app_path('Filament/Resources');

    if (File::isDirectory($resourcePath)) {
        foreach (LINTER_LOCATIONS_RESOURCE_FILES as $file) {
            File::delete($resourcePath.'/'.$file);
        }
    }
});

afterEach(function () {
    $resourcePath = app_path('Filament/Resources');

    if (File::isDirectory($resourcePath)) {
        foreach (LINTER_LOCATIONS_RESOURCE_FILES as $file) {
            File::delete($resourcePath.'/'.$file);
        }
    }
});

it('can create resource wrappers from the install command', function () {
    $this->artisan('linter-locations:install')
        ->expectsConfirmation('Do you want to populate the database?', 'no')
        ->expectsConfirmation('Do you want to create the Filament resources in your application?', 'yes')
        ->assertSuccessful();

    $resourcePath = app_path('Filament/Resources');

    expect(File::exists($resourcePath.'/ContinentResource.php'))->toBeTrue();
    expect(File::exists($resourcePath.'/CountryResource.php'))->toBeTrue();
    expect(File::exists($resourcePath.'/CommunityResource.php'))->toBeTrue();
    expect(File::exists($resourcePath.'/StateResource.php'))->toBeTrue();
    expect(File::exists($resourcePath.'/CityResource.php'))->toBeTrue();
    expect(File::exists($resourcePath.'/AddressResource.php'))->toBeTrue();

    expect(File::get($resourcePath.'/ContinentResource.php'))
        ->toContain('extends \Pardalsalcap\LinterLocations\Resources\ContinentResource');
});

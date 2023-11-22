<?php

namespace Pardalsalcap\LinterLocations\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Pardalsalcap\LinterLocations\Models\City;
use Pardalsalcap\LinterLocations\Models\Community;
use Pardalsalcap\LinterLocations\Models\Continent;
use Pardalsalcap\LinterLocations\Models\Country;
use Pardalsalcap\LinterLocations\Models\State;
use Pardalsalcap\LinterLocations\Traits\LocationsTrait;

class LinterLocationsCommand extends Command
{
    use LocationsTrait;

    public $signature = 'linter-locations:install';

    protected $description = 'Install the LinterLocations package';

    protected array $available_locales = [];

    protected ?string $fallback_locale = null;

    public function handle(): int
    {
        $this->loadConfiguration();
        $this->populateDatabase();

        $this->info('Installation completed.');

        return self::SUCCESS;
    }

    protected function loadConfiguration(): void
    {
        $this->available_locales = config('linter-locations.available_locales');
        $this->fallback_locale = config('app.fallback_locale');
    }

    protected function populateDatabase(): void
    {
        if ($this->confirm('Do you want to populate the database?')) {
            $this->populateContinents();
            $this->populateCountries();
            $this->populateCommunities();
            $this->populateStates();
            $this->populateCities();
        }
    }

    protected function populateContinents(): int
    {
        $this->comment('Installing continents');
        try {
            $check = Continent::count();
            if ($check) {
                throw new Exception('The continents table already has data');
            }

            $continentsData = $this->loadJson('continents.json');

            $continents = [];
            foreach ($continentsData as $iso => $data) {
                $translations = $data['translations'];
                $default = $translations[$this->fallback_locale] ?? $translations['en'];
                $trimmed_translations = collect($translations)->filter(function ($value, $key) {
                    return in_array($key, array_keys($this->available_locales));
                })->all();

                $continents[] = [
                    'iso' => $iso,
                    'name' => $default,
                    'lat' => $this->adaptCoordinates($data['location']['latitude']),
                    'lon' => $this->adaptCoordinates($data['location']['longitude']),
                    'translations' => json_encode($trimmed_translations),
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
            DB::transaction(function () use ($continents) {
                DB::table('continents')->insert($continents);
            });
            $this->info('Continents table populated successfully');

            return self::SUCCESS;
        } catch (Exception $e) {
            // Log the error and rethrow the exception
            Log::error($e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    public function populateCountries(): int
    {
        $this->comment('Installing countries');
        try {
            $check = Country::count();
            if ($check) {
                throw new Exception('The countries table already has data');
            }

            $countries = $this->loadJson('countries.json');
            $countries2continents = $this->loadJson('countries2continents.json');
            $continents_arr = Continent::pluck('id', 'iso');
            $countries_data = [];
            foreach ($countries as $country) {
                $translations = [];
                foreach ($this->available_locales as $key => $value) {
                    $translations[$key] = isset($country['translations'][$this->convertLangISO3toISO2($key)]['common']) ? $country['translations'][$this->convertLangISO3toISO2($key)]['common'] : $country['name']['common'];
                }
                $iso = strtoupper($country['cca2']);
                $iso3 = strtoupper($country['cca3']);
                $continent_iso = $countries2continents[$iso] ?? null;
                $continent_id = $continents_arr[$continent_iso] ?? null;
                $countries_data[] = [
                    'iso' => $iso,
                    'iso3' => $iso3,
                    'name' => $translations[$this->fallback_locale],
                    'translations' => json_encode($translations),
                    'continent_id' => $continent_id,
                    'lat' => $this->adaptCoordinates($country['latlng'][0] ?? null),
                    'lon' => $this->adaptCoordinates($country['latlng'][1] ?? null),
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
            DB::transaction(function () use ($countries_data) {
                DB::table('countries')->insert($countries_data);
            });
            $this->info('Countries table populated successfully');

            return self::SUCCESS;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }

    }

    public function populateCommunities(): int
    {
        $this->comment('Installing spanish communities');
        try {
            $check = Community::count();
            if ($check) {
                throw new Exception('The communities table already has data');
            }

            $spain = Country::where('iso', 'ES')->first();
            if (! $spain) {
                throw new Exception('The country Spain was not found');
            }
            $communities = $this->loadJson('/spain/states.json');
            $arr = [];
            $communities_data = [];
            foreach ($communities as $value) {
                if (! isset($arr[$value['fields']['cod_ccaa']])) {
                    $arr[$value['fields']['cod_ccaa']] = [
                        'name' => $value['fields']['ccaa'],
                        'latitude' => $value['fields']['geo_point_2d'][0] ?? null,
                        'longitude' => $value['fields']['geo_point_2d'][1] ?? null,
                    ];
                }
            }
            foreach ($arr as $key => $value) {
                $translations = [];
                foreach ($this->available_locales as $lang => $lang_value) {
                    $translations[$lang] = $value['name'];
                }
                $communities_data[] = [
                    'iso' => $key,
                    'name' => $value['name'],
                    'translations' => json_encode($translations),
                    'country_id' => $spain->id,
                    'lat' => $this->adaptCoordinates($value['latitude']),
                    'lon' => $this->adaptCoordinates($value['longitude']),
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
            DB::transaction(function () use ($communities_data) {
                DB::table('communities')->insert($communities_data);
            });
            $this->info('Communities table populated successfully');

            return self::SUCCESS;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    public function populateStates(): int
    {
        $this->comment('Installing spanish states');
        try {
            $check = State::count();
            if ($check) {
                throw new Exception('The states table already has data');
            }

            $spain = Country::where('iso', 'ES')->first();
            if (! $spain) {
                throw new Exception('The country Spain was not found');
            }

            $states = $this->loadJson('/spain/states.json');
            $communities = Community::all();
            $arr = [];
            $states_data = [];
            foreach ($states as $value) {
                if (! isset($arr[$value['fields']['codigo']])) {
                    $arr[$value['fields']['codigo']] = [
                        'name' => $value['fields']['provincia'],
                        'community_iso' => $value['fields']['cod_ccaa'],
                        'geo_point_2d' => $value['fields']['geo_point_2d'],
                    ];
                }
            }
            foreach ($arr as $key => $value) {
                $translations = [];
                foreach ($this->available_locales as $lang => $lang_value) {
                    $translations[$lang] = $value['name'];
                }

                $states_data[] = [
                    'iso' => $key,
                    'name' => $value['name'],
                    'translations' => json_encode($translations),
                    'country_id' => $spain->id,
                    'community_id' => $communities->where('iso', $value['community_iso'])->first()->id,
                    'lat' => $this->adaptCoordinates($value['geo_point_2d'][0] ?? null),
                    'lon' => $this->adaptCoordinates($value['geo_point_2d'][1] ?? null),
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
            DB::transaction(function () use ($states_data) {
                DB::table('states')->insert($states_data);
            });
            $this->info('States table populated successfully');

            return self::SUCCESS;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    public function populateCities(): int
    {
        try {
            $check = City::count();
            if ($check) {
                throw new Exception('The cities table already has data');
            }
            $this->comment('Installing spanish cities');
            $cities = $this->loadJson('/spain/cities.json');
            $states = State::all();
            $cities_data = [];
            foreach ($cities as $value) {
                foreach ($value['provinces'] as $provinces) {
                    foreach ($provinces['towns'] as $town) {

                        $translations = [];
                        foreach ($this->available_locales as $key => $value) {
                            $translations[$key] = $town['label'];
                        }
                        $check = City::count();
                        if ($check) {
                            throw new Exception('The cities table already has data');
                        }

                        $spain = Country::where('iso', 'ES')->first();
                        if (! $spain) {
                            throw new Exception('The country Spain was not found');
                        }
                        $cities_data[] = [
                            'state_id' => $states->where('iso', $town['parent_code'])->first()?->id,
                            'name' => $town['label'],
                            'translations' => json_encode($translations),
                            'created_at' => now()->format('Y-m-d H:i:s'),
                            'updated_at' => now()->format('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
            DB::transaction(function () use ($cities_data) {
                DB::table('cities')->insert($cities_data);
            });
            $this->info('Cities table populated successfully');

            return self::SUCCESS;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    public function loadJson($file_name)
    {
        // Construct the path relative to the package's directory
        $path = base_path('vendor/pardalsalcap/linter-locations/resources/datasets/'.$file_name);

        try {
            // Check if the file exists and is readable
            if (! file_exists($path) || ! is_readable($path)) {
                throw new Exception("JSON file not found or not readable: $path");
            }

            // Proceed with reading the file
            $jsonContent = file_get_contents($path);

            return json_decode($jsonContent, true);
        } catch (Exception $e) {
            // Log the error and rethrow the exception
            Log::error("Error loading JSON file: $file_name, Error: ".$e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    public function convertLangISO3toISO2($iso2): string
    {
        $arr = [
            'en' => 'eng',
            'ca' => 'cat',
            'es' => 'spa',
            'de' => 'deu',
            'it' => 'ita',
            'fr' => 'fra',
        ];

        return $arr[$iso2] ?? 'eng';
    }
}

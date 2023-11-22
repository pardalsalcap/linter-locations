<?php

namespace Pardalsalcap\LinterLocations\Traits;

trait HasTranslations
{
    public function translate($locale): string
    {
        if (! is_array($this->translations)) {
            return '';
        }
        if (isset($this->translations[$locale])) {
            return $this->translations[$locale];
        }
        if (isset($this->translations[config('app.fallback_locale')])) {
            return $this->translations[config('app.fallback_locale')];
        }

        return $this->name;
    }
}

<?php

namespace Addgod\TranslationManager;

use Addgod\TranslationManager\Models\Translation;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Translation\Translator as LaravelTranslator;

class Translator extends LaravelTranslator
{
    /** @var Dispatcher */
    protected $events;

    /**
     * Get the translation for the given key.
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @return string
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        // Get without fallback
        $result = parent::get($key, $replace, $locale, false);
        if ($result === $key) {
            $this->notifyMissingKey($key);

            // Reget with fallback
            $result = parent::get($key, $replace, $locale, $fallback);
        }

        return $result;
    }

    /**
     * Load the specified language group.
     *
     * @param  string  $namespace
     * @param  string  $group
     * @param  string  $locale
     * @return array
     */
    public function load($namespace, $group, $locale)
    {
        if ($this->isLoaded($namespace, $group, $locale)) {
            return $this->loaded;
        }

        // First try to load lines from the databse, if that failes, then use
        // the standard loader.
        try {
            $lines = Translation::whereNamespace($namespace)->whereGroup($group)->whereLocale($locale)->get(['key', 'value'])->pluck('value', 'key')->toArray();

            // Make the lines into a nested array again.
            if (!empty($lines)) {
                $array = [];
                foreach ($lines as $key => $value) {
                    if ($value) {
                        Arr::set($array, $key, $value);
                    }
                }
                $lines = $array;
            }

            // Merge the two translations storage types, one from DB one from files, where the DB entrys are preferred.
            $lines = array_replace_recursive($this->loader->load($locale, $group, $namespace), $lines);

            $this->loaded[$namespace][$group][$locale] = $lines;
        } catch (\Exception $e) {
            // This could happen, if the module is loaded, but the migrations are not run. So do nothing.
        }

        return $this->loaded;
    }

    public function setTranslationManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    protected function notifyMissingKey($key)
    {
        list($namespace, $group, $item) = $this->parseKey($key);
        if ($this->manager && $namespace === '*' && $group && $item) {
            $this->manager->missingKey($namespace, $group, $item);
        }
    }
}

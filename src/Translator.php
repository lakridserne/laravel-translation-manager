<?php namespace Addgod\TranslationManager;

use Addgod\TranslationManager\Models\Translation;
use Illuminate\Translation\Translator as LaravelTranslator;
use Illuminate\Events\Dispatcher;

class Translator extends LaravelTranslator {

    /** @var  Dispatcher */
    protected $events;

    /**
     * Get the translation for the given key.
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @return string
     */
    public function get($key, array $replace = array(), $locale = null, $fallback = true)
    {
        // Get without fallback
        $result = parent::get($key, $replace, $locale, false);
        if($result === $key){
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
            return;
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
                        array_set($array, $key, $value);
                    }
                }
                $lines = $array;
            }

            // The loader is responsible for returning the array of language lines for the
            // given namespace, group, and locale. We'll set the lines in this array of
            // lines that have already been loaded so that we can easily access them.
            if (empty($lines)) {
                $lines = $this->loader->load($locale, $group, $namespace);
            }

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
        if($this->manager && $namespace === '*' && $group && $item ){
            $this->manager->missingKey($namespace, $group, $item);
        }
    }

}

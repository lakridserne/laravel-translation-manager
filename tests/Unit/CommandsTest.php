<?php

namespace Lakridserne\TranslationManager\Tests\Unit;

use Lakridserne\TranslationManager\Models\Translation;
use Lakridserne\TranslationManager\Tests\TestCase;

class CommandsTest extends TestCase
{
    public function testImportTranslation()
    {
        $this->artisan('translations:import');
        $this->assertGreaterThan(0, Translation::all()->count());
    }

    public function testExportTranslations()
    {
        Translation::create([
            'namespace' => 'test',
            'locale'    => 'da',
            'group'     => 'test',
            'key'       => 'test',
            'value'     => 'test',
        ]);

        $this->artisan('translations:export', ['group' => '*']);
        $this->assertFileExists($this->app['path.lang'] . '/da/test.php');
    }
}

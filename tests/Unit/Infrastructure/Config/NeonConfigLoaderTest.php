<?php

namespace Pull\Tests\Unit\Infrastructure\Config;

use Pull\Infrastructure\Config\NeonConfigLoader;
use PHPUnit\Framework\TestCase;
use function getenv;

class NeonConfigLoaderTest extends TestCase
{
    private NeonConfigLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new NeonConfigLoader();
    }

    public function testLoad(): void
    {
        $neonData = <<<NEON
        projectDir: "~/projects"
        maxDepth: 5
        NEON;

        $config = $this->loader->load($neonData);

        $this->assertEquals(getenv('HOME') . '/projects', $config->projectDir());
        $this->assertEquals(5, $config->maxDepth());
    }
}

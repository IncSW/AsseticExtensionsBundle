<?php

declare(strict_types = 1);

namespace AsseticExtensionsBundle\Test\Filter;

use IncSW\AsseticExtensionsBundle\Assetic\Filter\BabelFilter;
use Assetic\Asset\StringAsset;

/**
 * Class BabelFilterTest
 *
 * @package AsseticExtensionsBundle\Test\Filter
 */
final class BabelFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \IncSW\AsseticExtensionsBundle\Assetic\Filter\BabelFilter
     */
    private $filter;

    /**
     *
     * @return void
     */
    protected function setUp()
    {
        $this->filter = new BabelFilter('node', __DIR__ . '/../Resources/.babelrc', [__DIR__ . '/../../node_modules']);
    }

    /**
     *
     * @return void
     */
    public function testFilterLoad()
    {
        $asset = new StringAsset(file_get_contents(__DIR__ . '/../Resources/in.js'));
        $asset->load();
        $this->filter->filterLoad($asset);
        $this->assertEquals(file_get_contents(__DIR__ . '/../Resources/out.js'), $asset->getContent(), '->filterLoad() parses the content');
    }
}

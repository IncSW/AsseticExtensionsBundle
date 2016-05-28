<?php

declare(strict_types = 1);

namespace IncSW\AsseticExtensionsBundle\Assetic\Filter;

use Assetic\Exception\FilterException;
use Assetic\Filter\BaseNodeFilter;
use Assetic\Util\FilesystemUtils;
// Interfaces
use Assetic\Asset\AssetInterface;

/**
 * Class BabelFilter
 *
 * @package IncSW\AsseticExtensionsBundle\Assetic\Filter
 */
final class BabelFilter extends BaseNodeFilter
{

    /**
     *
     * @var string
     */
    private $nodeBin;

    /**
     *
     * @var string|null
     */
    private $config;

    /**
     *
     * @var string|null
     */
    private $executableTemplate;

    /**
     * BabelFilter constructor.
     *
     * @param string $nodeBin
     * @param string|null $config
     * @param array $nodePaths
     */
    public function __construct(string $nodeBin = '/usr/bin/node', string $config = null, array $nodePaths = [])
    {
        $this->nodeBin = $nodeBin;
        $this->config = $config;
        $this->setNodePaths($nodePaths);
        $this->executableTemplate = <<<'EOF'
var babel = require('babel-core');

babel.transformFile('%s', {
    extends: '%s' || null
}, function(error, result) {
    if (error) {
        console.log(error);
        process.exit(2);
    }
    console.log(result.code);
});
EOF;
    }

    /**
     *
     * @param \Assetic\Asset\AssetInterface $asset
     *
     * @return void
     *
     * @throws \RuntimeException
     * @throws \Assetic\Exception\FilterException
     */
    public function filterLoad(AssetInterface $asset)
    {
        $input = FilesystemUtils::createTemporaryFile('babel_in');
        $executable = FilesystemUtils::createTemporaryFile('babel_executable');

        file_put_contents($input, $asset->getContent());
        file_put_contents($executable, sprintf($this->executableTemplate, $input, $this->config));

        $processBuilder = $this->createProcessBuilder([
            $this->nodeBin,
            $executable
        ]);
        $process = $processBuilder->getProcess();
        $code = $process->run();

        unlink($input);
        unlink($executable);

        if ($code !== 0) {
            throw FilterException::fromProcess($process)
                ->setInput($asset->getContent())
            ;
        }

        $asset->setContent($process->getOutput());
    }

    /**
     *
     * @param \Assetic\Asset\AssetInterface $asset
     *
     * @return void
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}

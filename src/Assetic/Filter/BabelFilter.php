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
var fs = require('fs');

babel.transformFile('%s', {
    extends: '%s' || null
}, function(error, result) {
    error && process.exit(2);
    fs.writeFileSync('%s', result.code);
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
        $output = FilesystemUtils::createTemporaryFile('babel_out');

        file_put_contents($input, $asset->getContent());
        file_put_contents($executable, sprintf($this->executableTemplate, $input, $this->config, $output));

        $processBuilder = $this->createProcessBuilder([
            $this->nodeBin,
            $executable
        ]);
        $process = $processBuilder->getProcess();
        $code = $process->run();

        unlink($input);
        unlink($executable);

        if ($code !== 0) {
            if (file_exists($output)) {
                unlink($output);
            }
            throw FilterException::fromProcess($process)
                ->setInput($asset->getContent())
            ;
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $compiledJs = file_get_contents($output);
        unlink($output);

        $asset->setContent($compiledJs);
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

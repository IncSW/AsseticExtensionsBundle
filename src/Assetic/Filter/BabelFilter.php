<?php

declare(strict_types = 1);

namespace Incsw\AsseticExtensionsBundle\Assetic\Filter;

use Assetic\Exception\FilterException;
use Assetic\Filter\BaseNodeFilter;
use Assetic\Util\FilesystemUtils;
// Interfaces
use Assetic\Asset\AssetInterface;

/**
 * Class BabelFilter
 *
 * @package Incsw\AsseticExtensionsBundle\Assetic\Filter
 */
final class BabelFilter extends BaseNodeFilter
{

    /**
     *
     * @var string
     */
    private $babelBin;

    /**
     *
     * @var string|null
     */
    private $nodeBin;

    /**
     *
     * @var string|null
     */
    private $config;

    /**
     * BabelFilter constructor.
     *
     * @param string $babelBin
     * @param string|null $nodeBin
     * @param string|null $config
     */
    public function __construct(string $babelBin = '/usr/bin/babel', string $nodeBin = null, string $config = null)
    {
        $this->babelBin = $babelBin;
        $this->nodeBin = $nodeBin;
        $this->config = $config;
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
        $processBuilder = $this->createProcessBuilder($this->nodeBin ? [$this->nodeBin, $this->babelBin] : [$this->babelBin]);

        $inputDir = FilesystemUtils::createThrowAwayDirectory('babel_in');
        $input = tempnam($inputDir, 'assetic') . '.js';
        $output = FilesystemUtils::createTemporaryFile('babel_out');
        if ($this->config !== null) {
            copy($this->config, $inputDir . '/' . basename($this->config));
        }

        file_put_contents($input, $asset->getContent());
        $processBuilder->add($input)
            ->add('--out-file')
            ->add($output)
        ;

        $process = $processBuilder->getProcess();
        $code = $process->run();
        FilesystemUtils::removeDirectory($inputDir);

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

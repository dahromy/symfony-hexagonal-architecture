<?php


namespace App\Infrastructure\Bridge\InFile;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FilesystemHandler
{
    private Filesystem $filesystem;
    private Finder $finder;
    private string $rootDir;

    /**
     * FilesystemHandler constructor.
     *
     * @param Filesystem $filesystem
     * @param string $rootDir
     */
    public function __construct(Filesystem $filesystem, string $rootDir)
    {
        $this->filesystem = $filesystem;
        $this->finder = Finder::create();
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $filename
     * @param string $content
     *
     * @return void
     */
    public function createFile(string $filename, string $content): void
    {
        $this->ensureRootFolderExists();
        $this->filesystem->dumpFile("$this->rootDir/$filename", $content);
    }

    /**
     * @return void
     */
    private function ensureRootFolderExists(): void
    {
        if (!$this->filesystem->exists($this->rootDir)) {
            $this->filesystem->mkdir($this->rootDir);
        }
    }

    public function readFile(string $filename): ?string
    {
        $this->ensureRootFolderExists();
        $files = $this->finder->files()->in($this->rootDir)->name($filename);
        $content = null;
        foreach ($files as $file) {
            $content = $file->getContents();
            break;
        }

        return $content;
    }
}

<?php


namespace App\Infrastructure\Persistence\InFile;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FilesystemHandler
{
    private Filesystem $filesystem;
    private Finder $finder;
    private string $rootDir;

    /**
     * FilesystemHandler constructor.
     * @param Filesystem $filesystem
     * @param string $rootDir
     */
    public function __construct(Filesystem $filesystem, string $rootDir)
    {
        $this->filesystem = $filesystem;
        $this->finder = Finder::create();
        $this->rootDir = $rootDir;
    }

    public function createFile(string $filename, string $content)
    {
        $this->ensureRootFolderExists();
        $this->filesystem->dumpFile("{$this->rootDir}/{$filename}", $content);
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

    private function ensureRootFolderExists()
    {
        if (!$this->filesystem->exists($this->rootDir)) {
            $this->filesystem->mkdir($this->rootDir);
        }
    }
}

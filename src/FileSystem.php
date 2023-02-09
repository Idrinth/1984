<?php

namespace De\Idrinth\Project1984;

final class FileSystem
{
    private string $in;
    private string $home;
    private string $source;
    private string $target;

    public function __construct(string $in, string $out)
    {
        $this->in = $in;
        $this->home = "$out/home";
        $this->source = "$out/source";
        $this->target = "$out/target";
    }

    private function remove(string $path): void
    {
        if (is_dir($path)) {
            foreach (array_diff(scandir($path), ['.', '..']) as $file) {
                $this->remove("$path/$file");
            }
            rmdir($path);
        } elseif (is_file($path)) {
            unlink($path);
        }
    }
    private function provideEmpty(string $path): void
    {
        $this->remove($path);
        mkdir($path, 0777, true);
    }
    public function clearOut(): void
    {
        $this->provideEmpty($this->home);
        $this->provideEmpty($this->target);
        $this->provideEmpty($this->source);
    }
    public function read(string $name, string $type, bool $strip = true): string
    {
        $data = file_get_contents("{$this->in}/$name.$type") ?: '';
        if ($strip) {
            switch ($type) {
                case 'php':
                    return substr($data, 6);
                case 'sh':
                    return substr($data, 10);
            }
        }
        return $data;
    }
    public function md5Source(string $file): string
    {
        return md5_file("{$this->source}/$file");
    }
    public function writeTarget(string $name, string $content): void
    {
        file_put_contents("{$this->target}/$name", $content);
    }
    public function writeSource(string $name, string $content): void
    {
        file_put_contents("{$this->source}/$name", $content);
    }
    public function writeHome(string $name, string $content): void
    {
        file_put_contents("{$this->home}/$name", $content);
    }
}

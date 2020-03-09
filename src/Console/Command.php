<?php

namespace Huztw\Admin\Console;

trait Command
{
    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__ . "/stubs/$name.stub");
    }

    /**
     * Make new directory.
     *
     * @param string $path
     */
    protected function makeDir($path = '')
    {
        $this->laravel['files']->makeDirectory("{$this->directory}/$path", 0755, true, true);

        $dirname = (!empty($path) && '/' != $path) ? $path : basename($this->directory);

        $this->line('<info>' . ucfirst($dirname) . ' directory was created:</info> ' . preg_replace('/\/|\\\\/', DIRECTORY_SEPARATOR, str_replace(base_path(), '', $this->directory)));
    }

    /**
     * Make new file.
     *
     * @param string $path
     */
    protected function makefile($file, $contents, $name = null)
    {
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));

        $filename = $name ?: basename($file, ".php");

        $this->line('<info>' . ucfirst($filename) . ' file was created:</info> ' . preg_replace('/\/|\\\\/', DIRECTORY_SEPARATOR, str_replace(base_path(), '', $file)));
    }
}

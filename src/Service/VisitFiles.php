<?php

namespace App\Service;

class File
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}

class Directory
{
    /**
     * @param string $name
     * @param (File|Directory)[] $children
     */
    public function __construct(
        public readonly string $name,
        public readonly array $children,
    ) {
    }
}

class VisitFiles
{
    /**
     * Traverse Files & Directories.
     *
     * Return a list of files filtered by given function.
     *
     * @param Directory $root
     * @param ?callable $filterFn
     *
     * @return array
     */
    public function visitFiles(Directory $root, ?callable $filterFn = null): array
    {
        $result = array();

        foreach ($root->children as $child) {
            if ($filterFn($child)) {
                $result[] = match (get_class($child)) {
                    File::class => $child->name,
                    Directory::class => [
                        $child->name => $this->visitFiles($child, $filterFn)
                    ]
                };
            }
        }

        return $result;
    }

    /**
     * @return array
     * returns [
     *          [
     *              "abba" => []
     *          ],
     *          "radar"
     *       ]
     */
    public function usageExemple(): array
    {
        // example: a directory containing one empty directory and two files, one with a palindromic name
        $childDir = new Directory('abba', []);
        $childFile = new File('radar');
        $childFile2 = new File('test');
        $root = new Directory('root', [
            $childDir,
            $childFile,
            $childFile2
        ]);

        return $this->visitFiles(
            $root,
            // the callback checks if name of element is a palindrome
            function ($file) {
                $name = $file->name;
                for ($i = 0; $i < floor(strlen($name)); $i++) {
                    if ($name[$i] != $name[strlen($name) - $i - 1]) {
                        return false;
                    }
                }
                return true;
            }
        );
    }
}

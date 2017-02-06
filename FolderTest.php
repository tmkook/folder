<?php
// Folder Tester
// First chmod -rf 777 ./
// phpunit FolderTest.php
require 'src/Folder.php';
require 'src/FolderInfo.php';
class FolderTest extends PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $a = new Tmkook\Folder();
        $a->open(dirname(__FILE__));
        $create = $a->create('test');
        $this->assertDirectoryExists($a->getDirectory().'test');

        $a->entry('test');
        $create = $a->create('subtest');
        $this->assertDirectoryExists($a->getDirectory().'subtest');
        $add = $a->addFile('example.txt', 'testing...');
        $this->assertFileExists($a->getDirectory().'example.txt');

        $a->entry('subtest');
        $add = $a->addFile('subexample.txt', 'subtesting...');
        $this->assertFileExists($a->getDirectory().'subexample.txt');
    }

    public function testGet()
    {
        $a = new Tmkook\Folder();
        $a->open(dirname(__FILE__));
        $a->entry('test');

        $a->entry('subtest');
        $this->assertEquals('subtest', $a->getName());

        $a->back();
        $this->assertEquals('test', $a->getName());

        $folders = $a->getFolders();
        $this->assertCount(1, $folders);

        $subfolders = $a->getSubFolders();
        $this->assertCount(1, $subfolders);

        $files = $a->getFiles();
        $this->assertCount(1, $files);

        $subfiles = $a->getSubFiles();
        $this->assertCount(2, $subfiles);
    }

    public function testMove()
    {
        $a = new Tmkook\Folder();
        $a->open(dirname(__FILE__));
        $a->entry('test');

        $mvto = dirname($a->getDirectory());
        $a->move('subtest', $mvto);
        $this->assertDirectoryExists($mvto.'/subtest');

        $a->back();
        $a->move('subtest', $a->getDirectory().'test/');
        $this->assertDirectoryExists($a->getDirectory().'test/subtest');

        $a->entry('test');
        $a->move('example.txt', $a->getDirectory().'subtest/');
        $this->assertFileExists($a->getDirectory().'subtest/example.txt');
    }

    public function testFolderInfo()
    {
        $a = new Tmkook\Folder();
        $a->open(dirname(__FILE__));
        $a->entry('test/subtest');
        $files = $a->getFiles();

        $sort = new Tmkook\FolderInfo($files);
        $list = $sort->get();
        $this->assertCount(2, $list);

        $list = $sort->orderByNameDesc()->get();
        $this->assertEquals('subexample.txt', $list[0]['name']);

        $list = $sort->orderByNameAsc()->get();
        $this->assertEquals('example.txt', $list[0]['name']);
    }

    public function testRemove()
    {
        $a = new Tmkook\Folder();
        $a->open(dirname(__FILE__));
        $a->entry('test/subtest');
        $a->delFile('example.txt');
        $this->assertFileNotExists($a->getDirectory().'example.txt');

        $a->back();
        $a->back();
        $a->remove('test', true);
        $this->assertDirectoryNotExists($a->getDirectory().'test');
    }
}

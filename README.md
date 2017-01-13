# Folder Handle

文件夹操作助手

```
$folder = new Folder;
$folder->open('./folder');
$folder->getFiles();
$folder->getSubFiles(); //recursive
$folder->getFolders();
$folder->getSubFolders(); //recursive
$folder->create('newfolder');
$folder->remove('removefolder',true); //true = rm -rf dir
$folder->rename('oldname','newname');
```

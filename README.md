# 安装

在 composer.json 中添加

```
"require": {
    "tmkook/folder": "*"
}
```

# 使用

### 打开文件夹

```
$folder = new Markbox\Folder;
$folder->open('./folder'); //打开 folder
$folder->entry('foo'); //进入 folder/foo
$folder->back(); //退回 folder
```

### 获取文件列表

```
$folder->getName(); //返回当前文件夹名称
$folder->getDirectory(); //返回当前文件夹路径
$folder->getFiles(); //获取子文件
$folder->getSubFiles(); //递归获取子文件
$folder->getFolders(); //获取子文件夹
$folder->getSubFolders(); //递归获取子文件夹
```

### 操作文件夹

```
$folder->create('newfolder'); //新建文件夹
$folder->remove('newfolder',true); //删除文件夹 true = rm -rf dir
$folder->rename('oldname','newname'); //重命名文件夹
$folder->move('oldname','../test/'); //移动文件夹
```

### 操作文件

```
$folder->addFile('example.txt','test'); //添加文件
$folder->delFile('example.txt'); //删除文件
$folder->move('example.txt','../test/'); //移动文件
```

### 排序与文件信息

```
$files = $folder->getFiles();
$sort = new FolderInfo($files);
$files = $sort->orderByNameDesc()->get();
$files = $sort->setOrder('name','asc')->get();
//setOrder(mtime|ctime|atime|name,asc|desc);
```

### 异常

```
try{
  ...
}catch(Markbox\FolderException $e){
  $e->getMessage();
}
```
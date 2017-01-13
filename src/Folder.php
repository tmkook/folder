<?php namespace Markbox;
/**
* 类 Folder 提供了目录文件的基本操作。
*
* @link http://github.com/tmkook/markbox
*
* @copyright (c) 2016 tmkook.
* @license MIT
*
* @version $Id: Folder.php
*/
class Folder
{
	/**
	* 当前打开的文件夹路径
	*
	* @param $directory string
	*/
	private $directory = './';
	
	/**
	* 打开指定路径的文件夹
	*
	* @param $path string
	* @return boolen
	*/
	public function open($path){
        if ( ! is_dir($path)) {
            throw new FolderException("not found dir {$path}");
        }
		$path = realpath($path);
        $this->directory = '/'.trim($path, '/').'/';
		return true;
	}
	
	/**
	* 进入当前文件夹下指定的子文件夹
	*
	* @param $name string
	* @return boolen
	*/
	public function entry($name){
		$path = $this->directory . $name;
		return $this->open($path);
	}
	
	/**
	* 返回到当前文件夹的上一层文件夹
	*
	* @return boolen
	*/
	public function back(){
		$path = dirname($this->directory);
		return $this->open($path);
	}
	
	/**
	* 获取当前的文件夹名
	*
	* @return string
	*/
	public function getName(){
		$dir = dirname($this->directory);
        $name = trim(str_replace($dir,'',$this->directory),'/');
        if(empty($name)){
            return '.';
        }
        return $name;
	}
	
	/**
	* 获取当前的文件夹的绝对路径
	*
	* @return string
	*/
	public function getDirectory(){
        return $this->directory;
	}
	
	/**
	* 获取当前文件夹下的文件夹
	*
	* @return array
	*/
	public function getFolders(){
		$mod = GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT;
		$directories = glob($this->directory .'*', $mod);
		return  $directories;
	}
	
	/**
	* 递归获取当前文件夹下的所有子文件夹
	*
	* @return boolen
	*/
	public function getSubFolders(){
		return $this->recursiveFolders(array(),$this->directory);
	}
	
	/**
	* 递归获取当前文件夹下的所有子文件夹
	*
	* @param $directories array 获取到的目录
	* @param $path string 需要递归的路径
	* @return array
	*/
	private function recursiveFolders($directories,$path){
		$mod = GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT;
		$folders = glob($path . '*', $mod);
		
		foreach($folders as $dir){
			$directories[] = $dir;
			$directories = $this->recursiveFolders($directories,$dir);
		}
		return $directories;
	}
	
	/**
	* 获取当前文件夹下的文件
	*
	* @param $suffix string 文件后缀名
	* @return array
	*/
	public function getFiles($suffix='*.*'){
		$mod = GLOB_NOSORT;
		$files = glob($this->directory . $suffix,$mod);
		return $files;
	}
	
	/**
	* 递归获取当前文件夹下的所有文件
	*
	* @param $suffix string 文件后缀名
	* @return array
	*/
	public function getSubFiles($suffix='*.*'){
		$folders = $this->getSubFolders();
		$mod = GLOB_NOSORT;
		$files = array();
		foreach($folders as $dir){
			$subfiles = glob($dir.$suffix,$mod);
			$files = array_merge($files,$subfiles);
		}
		return $files;
	}
	
	/**
	* 在当前文件夹下新建文件夹
	*
	* @param $name string 新建的文件夹名称
	* @param $chmod integer 目录权限,参考Linux
	* @return boolen
	*/
	public function create($name,$chmod=0777){
		$name = trim($name, '/');
        if (empty($name)) {
            throw new FolderException('create name error');
        }
        $name = $this->directory . $name . '/';
        if (is_dir($name)) {
            throw new FolderException("dir {$name} exists",101);
        }

        return @mkdir($name, $chmod);
	}
	
	/**
	* 删除当前文件夹下的文件夹
	*
	* @param $name string 要删除的文件夹名称
	* @return boolen
	*/
	public function remove($name,$clean=false){
		$name = trim($name, '/');
		$path = $this->directory . $name;
		if(!is_dir($path)){
			throw new FolderException("not found dir",101);
		}
		$this->entry($name);
		if($clean){
			$files = array_merge($this->getFiles(),$this->getSubFiles());
			krsort($files);
			foreach($files as $file){
				unlink($file);
			}
			$folders = $this->getSubFolders();
			krsort($folders);
			foreach($folders as $folder){
				rmdir($folder);
			}
		}
		
		return rmdir($path);
    }
	
	/**
	* 重命名当前文件夹下的文件夹名称
	*
	* @param $oldname string 需重命名的文件夹
	* @param $newname string 文件夹的新名称
	* @return boolen
	*/
    public function rename($oldname, $newname){
        $oldname = trim($oldname, '/');
        $newname = trim($newname, '/');
        if (empty($oldname)) {
            throw new FolderException('oldname is error');
        }
        if (empty($newname)) {
            throw new FolderException('newname is error');
        }
        $oldname = $this->directory . $oldname;
        $newname = $this->directory . $newname;
        if (!is_dir($oldname) && !file_exists($oldname)) {
            throw new FolderException('oldname not exists');
        }

        return @rename($oldname, $newname);
    }
	
	/**
	* 在当前文件夹下添加文件
	*
	* @param $name string 添加的文件名
	* @param $body string 添加的文件数据
	* @return boolen
	*/
    public function addFile($name, $body){
        $name = trim($name, '/');
        $name = $this->directory . $name;

        return (bool) file_put_contents($name, $body);
    }
	
	/**
	* 删除当前文件夹的文件
	*
	* @param $name string 要删除的文件名
	* @return boolen
	*/
    public function delFile($name){
        $name = trim($name, '/');
        $name = $this->directory . $name;
        if ( ! file_exists($name)) {
            return true;
        }

        return unlink($name);
    }
	
	public function moveFile($file,$to){
		
	}
	
	public function move($folder,$to){
		
	}
	
	
}

//异常类
class FolderException extends \Exception{}

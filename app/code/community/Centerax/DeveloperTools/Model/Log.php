<?php

class Centerax_DeveloperTools_Model_Log
{

	public function fetchFiles()
	{
		$logFiles = array();

		foreach (new DirectoryIterator($this->_getLogDir()) as $fileInfo) {
		    if($fileInfo->isDot()){
		    	continue;
		    }

			if(preg_match('/[(.log)(.logs)]$/', $fileInfo->getFilename())){
				$logFiles [] = array('file' => $fileInfo->getPathname(), 'filename'=>$fileInfo->getFilename());
			}
		}

		return $logFiles;
	}

	protected function _getLogDir()
	{
		return Mage::getBaseDir('var').DS.'log'.DS;
	}
}
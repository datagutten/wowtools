<?php
class tabard
{
	public $remotepath = 'http://eu.battle.net/wow/static/images/guild/tabards/';
	public $filenames;
	public $guild;
	function __construct($guild)
	{
		$this->guild=$guild;
	}


	private function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') 
	{
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6)
		{ //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		}
		else
			return false; //Invalid hex color code

		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
	}
	private function simpleimagecopy($dst_im,$src_im,$dst_x,$dst_y)
	{
		imagecopy($dst_im,$src_im,$dst_x,$dst_y,0,0,imagesx($src_im),imagesy($src_im));
	}
	private function color($im,$backgroundColor)
	{
		if(strlen($backgroundColor)==8)
			$backgroundColor=substr($backgroundColor,2);
		$c1=$this->hex2RGB($backgroundColor);
		imagefilter($im, IMG_FILTER_COLORIZE, $c1['red'], $c1['green'], $c1['blue']);	
	}
	private function filenames($remotepath,$localpath=false)
	{
		$rings = array('0'=>'ring-alliance.png','1'=>'ring-horde.png');
		$basefilenames=array('ring'=>$rings[$this->guild['side']],
						'shadow'=>'shadow_00.png','bg'=>'bg_00.png',
						'overlay'=>'overlay_00.png',
						'border'=>'border_'.str_pad($this->guild['emblem']['border'],2,'0',STR_PAD_LEFT).'.png',
						'emblem'=>'emblem_'.str_pad($this->guild['emblem']['icon'],2,'0',STR_PAD_LEFT).'.png',
						'hooks'=>'hooks.png');
		if(substr($remotepath,-1,1)!='/')
			$remotepath.='/';
		if($localpath!==false)
		{
			if(substr($localpath,-1,1)!='/')
				$localpath.='/';	
			if(!file_exists($localpath)) //Create the cache folder
				mkdir($localpath);
		}
		foreach($basefilenames as $key=>$file)
		{
			if($localpath!==false) //Check if files should be cached
			{
				if(!file_exists($localpath.$file))
					copy($remotepath.$file,$localpath.$file);
				$files[$key]=$localpath.$file;
			}
			else
				$files[$key]=$remotepath.$file; //No caching, return the remote address
		}
		return $files;
	}

	public function maketabard()
	{
		$guild=$this->guild;
		//Create images
		foreach($this->filenames($this->remotepath,'tabardcache') as $key=>$file) //Create image resources
		{
			$im[$key]=imagecreatefrompng($file);
		}

		$tabard=imagecreatetruecolor(240,240);
		imagefill($tabard,0,0,0xFFFFFF);

		$this->simpleimagecopy($tabard,$im['ring'],0,0); //Ring

		$this->color($im['shadow'],$guild['emblem']['backgroundColor']);
		$this->simpleimagecopy($tabard,$im['shadow'],18,27); //Shadow (base tabard)

		$this->color($im['bg'],$guild['emblem']['backgroundColor']);
		$this->simpleimagecopy($tabard,$im['bg'],18,27); //Background

		$this->simpleimagecopy($tabard,$im['overlay'],18,27); //Overlay (shadow)

		$this->color($im['border'],$guild['emblem']['borderColor']);
		$this->simpleimagecopy($tabard,$im['border'],31,40); //Border


		$this->color($im['emblem'],$guild['emblem']['iconColor']);
		$this->simpleimagecopy($tabard,$im['emblem'],33,57); //Emblem

		$this->simpleimagecopy($tabard,$im['hooks'],18,27); //Hooks

		return $tabard;
	}
}
?>
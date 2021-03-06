<?php
class Log{
	private static $logpath=LOG_DIR;

	public static function out($strFileName="",$strType="I",$strMSG="",$strExtra="",$line=""){
		if($strType=="")
			$strType = "I";

		if(!file_exists(self::$logpath)){
			if(!mkdir(self::$logpath,'0777')){
				if(DEBUG_MODE){
					die(Tools::displayError("Make ".self::$logpath." error"));
				}
				else{
					die("error");
				}
			}
		}
		elseif(!is_dir(self::$logpath)){
			if(DEBUG_MODE){
				die(Tools::displayError(self::$logpath." is already token by a file"));
			}
			else{
				die("error");
			}
		}
		else{
			if(!is_writable(self::$logpath)){
				@chmod(self::$logpath,0777);
			}
			$logfile=rtrim(self::$logpath,'/').'/'.$strFileName.'_'.date("ymd").'.log';
			if(file_exists($logfile) && !is_writable($logfile)){
				@chmod($logfile,0644);
			}
			$handle = @fopen($logfile, "a+");
			if($handle){
				$strContent = "[".date("Y-m-d H:i:s")."] [".strtoupper($strType)."] [".Tools::getRemoteAddr()."] MSG:[".$strMSG."]".$strExtra." Location:".$_SERVER["SCRIPT_FILENAME"].($line?" Line:".$line:"")." QUERY_STRING:".$_SERVER["QUERY_STRING"]." HTTP_REFERER:".(isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:"")." User-Agent:".$_SERVER["HTTP_USER_AGENT"]."\n";
				if(!fwrite($handle, $strContent)){
					@fclose($handle);
					die("Write permission deny");
				}
				@fclose($handle);
			}
		}
	}
	
	public static function simplewrite($strFileName,$strMSG){
		if(!file_exists(self::$logpath)){
			if(!mkdir(self::$logpath,'0777')){
				if(DEBUG_MODE){
					die(Tools::displayError("Make ".self::$logpath." error"));
				}
				else{
					die("error");
				}
			}
		}
		elseif(!is_dir(self::$logpath)){
			if(DEBUG_MODE){
				die(Tools::displayError(self::$logpath." is already token by a file"));
			}
			else{
				die("error");
			}
		}
		else{
			if(!is_writable(self::$logpath)){
				@chmod(self::$logpath,0777);
			}
			$logfile=rtrim(self::$logpath,'/').'/'.$strFileName.'.log';
			if(file_exists($logfile) && !is_writable($logfile)){
				@chmod($logfile,0644);
			}
			$handle = @fopen($logfile, "w");
			if($handle){
				$strContent = $strMSG."\n";
				if(!fwrite($handle, $strContent)){
					@fclose($handle);
					die("Write permission deny");
				}
				@fclose($handle);
			}
		}
	}

	public static function simpleappend($strFileName,$strMSG){
		if(!file_exists(self::$logpath)){
			if(!mkdir(self::$logpath,'0777')){
				if(DEBUG_MODE){
					die(Tools::displayError("Make ".self::$logpath." error"));
				}
				else{
					die("error");
				}
			}
		}
		elseif(!is_dir(self::$logpath)){
			if(DEBUG_MODE){
				die(Tools::displayError(self::$logpath." is already token by a file"));
			}
			else{
				die("error");
			}
		}
		else{
			if(!is_writable(self::$logpath)){
				@chmod(self::$logpath,0777);
			}
			$logfile=rtrim(self::$logpath,'/').'/'.$strFileName.'.log';
			if(file_exists($logfile) && !is_writable($logfile)){
				@chmod($logfile,0644);
			}
			$handle = @fopen($logfile, "a");
			if($handle){
				$strContent = $strMSG."\n";
				if(!fwrite($handle, $strContent)){
					@fclose($handle);
					die("Write permission deny");
				}
				@fclose($handle);
			}
		}
	}
	
	public static function simpleread($strFileName){
		$logfile=trim(self::$logpath,'/').'/'.$strFileName.'.log';
		if(file_exists($logfile) && is_readable($logfile)){
			$strContent='';
			$handler=@fopen($logfile, 'r');
			if($handler){
				while(!feof($handler)){
					$strContent.=fgets($handler);
				}
				@fclose($handler);
			}
			return $strContent;
		}
		return false;
	}
}
?>

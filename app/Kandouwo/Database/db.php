<?php namespace kandouwo\Database;

class DB {
	protected $error = null;
	
	public function create($dbserver, $dbuser, $dbpass, $dbname) {
	
		echo '创建数据库：<br>';
		
		$ret = false;
		$conn = @mysql_connect($dbserver, $dbuser, $dbpass);
		if($conn)
		{
			@mysql_unbuffered_query("SET NAMES 'utf8'");
			
			mysql_query("CREATE DATABASE IF NOT EXISTS `".$dbname."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
			$dberror = mysql_error();
			if(!empty($dberror)){
				exit("数据库{$dbname}建立失败。\r\n".$dberror);
			}

			$this->populate_db($dbname, '', __DIR__.'/kandouwo.sql' );
			if(!empty($this->errors)){
				echo("数据表建立时发生".count($this->errors)."个异常。\r\n");
				foreach($this->errors as $err){
					echo($err[0]."\r\n");
				}
			} else { $ret = true; }
			mysql_close($conn);
		}
		
		return $ret;
	}
	
	protected function populate_db($DBname, $DBPrefix, $sqlfile) {  
		@mysql_select_db($DBname);  
		$mqr = @get_magic_quotes_runtime();  
		@set_magic_quotes_runtime(0);  
		$query = fread(fopen($sqlfile, "r"), filesize($sqlfile));  
		@set_magic_quotes_runtime($mqr);

		$pieces  = $this->split_sql($query);  
	  
		for ($i=0; $i<count($pieces); $i++) {  
			$pieces[$i] = trim($pieces[$i]);  
			if(!empty($pieces[$i]) && $pieces[$i] != "#") {  
				$pieces[$i] = str_replace( "#__", $DBPrefix, $pieces[$i]);  
				if (!$result = @mysql_query ($pieces[$i])) {  
					$this->errors[] = array ( mysql_error(), "LINE {$i}:".$pieces[$i] );  
				}  
			}  
		}  
	}  
	  
	protected function split_sql($sql) {  
		$sql = trim($sql);
		//$sql = preg_replace("\n#[^\n]*\n", "\n", $sql);  
		$buffer = array();  
		$ret = array();  
		$in_string = false;  
	  
		for($i=0; $i<strlen($sql)-1; $i++) {  
			if($sql[$i] == ";" && !$in_string) {  
				$ret[] = substr($sql, 0, $i);  
				$sql = substr($sql, $i + 1);  
				$i = 0;  
			}  
	  
			if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {  
				$in_string = false;  
			}  
			elseif(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {  
				$in_string = $sql[$i];  
			}  
			if(isset($buffer[1])) {  
				$buffer[0] = $buffer[1];  
			}  
			$buffer[1] = $sql[$i];  
		}  
	  
		if(!empty($sql)) {  
			$ret[] = $sql;  
		}  
		return($ret);  
	}
	
	public static function writeFile($filename,$data,$method='rb+',$iflock=1,$check=1,$chmod=1){
		touch($filename);
		$handle = fopen($filename,$method);
		$iflock && flock($handle,LOCK_EX);
		fwrite($handle,$data);
		$method=='rb+' && ftruncate($handle,strlen($data));
		fclose($handle);
		$chmod && @chmod($filename,0777);
	}
}
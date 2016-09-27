<?php

use com\busit\App;
use com\busit\Consumer;

define("__CLASSNAME__", "\\Mysql");

class Mysql extends App implements Consumer
{
    public function consume($message, $in)
    {
		try
		{
			$db = new PDO('mysql:host='.$this->config('host').';dbname='.$this->config('database'), $this->config('username'), $this->config('pass'));

			$rows = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE " . 
				"TABLE_SCHEMA = '" . $this->escape($this->config('database')) . "' AND " . 
				"TABLE_NAME = '" . $this->escape($this->config('table')) . "'");

			if( $rows == null || $rows->rowCount() == 0 )
				throw new \Exception('No columns found for given table');
			
			$fields = array();
			$values = array();
			while ($r = $rows->fetch())
			{
				foreach( $message->content() as $key => $value )
				{
					if( strtolower($r['COLUMN_NAME']) == strtolower($key) )
					{
						$fields[] = $r['COLUMN_NAME'];
						$values[] = "'" . $this->escape($value) . "'";
						break;
					}
				}
			}
			
			$db->exec("INSERT INTO " . $this->escape($this->config('table')) . " (" . implode($fields, ',') . ") VALUES (" . implode($values, ',') . ")");
		}
		catch(\Exception $e)
		{
			$this->notifyUser($e->getMessage());
		}
    }

    public function test()
    {
		try
		{
			$db = new PDO('mysql:host='.$this->config('host').';dbname='.$this->config('database'), $this->config('username'), $this->config('pass'));
			$db->exec("SELECT 1");
			return true;
		}
		catch(\Exception $e)
		{
			return false;
		}
    }

	public function escape($text)
	{
		  return preg_replace("/(\\x00|\\n|\\r|'|\"|\\\\|\\x1a)/", "\\\\\\1", $text);
	}
}

?>

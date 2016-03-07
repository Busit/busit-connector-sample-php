<?php

namespace Busit\Connectors\Com\Busit;

use Busit\Framework\Com\Busit\Connector;
use Busit\Framework\Com\Busit\Consumer;
use Busit\Framework\Com\Busit\Factory;
use Busit\Framework\Com\Anotherservice\Db\mysql;

define("__CLASSNAME__", "\\Busit\\Connectors\\Com\\Busit\\MysqlStorage");

class MysqlStorage extends Connector implements Consumer
{
    public function consume($message, $in)
    {
		try
		{
			$db = new mysql(
				$this->config('host'), 
				$this->config('user'), 
				$this->config('pass'), 
				$this->config('database'), 
				$this->config('port')
			);
			
			$rows = $db->select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE " . 
				"TABLE_SCHEMA = '" . mysql::escape($this->config('database')) . "' AND " . 
				"TABLE_NAME = '" . mysql::escape($this->config('table')) . "'");

			if( $rows == null || count($rows) == 0 )
				throw new \Exception('No columns found for given table');
			
			$fields = array();
			$values = array();
			foreach( $rows as $r )
			{
				foreach( $message->content() as $key => $value )
				{
					if( strtolower($r['COLUMN_NAME']) == strtolower($key) )
					{
						$fields[] = $r['COLUMN_NAME'];
						$values[] = "'" . mysql::escape($value) . "'";
						break;
					}
				}
			}
			
			$db->insert("INSERT INTO " . mysql::escape($this->config('table')) . " (" . implode($fields, ',') . ") VALUES (" . implode($values, ',') . ")");
		}
		catch(\Exception $e)
		{
			$this->notifyUser($e->getMessage());
		}
    }

    public function test()
    {
        return true;
    }
}

?>

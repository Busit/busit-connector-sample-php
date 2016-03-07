<?php

namespace Busit\Connectors\Com\Busit;

use Busit\Framework\Com\Busit\Connector;
use Busit\Framework\Com\Busit\Transformer;

define("__CLASSNAME__", "\\Busit\\Connectors\\Com\\Busit\\TimeFilter");

class TimeFilter extends Connector implements Transformer
{
    public function transform($message, $in, $out)
    {
		try
		{
			if( $this->in_range() )
			{
				if( $out->key == 'allow' )
					return $message;
				else
					return null;
			}
			else
			{
				if( $out->key == 'block' )
					return $message;
				else
					return null;
			}
		}
		catch(\Exception $e)
		{
			$this->notifyUser("The connector encountered an invalid time frame definition. Please review the configuration.");
			return null;
		}
	}
	
	private function in_range()
	{
		$conf = json_decode($this->config('data'), true);
		
		foreach( $conf as $c )
			if( $this->in_range2($c) )
				return true;
		return false;
	}
	
	private function in_range2($c)
	{
		$day = intval(date('N'), 10);
		$hour = intval(date('H'), 10);
		$min = intval(date('i'), 10);
		
		// 1) check day
		switch($day)
		{
			case 1: $day = 'monday'; break;
			case 2: $day = 'tuesday'; break;
			case 3: $day = 'wednesday'; break;
			case 4: $day = 'thursday'; break;
			case 5: $day = 'friday'; break;
			case 6: $day = 'saturday'; break;
			case 7: $day = 'sunday'; break;
		}
		if( !in_array($day, $c['day']) )
			return false;

		// 2) check time FROM
		preg_match("/^([0-9]{1,2})\\s*(?::|h)?\\s*([0-9]{1,2})?$/i", $c['from'], $matches);
		$from_hour = min(23, @intval($matches[1], 10));
		$from_min = min(59, @intval($matches[2], 10));
		if( $from_hour > $hours || ($from_hour == $hours && $from_min > $minutes) )
			return false;
		
		// 3) check time TO
		preg_match("/^([0-9]{1,2})\\s*(?::|h)?\\s*([0-9]{1,2})?$/i", $c['to'], $matches);
		$to_hour = min(23, @intval($matches[1], 10));
		$to_min = min(59, @intval($matches[2], 10));
		if( $to_hour < $hours || ($to_hour == $hours && $to_min < $minutes) )
			return false;
		
		// 4) otherwise, bingo
		return true;
    }

    public function test()
    {
        return true;
    }
}

?>
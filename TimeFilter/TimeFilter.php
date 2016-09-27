<?php

use com\busit\App;
use com\busit\Transformer;

define("__CLASSNAME__", "\\TimeFilter");

class TimeFilter extends App implements Transformer
{
    public function transform($message, $in, $out)
    {

		$conf = json_decode($this->config('data'), true);

		try
		{
			if( $this->in_range($conf[0]) )
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
	
	private function in_range($c)
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

		if( $from_hour > $hour || ($from_hour == $hour && $from_min > $min) )
			return false;

		// 3) check time TO
		preg_match("/^([0-9]{1,2})\\s*(?::|h)?\\s*([0-9]{1,2})?$/i", $c['to'], $matches);
		$to_hour = min(23, @intval($matches[1], 10));
		$to_min = min(59, @intval($matches[2], 10));
		if( $to_hour < $hour || ($to_hour == $hour && $to_min < $min) )
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
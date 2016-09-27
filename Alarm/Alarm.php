<?php

use com\busit\App;
use com\busit\Producer;
use com\busit\Message;
use com\busit\Content;

define("__CLASSNAME__", "\\Alarm");

class Alarm extends App implements Producer
{
	public function produce($out, $data=null)
	{
		$content = new Content('message');
		$content['uuid'] = 'BusApp_Alarm_'.$this->id();
		$content['data'] = ($this->config('message') ? $this->config('message') : "No message configured");
		
		$message = new Message();
		$message->content($content);

		return $message;
	}

	public function sample($out)
	{
		$content = new Content('message');
		$content['uuid'] = 'BusApp_Alarm_'.$this->id();
		$content['data'] = "Sample for the alarm";
		
		$message = new Message();
		$message->content($content);

		return $message;
	}

	public function test()
	{
		// this BusApp does not have any dependency
		return true;
	}
}

?>
<?php

namespace Busit\Connectors\Com\Busit;

use Busit\Framework\Com\Busit\Connector;
use Busit\Framework\Com\Busit\Factory;
use Busit\Framework\Com\Busit\Producer;

define("__CLASSNAME__", "\\Busit\\Connectors\\Com\\Busit\\Alarm");

class Alarm extends Connector implements Producer
{
    public function produce($out)
    {
        if ($out->value)
		{
            $message = Factory::message();
            $content = Factory::content('message');
            $content['message'] = $out->value;
            $content['timestamp'] = time();
            $content['date'] = date('Y-m-d H:i:s');
            $message->content($content);

            return $message;
        }
		else
			return null;
    }

    public function sample($out)
    {
        $message = Factory::message();
        $content = Factory::content('message');
        $content['message'] = "The alarm is ringing!";
        $content['timestamp'] = time();
        $content['date'] = date('Y-m-d H:i:s');
        $message->content($content);

        return $message;
    }

    public function test()
    {
        return true;
    }
}

?>
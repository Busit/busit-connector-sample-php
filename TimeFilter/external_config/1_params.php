<?php

if( !defined('PROPER_START') )
{
	header("HTTP/1.0 403 Forbidden");
	exit;
}

if( (!isset($_SESSION['BUSIT_TOKEN']) && !isset($_GET['token'])) || !isset($_SESSION['BUSIT_INSTANCE']) )
	template::redirect('/timefilter/config');
if( !isset($_SESSION['BUSIT_TOKEN']) )
	$_SESSION['BUSIT_TOKEN'] = $_GET['token'];
	
$error = '';
if( isset($_GET['e']) )
{
	$error = "<p style=\"color: red;\">";
	switch($_GET['e'])
	{
		case 1: $error .= $lang['error_empty']; break;
		case 2: $error .= $lang['error_antispam']; break;
		case 3: $error .= $lang['error_name']; break;
		default: $error .= $lang['error_other']; break;
	}
	$error .= "</p>";
}	

$_SESSION['__ANTISPAM__'] = array(md5('simon'.rand()), md5('busit'.time()));

// get current values from the connector
if( isset($_SESSION['FORM']['name']) )
	$n = $_SESSION['FORM']['name'];
else
{
	$n = api::send('self/busit/instance/select', array('id'=>$_SESSION['BUSIT_INSTANCE']), "i:{$_SESSION['BUSIT_INSTANCE']}:{$_SESSION['BUSIT_TOKEN']}");
	$n = $n[0]['instance_name'];
}

if( isset($_SESSION['FORM']['time']) )
	$t = $_SESSION['FORM']['time'];
else
{
	$t = api::send('self/busit/instance/config/select', array('id'=>$_SESSION['BUSIT_INSTANCE'], 'key'=> 'data'), "i:{$_SESSION['BUSIT_INSTANCE']}:{$_SESSION['BUSIT_TOKEN']}");
	$t = json_decode($t[0]['config_value'], true);
}

$content .= "
		<div style=\"text-align: center; width: 100%; height: 448px; overflow-y: auto;\">
			<img src=\"https://images.busit.com/connectors/378_100.png\" style=\"width: 100px; height: 100px;\" />
			<br /><br />
			<h4>{$lang['title']}</h4>
			<p>{$lang['first_text']}</p>{$error}
			
			<form action=\"/timefilter/2_finish\" method=\"post\" class=\"center\" enctype=\"multipart/form-data\">
				<input type=\"hidden\" name=\"{$_SESSION['__ANTISPAM__'][0]}\" value=\"{$_SESSION['__ANTISPAM__'][1]}\" />
				<fieldset>
					<input type=\"text\" id=\"name\" name=\"name\" value=\"{$n}\" placeholder=\"{$lang['name']}\" />
					<span class=\"help-block\">{$lang['name_help']}</span>
				</fieldset>
				<div id=\"stepsDiv\" style=\"margin: 20px 0px; padding: 20px; border: 1px solid #5092D3; background-color: #FCFCFC;\">";

for( $i = 0; $i < count($t); $i++ )
{
	$content .= "
					<div style=\"height: 170px; clear: both;\"><h3>{$lang['interval']} " . ($i+1) . "</h3>
					<fieldset style=\"display: inline-block; float: left; text-align: left;\">
						<input type=\"checkbox\" id=\"time[{$i}][day][]\" name=\"time[{$i}][day][]\" value=\"monday\"".(in_array('monday',$t[$i]['day'])?" checked":"")." /> {$lang['monday']}<br />
						<input type=\"checkbox\" id=\"time[{$i}][day][]\" name=\"time[{$i}][day][]\" value=\"tuesday\"".(in_array('tuesday',$t[$i]['day'])?" checked":"")." /> {$lang['tuesday']}<br />
						<input type=\"checkbox\" id=\"time[{$i}][day][]\" name=\"time[{$i}][day][]\" value=\"wednesday\"".(in_array('wednesday',$t[$i]['day'])?" checked":"")." /> {$lang['wednesday']}<br />
						<input type=\"checkbox\" id=\"time[{$i}][day][]\" name=\"time[{$i}][day][]\" value=\"thursday\"".(in_array('thursday',$t[$i]['day'])?" checked":"")." /> {$lang['thursday']}<br />
						<input type=\"checkbox\" id=\"time[{$i}][day][]\" name=\"time[{$i}][day][]\" value=\"friday\"".(in_array('friday',$t[$i]['day'])?" checked":"")." /> {$lang['friday']}<br />
						<input type=\"checkbox\" id=\"time[{$i}][day][]\" name=\"time[{$i}][day][]\" value=\"saturday\"".(in_array('saturday',$t[$i]['day'])?" checked":"")." /> {$lang['saturday']}<br />
						<input type=\"checkbox\" id=\"time[{$i}][day][]\" name=\"time[{$i}][day][]\" value=\"sunday\"".(in_array('sunday',$t[$i]['day'])?" checked":"")." /> {$lang['sunday']}<br />
					</fieldset>
					<fieldset style=\"display: inline-block; float: right;\">
						{$lang['from']}<br />
						<input type=\"text\" id=\"time[{$i}][from]\" name=\"time[{$i}][from]\" value=\"{$t[$i]['from']}\" placeholder=\"{$lang['from_placeholder']}\" /><br />
						{$lang['to']}<br />
						<input type=\"text\" id=\"time[{$i}][to]\" name=\"time[{$i}][to]\" value=\"{$t[$i]['to']}\" placeholder=\"{$lang['to_placeholder']}\" /><br />
					</fieldset>
					</div>";
}
				
$content .= "
					<input style=\"clear: both;\" type=\"button\" onclick=\"append(); return false;\" value=\"{$lang['add_step']}\" />
				</div>
				<fieldset>
					<input type=\"submit\" value=\"{$lang['submit']}\" />
				</fieldset>
			</form>
		</div>
		<script type=\"text/javascript\">
			function append()
			{
				var d = document.getElementById('stepsDiv');
				var n = document.createElement('DIV');
				var last = null;
				var i = 0; 
				for(var j = 0; j < d.childNodes.length; j++ )
				{
					if( d.childNodes[j].tagName == 'DIV' )
						i++;
					else if( d.childNodes[j].tagName == 'INPUT' )
						last = d.childNodes[j];
				}
				
				n.style.height = '170px';
				n.style.clear = 'both';
				n.innerHTML = \"<h3>{$lang['interval']} \" + (i+1) + \"</h3>\" + 
					\"<fieldset style=\\\"display: inline-block; float: left; text-align: left;\\\">\" + 
					\"	<input type=\\\"checkbox\\\" id=\\\"time[\"+i+\"][day][]\\\" name=\\\"time[\"+i+\"][day][]\\\" value=\\\"monday\\\" checked /> {$lang['monday']}<br />\" + 
					\"	<input type=\\\"checkbox\\\" id=\\\"time[\"+i+\"][day][]\\\" name=\\\"time[\"+i+\"][day][]\\\" value=\\\"tuesday\\\" checked /> {$lang['tuesday']}<br />\" + 
					\"	<input type=\\\"checkbox\\\" id=\\\"time[\"+i+\"][day][]\\\" name=\\\"time[\"+i+\"][day][]\\\" value=\\\"wednesday\\\" checked /> {$lang['wednesday']}<br />\" + 
					\"	<input type=\\\"checkbox\\\" id=\\\"time[\"+i+\"][day][]\\\" name=\\\"time[\"+i+\"][day][]\\\" value=\\\"thursday\\\" checked /> {$lang['thursday']}<br />\" + 
					\"	<input type=\\\"checkbox\\\" id=\\\"time[\"+i+\"][day][]\\\" name=\\\"time[\"+i+\"][day][]\\\" value=\\\"friday\\\" checked /> {$lang['friday']}<br />\" + 
					\"	<input type=\\\"checkbox\\\" id=\\\"time[\"+i+\"][day][]\\\" name=\\\"time[\"+i+\"][day][]\\\" value=\\\"saturday\\\" /> {$lang['saturday']}<br />\" + 
					\"	<input type=\\\"checkbox\\\" id=\\\"time[\"+i+\"][day][]\\\" name=\\\"time[\"+i+\"][day][]\\\" value=\\\"sunday\\\" /> {$lang['sunday']}<br />\" + 
					\"</fieldset>\" + 
					\"<fieldset style=\\\"display: inline-block; float: right;\\\">\" + 
					\"	{$lang['from']}<br />\" + 
					\"	<input type=\\\"text\\\" id=\\\"time[\"+i+\"][from]\\\" name=\\\"time[\"+i+\"][from]\\\" value=\\\"\\\" placeholder=\\\"{$lang['from_placeholder']}\\\" /><br />\" + 
					\"	{$lang['to']}<br />\" + 
					\"	<input type=\\\"text\\\" id=\\\"time[\"+i+\"][to]\\\" name=\\\"time[\"+i+\"][to]\\\" value=\\\"\\\" placeholder=\\\"{$lang['to_placeholder']}\\\" /><br />\" + 
					\"</fieldset>\";
				
				d.insertBefore(n, last);
			}
		</script>
		";

/* ========================== OUTPUT PAGE ========================== */
$template->output($content);

?>

<?php
//USER PERSONAL SETTINGS
$NETDUINO_PLUS_PORT = 8085;
$RASPBERRYPI1_PORT  = 8086;
$RASPBERRYPI2_PORT  = 8087;

//PUSHINGBOX SETTINGS
$PushingBoxDeviceID = "Your PushingBox device ID";

//PUSHOVERSETTINGS
$APP_TOKEN = "Your Pushover App token";
$USER_KEY  = "Your Pushover User Key";

//DEBUG SETTINGS
$PHP_DEBUG  = 0;
$MOBILE     = 0;
$FILE_DEBUG = 0;

//Logs
$ALARM_LOGS;
$SYSTEM_LOGS;
  
/*Time Display the Linux way*/
//$current_time = exec("date +'%d %b %Y %r %Z'");

//TIME DISPLAY
/*Time Display the PHP way*/
/*Select your zone http://www.php.net/manual/en/class.datetimezone.php */
date_default_timezone_set('America/New_York');
$current_time = strtoupper(date('d M Y h:i:s a T'));

if (isset($_GET['main-page']))
{
  if (isset($_GET['deviceID']))
  {
     $PushingBoxDeviceID = $_GET["deviceID"];
  }

  $MOBILE = isset($_GET['mobile'])? "1": "0";

  //send notification to PushingBox
  exec('curl \'http://api.pushingbox.com/pushingbox?devid='.$PushingBoxDeviceID.'\'');
  
  //send notification to Pushover
  exec('curl -s   -F "token='.$APP_TOKEN.'"   -F "user='.$USER_KEY.'"   -F "message=Web Server Access from Pi."   -F "title=Web Trigger"   https://api.pushover.net/1/messages.json');

  //alarm logs path
  $filename = "data/alerts.json";

  $fp = fopen($filename, 'r');
  $array = explode("\n", fread($fp, filesize($filename)));

if ($fp)
{
   $ALERT_COUNT = count($array) -1;
   if($FILE_DEBUG)
   {
      echo $ALERT_COUNT;
	  echo $filename;
   }
   if ($ALERT_COUNT >0)
   {
      // Add each line to an array
      for($i=0;$i<$ALERT_COUNT;++$i)
      {
         $json= $array[$i];
         $obj = json_decode($json);
		 $ALARM_LOGS = $ALARM_LOGS . "<td><center>" . $obj->{'time'} . "</center></td>" . "<td><center>" . $obj->{'zone'} . "</center></td>" . "<td><center>" . $obj->{'description'} . "</center></td>" ."</tr>";
      }
   }
}
else
{
  $ALARM_LOGS = "<tr><td></td><td><center>No Alarms/Sensors to report</center></td><td></td></tr>";
}

  //system logs path
  $filename = "data/system-logs.json";

  $fp = fopen($filename, 'r');
  $array = explode("\n", fread($fp, filesize($filename)));

if ($fp)
{
   $LOGS_COUNT = count($array) -1;
   if($FILE_DEBUG)
   {
      echo $LOGS_COUNT;
	  echo $filename;
   }
   if ($LOGS_COUNT >0)
   {
      // Add each line to an array
      for($i=0;$i<$LOGS_COUNT;++$i)
      {
         $json= $array[$i];
         $obj = json_decode($json);
		 $SYSTEM_LOGS = $SYSTEM_LOGS . "<td><center>" . $obj->{'time'} . "</center></td>" . "<td><center>" . $obj->{'description'} . "</center></td>" ."</tr>";
      }
   }
}
else
{
  $SYSTEM_LOGS = "<tr><td></td><td><center>No System logs to report</center></td></tr>";
}

  print <<< EOT
<!doctype html>
<html lang="en">
<!-- original content from HomeAlarmPlus project details on  http://netduinoexperience.blogspot.com/ -->
<!--jQuery, linked from a CDN-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>

<!-- Weather data from Wunderground, http://www.wunderground.com/weather/api/ -->
<script src="WebResources/wunderground_query.js"></script>
  <script>
  $(function() {
    $( "#menu" ).menu();
  });
  </script>

  <style>
  .ui-menu { width: 180px; position:relative; left:2px; }
  </style>

<head>
<meta http-equiv="Content-Style-Type" content="text/css">
<meta charset="UTF-8">
<meta name="author"   content="Gilberto Garc&#237;a"/>
<meta name="mod-date" content="01/02/2014"/>

<!-- http://www.formsite.com/documentation/mobile-optimization.html -->
<!--
<?php if ($MOBILE ==1) : ?>
   <meta name="viewport" content="width=device-width, height=device-height, user-scalable=no" />
   <meta name="MobileOptimized" content="width" />
   <meta name="HandheldFriendly" content="true" />
   <meta name="apple-mobile-web-app-title" content="HomeAlarmPlus Pi" />
   <meta name="apple-mobile-web-app-capable" content="yes" />
   <meta name="apple-mobile-web-app-status-bar-style" content="black" />
<?php endif; ?>
-->

<!--jQuery, linked from a CDN-->
<script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"/></script>
<!--jQueryUI Theme -->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/redmond/jquery-ui.css" />

<link rel="stylesheet" type="text/css" href="WebResources/header_style.css"/>
<link rel="stylesheet" type="text/css" href="WebResources/table_style.css"/>
<title>RASPBERRY PI Control Panel - Home</title>
</head>
<body>
        <div id="container" class="ui-widget" >
		<!-- style="text-align:center;" -->
        <div id="header" class="ui-widget-header ui-corner-top">
        <h2>HomeAlarmPlus Pi</h2>
	</div>
<div id="menu" style="height:445px;width:235px;float:right;position:absolute;top:80px;display:block;z-index:1000;">
<b><center>Menu</center></b>
<div style="border-bottom:1px;"></div>
<div style="border-bottom:1px;"></div>
  <li><a href="http://{$_SERVER['SERVER_NAME']}:{$NETDUINO_PLUS_PORT}" target="_blank">Alarm Panel [Netduino Plus]</a></li>
  <div style="border-bottom:1px;"></div>
  <li><a href="/weather.html" target="_blank">Weather</a></li>
  <div style="border-bottom:1px;"></div>
  <li>
    <a href="#">Raspberry Pi2</a>
    <ul>
      <li><a href="http://{$_SERVER['SERVER_NAME']}:{$RASPBERRYPI2_PORT}" target="_blank">Main Page</a></li>
      <div style="border-bottom:1px;"></div>
      <li class="ui-state-disabled"><a href="#">Option1</a></li>
      <div style="border-bottom:1px;"></div>
      <li class="ui-state-disabled"><a href="#">Option2</a></li>
    </ul>
  </li>
  <div style="border-bottom:1px;"></div>
  <li>
    <a href="#">Other Platforms</a>
      <ul>
        <li><a href="/mobile" target="_blank">Mobile</a></li>
        <div style="border-bottom:1px;"></div>
        <li><a href="/Touch" target="_blank">Tablet</a></li>
      </ul>
  </li>
  <div style="border-bottom:1px;"></div>
  <li>
    <a href="#">Help</a>
    <ul>
      <li><a href="/references.htm" target="_blank">References</a></li>
      <div style="border-bottom:1px;"></div>
      <li><a href="/sysinfo/index.php" target="_blank">System Info</a></li>
      <div style="border-bottom:1px;"></div>
      <li><a href="/sysinfo_v2/index.php" target="_blank">System Info v2</a></li>
    </ul>
  </li>
  <div style="border-bottom:1px;"></div>
<!-- menu -->

<center>
        <table class="desktop_weather_table" border="0" cellspacing="0" width="25%">
                <tr>
                   <td id="c_current_conditions"><center>Now</center><br/></td>
		           <td id="c_temperature"><center>Temperature</center></td>
                </tr>
        </table>
</center>

</div>

        <div id="content" style="background-color:#EEEEEE;padding:0px 0 0 250px;" class="ui-widget-content ui-corner-bottom" >
        <p>System Time: <b>{$current_time}</b></p>
        <p id="c_location">Location: </p>
        <p id="c_current_forecast">Forecast: </p>
<!--
<?php if ($PHP_DEBUG == 1) { ?>
		<p>DEBUG: Value of Mobile is :{$MOBILE}</p>
		<p>DEBUG: cURL status: {$CURL_STATUS}</p>
<?php } ?>
        <br/>
-->
            <br>
			<b>Alarm Logs:</b>

            <table class="gridtable">
            <tr><th><center>Time</center></th><th><center>Zone/Sensor</center></th><th><center>Description</center></th></tr>
			{$ALARM_LOGS}
			</table>

            <br>
			<b>System Logs:</b>

            <table class="gridtable">
                        <tr><th><center>Time</center></th><th><center>Description</center></th></tr>
			{$SYSTEM_LOGS}
	    </table>


<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>

</div><!-- container -->

<div id="footer" style="border:1px solid #CCCCCC;">
<p><span class="note">Copyright &#169; 2014 Gilberto Garc&#237;a</span></p>
</div><!-- footer    -->

</div><!-- page      -->

</body></html>
EOT;
}

else
{
   $useragent=$_SERVER['HTTP_USER_AGENT'];
   if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
   {
	   //header("Location: http://detectmobilebrowsers.com/");
	   header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?main-page=yes&deviceID=".$PushingBoxDeviceID ."&mobile=yes");
   }
   else
   {
       header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?main-page=yes&deviceID=".$PushingBoxDeviceID);
   }
   exit;
}
?>

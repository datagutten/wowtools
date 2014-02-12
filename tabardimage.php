<?Php
require 'tabardgen.php';
$realm=$_GET['realm'];
$guild=$_GET['guild'];
$urlrealm=rawurlencode($realm);
$urlguild=rawurlencode($guild);

$json=file_get_contents($url="http://eu.battle.net/api/wow/guild/$urlrealm/$urlguild");
if($json===false)
{
	$im=imagecreatetruecolor(540,240);
	imagefill($im,0,0,0xFFFFFF);
	$black=imagecolorallocate($im,0,0,0);
	imagestring($im,3,5,0,"Error loading information for",$black);
	imagestring($im,3,5,11,"$guild-$realm",$black);
	//imagestring($im,3,5,22,$url,$black);
}
else
{
	$guild=json_decode($json,true);
	$tabard=new tabard($guild);
	$im=$tabard->maketabard();
}
header('Content-type: image/png');
imagepng($im);
?>
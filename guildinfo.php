<?Php
function guildinfo($realm,$guild)
{
	$urlrealm=rawurlencode($realm);
	$urlguild=rawurlencode($guild);

	$json=file_get_contents($url="http://eu.battle.net/api/wow/guild/$urlrealm/$urlguild?fields=members");
	if($json===false)
		return false;

	$guild=json_decode($json,true);

	foreach($guild['members'] as $member)
	{
		$sortedmembers[$member['character']['race']][$member['character']['class']][]=$member;
	}
	return array('guild'=>$guild,'sortedmembers'=>$sortedmembers);
}
?>
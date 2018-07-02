<?php namespace sex\guard\command\argument;


/**
 *  _    _       _                          _  ____
 * | |  | |_ __ (_)_    _____ _ ______ __ _| |/ ___\_ _______      __
 * | |  | | '_ \| | \  / / _ \ '_/ __// _' | | /   | '_/ _ \ \    / /
 * | |__| | | | | |\ \/ /  __/ | \__ \ (_) | | \___| ||  __/\ \/\/ /
 *  \____/|_| |_|_| \__/ \___|_| /___/\__,_|_|\____/_| \___/ \_/\_/
 *
 * @author sex_KAMAZ
 * @link   http://universalcrew.ru
 *
 */
use sex\guard\Manager;

use pocketmine\Player;
use pocketmine\level\Position;


/**
 * @todo nothing.
 */
class OwnerArgument
{
	/**
	 * @var Manager
	 */
	private $api;


	/**
	 * @param Manager $api
	 */
	function __construct( Manager $api )
	{
		$this->api = $api;
	}


	/**
	 *                                          _
	 *   __ _ _ ____ _ _   _ _ __ _   ___ _ ___| |_
	 *  / _' | '_/ _' | | | | '  ' \ / _ \ '_ \   _\
	 * | (_) | || (_) | |_| | || || |  __/ | | | |_
	 *  \__,_|_| \__, |\___/|_||_||_|\___|_| |_|\__\
	 *           /___/
	 *
	 * @param  Player   $sender
	 * @param  string[] $args
	 *
	 * @return bool
	 */
	function execute( Player $sender, array $args ): bool
	{
		$nick = strtolower($sender->getName());
		$api  = $this->api;

		if( count($args) < 1 )
		{
			$sender->sendMessage($api->getValue('owner_help'));
			return FALSE;
		}
		
		$region = $api->getRegionByName($args[0]);

		if( !isset($region) )
		{
			$sender->sendMessage($api->getValue('rg_not_exist'));
			return FALSE;
		}

		if( !isset($region) )
		{
			$sender->sendMessage($api->getValue('rg_not_exist'));
			return FALSE;
		}

		if( $region->getOwner() != $nick and !$sender->hasPermission('sexguard.all') )
		{
			$sender->sendMessage($api->getValue('player_not_owner'));
			return FALSE;
		}

		$owner = $args[1];

		if( !isset($owner) )
		{
			$sender->sendMessage($api->getValue('owner_help'));
			return FALSE;
		}

		$player = $api->getServer()->getPlayerExact($owner);

		if( !($player instanceof Player) )
		{
			$sender->sendMessage($api->getValue('player_not_exist'));
			return FALSE;
		}

		$val = $api->getGroupValue($player);
		
		if( count($api->getRegionList($owner)) > $val['max_count'] )
		{
			$sender->sendMessage(str_replace('{max_count}', $val['max_count'], $api->getValue('rg_overcount')));
			return FALSE;
		}

		$region->setOwner($owner);
		$region->addMember($nick);

		$sender->sendMessage(str_replace(['{player}', '{region}'], [$owner, $args[0]], $api->getValue('owner_change')));
		$player->sendMessage(str_replace(['{player}', '{region}'], [$nick,  $args[0]], $api->getValue('owner_got_region')));
		return TRUE;
	}
}
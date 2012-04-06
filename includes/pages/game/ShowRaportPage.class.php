<?php

/**
 *  2Moons
 *  Copyright (C) 2012 Jan
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package 2Moons
 * @author Jan <info@2moons.cc>
 * @copyright 2006 Perberos <ugamela@perberos.com.ar> (UGamela)
 * @copyright 2008 Chlorel (XNova)
 * @copyright 2009 Lucky (XGProyecto)
 * @copyright 2012 Jan <info@2moons.cc> (2Moons)
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.7.0 (2012-05-31)
 * @info $Id$
 * @link http://code.google.com/p/2moons/
 */

class ShowRaportPage extends AbstractPage
{
	public static $requireModule = 0;
	
	protected $disableEcoSystem = true;

	function __construct() 
	{
		parent::__construct();
	}
	
	function battlehall() 
	{
		global $LNG, $USER;
		
		$this->setWindow('popup');
		
		$RID		= HTTP::_GP('raport', 0);
		
		$Raport		= $GLOBALS['DATABASE']->getFirstRow("SELECT 
		raport, time,
		(
			SELECT 
			GROUP_CONCAT(username SEPARATOR ' & ') as attacker
			FROM ".USERS." 
			WHERE id IN (SELECT uid FROM ".TOPKB_USERS." WHERE ".TOPKB_USERS.".rid = ".RW.".rid AND role = 1)
		) as attacker,
		(
			SELECT 
			GROUP_CONCAT(username SEPARATOR ' & ') as defender
			FROM ".USERS." 
			WHERE id IN (SELECT uid FROM ".TOPKB_USERS." WHERE ".TOPKB_USERS.".rid = ".RW.".rid AND role = 2)
		) as defender
		FROM ".RW."
		WHERE rid = ".$RID.";");
		
		$Info		= array($Raport["attacker"], $Raport["defender"]);
		
		if(!isset($Raport)) {
			$this->printMessage($LNG['sys_raport_not_found']);
		}
		
		$CombatRaport			= unserialize($Raport['raport']);
		$CombatRaport['time']	= _date($LNG['php_tdformat'], $CombatRaport['time'], $USER['timezone']);

		$this->assign_vars(array(
			'Raport'	=> $CombatRaport,
			'Info'		=> $Info,
		));
		
		$this->render('shared.mission.raport.tpl');
	}
	
	function show() 
	{
		global $LNG, $USER;
		
		$this->setWindow('popup');
		
		$RID		= HTTP::_GP('raport', 0);
		
		$Raport		= $GLOBALS['DATABASE']->countquery("SELECT raport FROM ".RW." WHERE rid = ".$RID.";");
		$Info		= array();
		
		if(!isset($Raport)) {
			$this->printMessage($LNG['sys_raport_not_found']);
		}

		
		$CombatRaport	= unserialize($Raport);
		$CombatRaport['time']	= _date($LNG['php_tdformat'], $CombatRaport['time'], (isset($USER['timezone']) ? $USER['timezone'] : $CONF['timezone']));

		$this->assign_vars(array(
			'Raport'	=> $CombatRaport,
		));
		
		$this->render('shared.mission.raport.tpl');
	}
}
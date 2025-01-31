<?php
	/* I beg you, please leave this area, touching anything here will just lead to you complaining on the forums */
	/* unless you know what you're doing, then, by all means, be my guest, touch away. ~ ChaseHQ */

	require_once('query_utils.php');

	class PLAYER {
		private $_playerUUID;
		private $_playerRow;
	
		function __construct($playerUUID) {
			$this->_playerUUID = $playerUUID;
                        $this->popCheck();
		}
		
		private function popCheck() {
			if (!isset($this->_playerRow)) 
				$this->_playerRow = mysql_fetch_assoc(mysql_query("SELECT * FROM players WHERE uuid = '{$this->_playerUUID}'"));
		}
		
		public function getName() {
			return $this->_playerRow['player_name'];
		}
		
		public function getUUID() {
			return $this->_playerUUID;
		}
		
		public function isOnline() {
			if ($this->_playerRow['online'] == 'Y')
				return true;
                        else return false;
		}
		
		public function getFirstLogin() {
			return $this->_playerRow['firstever_login'];
		}
		
		public function getLastLogin() {
			return $this->_playerRow['last_login'];
		}
		
		public function getNumberOfLogins() {
			return $this->_playerRow['num_logins'];
		}
		
		public function getCurrentLoginTime() {
			return $this->_playerRow['this_login'];
		}
		
		public function getNumberOfSecondsLoggedOn() {
			return $this->_playerRow['num_secs_loggedon'];
		}
		
		public function getDistanceTraveledTotal() {
			return $this->_playerRow['distance_traveled'];
		}
		
		public function getDistanceTraveledByMinecart() {
			return $this->_playerRow['distance_traveled_in_minecart'];
		}
		
		public function getDistanceTraveledByBoat() {
			return $this->_playerRow['distance_traveled_in_boat'];
		}
		
		public function getDistanceTraveledByPig() {
			return $this->_playerRow['distance_traveled_on_pig'];
		}
		
		public function getDistanceTraveledByFoot() {
			return $this->getDistanceTraveledTotal() - ($this->getDistanceTraveledByMinecart() + $this->getDistanceTraveledByBoat() + $this->getDistanceTraveledByPig());
		}
		
		public function getBlocksDestroyedOfType($id) {
			$row = mysql_fetch_assoc(mysql_query("SELECT num_destroyed FROM blocks WHERE uuid = '{$this->_playerUUID}' AND block_id = '{$id}'"));
			return $row['num_destroyed'];
		}
		
		public function getBlocksPlacedOfType($id){
			$row = mysql_fetch_assoc(mysql_query("SELECT num_placed FROM blocks WHERE uuid = '{$this->_playerUUID}' AND block_id = '{$id}'"));
			return $row['num_placed'];
		}
		
		public function getBlocksDestroyedTotal() {
			$row = mysql_fetch_assoc(mysql_query("SELECT SUM(num_destroyed) AS destroyedTotal FROM blocks WHERE uuid = '{$this->_playerUUID}'"));
			return $row['destroyedTotal'];
		}
		
		public function getBlocksPlacedTotal() {
			$row = mysql_fetch_assoc(mysql_query("SELECT SUM(num_placed) AS placedTotal FROM blocks WHERE uuid = '{$this->_playerUUID}'"));
			return $row['placedTotal'];
		}
		
		public function getPlayerBlockTable() {
			return QueryUtils::get2DArrayFromQuery("SELECT block_id, num_destroyed, num_placed FROM blocks WHERE uuid = '{$this->_playerUUID}'");
		}
		
		public function getBlocksMostDestroyed() {
            
            $highest = 0;
            $idOfHighest = -1;
            
            foreach (QueryUtils::getResourceTable() as $resource) {
                $test = $this->getBlocksDestroyedOfType($resource['resource_id']);
                if ($test > $highest) {
                    $highest = $test;
                    $idOfHighest = $resource['resource_id'];
                }
            }
            
            return $idOfHighest;
        }
        
        public function getBlocksMostPlaced() {
  
            $highest = 0;
            $idOfHighest = -1;
            
            foreach (QueryUtils::getResourceTable() as $resource) {
                $test = $this->getBlocksPlacedOfType($resource['resource_id']);
                if ($test > $highest) {
                    $highest = $test;
                    $idOfHighest = $resource['resource_id'];
                }
            }
            
            return $idOfHighest;
        }
		
		public function getPickedUpOfType($id) {
			$row = mysql_fetch_assoc(mysql_query("SELECT num_pickedup FROM pickup_drop WHERE uuid = '{$this->_playerUUID}' AND item = '{$id}'"));
			return $row['num_pickedup'];
		}
		
		public function getDroppedOfType($id) {
			$row = mysql_fetch_assoc(mysql_query("SELECT num_dropped FROM pickup_drop WHERE uuid = '{$this->_playerUUID}' AND item = '{$id}'"));
			return $row['num_dropped'];
		}
		
		public function getPickedUpTotal() {
			$row = mysql_fetch_assoc(mysql_query("SELECT SUM(num_pickedup) AS totalPickedup FROM pickup_drop WHERE uuid = '{$this->_playerUUID}'"));
			return $row['totalPickedup'];
		}
		
		public function getDroppedTotal() {
			$row = mysql_fetch_assoc(mysql_query("SELECT SUM(num_dropped) AS totalDropped FROM pickup_drop WHERE uuid = '{$this->_playerUUID}'"));
			return $row['totalDropped'];
		}
		
		public function getPlayerPickupDropTable() {
			return QueryUtils::get2DArrayFromQuery("SELECT item, num_pickedup, num_dropped FROM pickup_drop WHERE uuid = '{$this->_playerUUID}'");
		}
		
		public function getMostPickedUp() {
            $highest = 0;
            $idOfHighest = -1;
            
            foreach (QueryUtils::getResourceTable() as $resource) {
                $test = $this->getPickedUpOfType($resource['resource_id']);
                if ($test > $highest) {
                    $highest = $test;
                    $idOfHighest = $resource['resource_id'];
                }
            }
            
            return $idOfHighest;
        }
        
        public function getMostDropped() {
            $highest = 0;
            $idOfHighest = -1;
            
            foreach (QueryUtils::getResourceTable() as $resource) {
                $test = $this->getDroppedOfType($resource['resource_id']);
                if ($test > $highest) {
                    $highest = $test;
                    $idOfHighest = $resource['resource_id'];
                }
            }
            
            return $idOfHighest;
        }
		
		public function getPlayerKillTable() {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_by_uuid = '{$this->_playerUUID}'");
		}
		
		public function getMostDangerousWeapon() {
            $highest = 0;
            $idOfHighest = -1;
            $idOfNone = QueryUtils::getResourceIdByName("None");
            
            foreach (QueryUtils::getResourceTable() as $resource) {
            	
            	if ($resource['resource_id'] == $idOfNone) continue;
            	
            	$res = $this->getPlayerKillTableUsing($resource['resource_id']);
            	
            	if ($res)
                	$test = count($res);
                	else
                	$test = 0;
                	
                if ($test > $highest) {
                    $highest = $test;
                    $idOfHighest = $resource['resource_id'];
                }
            }
            
            return $idOfHighest;
        }
        
        public function getMostKilledPVP($serverObj) {
            $highest = 0;
            $playerOfHighest = null;
            
            foreach ($serverObj->getAllPlayers() as $player) {
            	$res = $this->getPlayerKillPVP($player->getUUID());
            	
            	if ($res)
                	$test = count($res);
                	else
                	$test = 0;
                	
                if ($test > $highest) {
                    $highest = $test;
                    $playerOfHighest = $player;
                }
            }
            
            return $playerOfHighest;
        }
        
        public function getMostKilledByPVP($serverObj) {
            $highest = 0;
            $playerOfHighest = null;
            
            foreach ($serverObj->getAllPlayers() as $player) {
            	$res = $this->getPlayerDeathPVP($player->getUUID());
            	
            	if ($res)
                	$test = count($res);
                	else
                	$test = 0;
                	
                if ($test > $highest) {
                    $highest = $test;
                    $playerOfHighest = $player;
                }
            }
            
            return $playerOfHighest;
        }
        
        public function getPlayerKillTablePVP($limit = false, $limitStart = 0, $limitNumber = 0) {
			$playerCreatureId = QueryUtils::getCreatureIdByName("Player");
			if (!$limit)
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed = '{$playerCreatureId}' AND killed_by_uuid = '{$this->_playerUUID}' ORDER BY id DESC");
			else 
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed = '{$playerCreatureId}' AND killed_by_uuid = '{$this->_playerUUID}' ORDER BY id DESC LIMIT {$limitStart},{$limitNumber}");
		}
		
		public function getPlayerKillDeathTablePVP($limit = false, $limitStart = 0, $limitNumber = 0) {
			$playerCreatureId = QueryUtils::getCreatureIdByName("Player");
			if (!$limit)
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE (killed = '{$playerCreatureId}' AND killed_by = '{$playerCreatureId}') AND  (killed_by_uuid = '{$this->_playerUUID}' OR killed_uuid = '{$this->_playerUUID}') ORDER BY id DESC");
			else 
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE (killed = '{$playerCreatureId}' AND killed_by = '{$playerCreatureId}') AND  (killed_by_uuid = '{$this->_playerUUID}' OR killed_uuid = '{$this->_playerUUID}') ORDER BY id DESC LIMIT {$limitStart},{$limitNumber}");
		}
		
		public function getPlayerDeathTablePVP($limit = false, $limitStart = 0, $limitNumber = 0) {
			$playerCreatureId = QueryUtils::getCreatureIdByName("Player");
			if (!$limit)
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_by = '{$playerCreatureId}' AND killed_uuid = '{$this->_playerUUID}' ORDER BY id DESC");
			else 
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_by = '{$playerCreatureId}' AND killed_uuid = '{$this->_playerUUID}' ORDER BY id DESC LIMIT {$limitStart},{$limitNumber}");
		}
		
		public function getPlayerKillTablePVE($limit = false, $limitStart = 0, $limitNumber = 0) {
			$playerCreatureId = QueryUtils::getCreatureIdByName("Player");
			$noneCreatureId = QueryUtils::getCreatureIdByName("None");
			if (!$limit)
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE (killed != '{$playerCreatureId}' AND killed != '{$noneCreatureId}') AND killed_by_uuid = '{$this->_playerUUID}' ORDER BY id DESC");
			else
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE (killed != '{$playerCreatureId}' AND killed != '{$noneCreatureId}') AND killed_by_uuid = '{$this->_playerUUID}' ORDER BY id DESC LIMIT {$limitStart},{$limitNumber}");
		}
		
		public function getPlayerKillDeathTablePVE($limit = false, $limitStart = 0, $limitNumber = 0) {
			$playerCreatureId = QueryUtils::getCreatureIdByName("Player");
			$noneCreatureId = QueryUtils::getCreatureIdByName("None");
			$blockCreatureId = QueryUtils::getCreatureIdByName("Block");
			if (!$limit)
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE ((killed != '{$playerCreatureId}' AND killed != '{$noneCreatureId}') XOR (killed_by != '{$playerCreatureId}' AND killed_by != '{$noneCreatureId}' AND killed_by != '{$blockCreatureId}'))  AND (killed_by_uuid = '{$this->_playerUUID}' OR killed_uuid = '{$this->_playerUUID}' ) ORDER BY id DESC");
			else
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE ((killed != '{$playerCreatureId}' AND killed != '{$noneCreatureId}') XOR (killed_by != '{$playerCreatureId}' AND killed_by != '{$noneCreatureId}' AND killed_by != '{$blockCreatureId}'))  AND (killed_by_uuid = '{$this->_playerUUID}' OR killed_uuid = '{$this->_playerUUID}' ) ORDER BY id DESC LIMIT {$limitStart},{$limitNumber}");
		}
		
		public function getPlayerDeathTablePVE($limit = false, $limitStart = 0, $limitNumber = 0) {
			$playerCreatureId = QueryUtils::getCreatureIdByName("Player");
			$noneCreatureId = QueryUtils::getCreatureIdByName("None");
			$blockCreatureId = QueryUtils::getCreatureIdByName("Block");
			if (!$limit)
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE (killed_by != '{$playerCreatureId}' AND killed_by != '{$noneCreatureId}' AND killed_by != '{$blockCreatureId}') AND killed_uuid = '{$this->_playerUUID}' ORDER BY id DESC");
			else
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE (killed_by != '{$playerCreatureId}' AND killed_by != '{$noneCreatureId}' AND killed_by != '{$blockCreatureId}') AND killed_uuid = '{$this->_playerUUID}' ORDER BY id DESC LIMIT {$limitStart},{$limitNumber}");
		}
		
		public function getPlayerDeathTableOther($limit = false, $limitStart = 0, $limitNumber = 0) {
			$noneCreatureId = QueryUtils::getCreatureIdByName("None");
			$blockCreatureId = QueryUtils::getCreatureIdByName("Block");
			if (!$limit)
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE (killed = '{$noneCreatureId}' OR killed = '{$blockCreatureId}') XOR (killed_by = '{$noneCreatureId}' OR killed_by = '{$blockCreatureId}') AND killed_uuid = '{$this->_playerUUID}' ORDER BY id DESC");
			else 
				return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE (killed = '{$noneCreatureId}' OR killed = '{$blockCreatureId}') XOR (killed_by = '{$noneCreatureId}' OR killed_by = '{$blockCreatureId}') AND killed_uuid = '{$this->_playerUUID}' ORDER BY id DESC LIMIT {$limitStart},{$limitNumber}");
		}
		
		public function getPlayerMostDangerousPVECreature() {
        	$ignoreID = QueryUtils::getCreatureIdByName("Player");
        	$noneID = QueryUtils::getCreatureIdByName("None");
        	$blockID = QueryUtils::getCreatureIdByName("Block");
        	
        	$highest = 0;
        	$idOfHighest = 0;
        	
        	foreach (QueryUtils::getCreatureTable() as $creatureRow) {
        		
        		if ($creatureRow['id'] == $ignoreID) continue;
        		if ($creatureRow['id'] == $noneID) continue;
        		if ($creatureRow['id'] == $blockID) continue;
        		
        		$res = $this->getPlayerDeathTableCreature($creatureRow['id']);
        		
        		if ($res)
        			$test = count($res);
        			else
        			$test = 0;
        		
        		if ($test > $highest) {
        			$highest = $test;
        			$idOfHighest = $creatureRow['id'];
        		}
        	}
        	
        	return $idOfHighest;
        }
        
        public function getPlayerMostKilledPVECreature() {
        	$ignoreID = QueryUtils::getCreatureIdByName("Player");
        	$noneID = QueryUtils::getCreatureIdByName("None");
        	$highest = 0;
        	$idOfHighest = 0;
        	
        	foreach (QueryUtils::getCreatureTable() as $creatureRow) {
        		
        		if ($creatureRow['id'] == $ignoreID) continue;
        		if ($creatureRow['id'] == $noneID) continue;
        		
        		$res = $this->getPlayerKillTableCreature($creatureRow['id']);
        		if ($res)
        		$test = count($res);
        		else
        		$test = 0;
        		
        		if ($test > $highest) {
        			$highest = $test;
        			$idOfHighest = $creatureRow['id'];
        		}
        	}
        	
        	return $idOfHighest;
        }
		
		public function getPlayerDeathTable() {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_uuid = '{$this->_playerUUID}'");
		}
		
		public function getPlayerKillPVP($uuid) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_by_uuid = '{$this->_playerUUID}' AND killed_uuid = '{$uuid}'");
		}
		
		public function getPlayerDeathPVP($uuid) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_uuid = '{$this->_playerUUID}' AND killed_by_uuid = '{$uuid}'");
		}
		
		public function getPlayerKillTableCreature($creatureTypeId) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_by_uuid = '{$this->_playerUUID}' AND killed = '{$creatureTypeId}'");
		}
		
		public function getPlayerDeathTableCreature($creatureTypeId) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_uuid = '{$this->_playerUUID}' AND killed_by = '{$creatureTypeId}'");
		}
		
		public function getPlayerKillTableType($killTypeId) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_by_uuid = '{$this->_playerUUID}' AND kill_type = '{$killTypeId}'");
		}
		
		public function getPlayerDeathTableType($killTypeId) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_uuid = '{$this->_playerUUID}' AND kill_type = '{$killTypeId}'");
		}
		
		public function getPlayerKillTableUsing($itemId) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_by_uuid = '{$this->_playerUUID}' AND killed_using = '{$itemId}'");
		} 
		
		public function getPlayerDeathTableUsing($itemId) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_uuid = '{$this->_playerUUID}' AND killed_using = '{$itemId}'");
		} 
		
		public function getPlayerKillTableProjectile($projectileId) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_by_uuid = '{$this->_playerUUID}' AND killed_projectile = '{$projectilId}'");
		}
		
		public function getPlayerDeathTableProjectile($projectilId) {
			return QueryUtils::get2DArrayFromQuery("SELECT * FROM kills WHERE killed_uuid = '{$this->_playerUUID}' AND killed_projectile = '{$projectilId}'");
		}
	}
	
?>
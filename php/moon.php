<?php
class moon  {
	private $mc;
	private $doc_log;
	
	/**
	// Basic Contructor and Destruct
	@author: h0lybyte
	@verison: 2.0
	
	**/
			public function __construct($mysql_ip, $username, $password, $database) {
			
				  $this->mc = new mysqli($mysql_ip, $username, $password, $database);
				  $this->doc_log .= '<br />Logs:<br />';
				  if (!$this->mc->connect_error) {
				  $this->doc_log .='Connected<br />';
				  }
				  else
				  {
				  $this->doc_log .='Broken connection<br />';
				  }
				 
			   }
			public function __destruct() {

				
				if($this->mc->close())
				{
					$this->doc_log .= "Closed Connection!<br />";
				}
				else
				{
					$this->doc_log .= "No connections were closed <br />";
				}
				return $this->getLog();
				$this->mc = null;
				$this->doc_log = null;
				
				
			}

	/**
	
	MoonBase Helper Methods!
	
	**/
	
	// Check function does exactly what is called, it checks if there is an active connection to the moonbase SQL
	// Returns true if its active and false if its dead.
	public function check()
	{
		
		if(!$this->mc->ping()) { return false; }
		else
		{
		return true;
		}
	}
	
	
	/*
	
	Moon Methods, including sub methods and revision methods.
	
	*/
	// Gets an Array of all the data specific to a moonid.
	public function getMoon($moonid)
	{
		if(!$this->mc->ping())
		{
		$data = "ERROR: NO PING";
		}
		else
		{
			//$sql = "SELECT * FROM moonbase";
			$result = $this->mc->query("SELECT * FROM moonbase WHERE id =".$moonid.";");

			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					return $row;
				}
			} else {
				$data = "ERROR: NO RESULTS";
			}
			return $data;
			
		
		}
		return $data;
	}
	
	/** Moon Reset UserID **/
	public function resetMoon($moonid,$userid = 1)
	{
		//$sql = "SELECT * FROM moonbase";
			$result = $this->mc->query("UPDATE moonbase SET hp = 10000, frost = 10000, gravity = 10000, control = ".$userid." WHERE id =".$moonid.";");
			if ($result->num_rows > 0) {
				// output data of each row
				return true;
			} else {
				return false;
			}
	return false;
	
	}
	
	/**
	* This is the sub
	* 2 = Victory, you control the moon
	* 1 = Attack Successful
	* 0 = Gravity Prevented your attack!
	**/
	public function MoonAttack($moonid, $userid, $dmg, $fdmg = 0, $gdmg = 0)
	{
		//$sql = "SELECT * FROM moonbase";
			$hp_ = $this->getMoonHP($moonid);
			$frost_ = $this->getMoonFrost($moonid);
			$gravity_ = $this->getMoonGravity($moonid);
			$dmg_ = $dmg;
			$frost_ = $frost_ - $fdmg;
			$frost_shield = 0;
			// Special Case #1 - if the moonbase has frost shield:
			if($frost_ > 0)
			{
			$frost_shield = $dmg_ * ($frost_ /100000);
			$dmg_ = $dmg_ - $frost_shield;
			}
			else
			{
			$frost_ = 0;
			}
			
			if($hp_ <= 0 || ($hp_ - $dmg_) <= 0) 
				{
				$this->resetMoon($moonid, $userid);
				return "You took over the Moon!"; 
				}	
			$hp_ = $hp_ - $dmg_;
			$result = $this->mc->query("UPDATE moonbase SET hp = ".$hp_.", frost = ".$frost_.", gravity = ".$gravity_." WHERE id =".$moonid.";");

			if($result !== false ) 
			{ 
			return "You did ".$dmg_." NET damage, the moon's frost shield prevented ".$frost_shield."HP worth of damage!";
			}
			else
			{
			return -1;
			}
	return -1;
	
	}
	
	
	// MOON DATA
	public function updateMoonHP($moonid, $hp)
	{
		
		//$sql = "SELECT * FROM moonbase";
			$hp_ = $hp + $this->getMoonHP($moonid);
			$result = $this->mc->query("UPDATE moonbase SET hp = ".$hp_." WHERE id =".$moonid.";");
			if($result !== false ) 
			{ 
			return true;
			}
			else
			{
			return false;
			}
		
	}

	public function getMoonHP($moonid)
	{
		return $this->getMoon_HP($moonid);
	}
	
	public function getMoon_HP($moonid)
	{
		$arry = $this->getMoon($moonid);
		return $arry['hp'];
	}
	
	public function getMoonFrost($moonid)
	{
		$arry = $this->getMoon($moonid);
		return $arry['frost'];
	}
	
	public function getMoonGravity($moonid)
	{
		$arry = $this->getMoon($moonid);
		return $arry['gravity'];
	}
	
	public function getMoons()
	{

		if(!$this->mc->ping())
		{
		$data = "ERROR: NO PING";
		}
		else
		{
			//$sql = "SELECT * FROM moonbase";
			$result = $this->mc->query("SELECT * FROM moonbase");

			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					$data.= "<br> id: ". $row["id"]. " - Name: ". $row["name"]. " - Control: " . $row["control"];
				}
			} else {
				$data = "ERROR: NO RESULTS";
			}
			
		
		}
		return $data;
	}
	
	public function UpdateMoon($moonid,$userid)
	{

		if(!$this->mc->ping())
			{
			return false;
			}
		else
			{
			//$sql = "SELECT * FROM moonbase";
			$result = $this->mc->query("UPDATE moonbase SET control = ".$userid." WHERE id=".$moonid."; ");
				  if ($result->num_rows > 0){ 
				  return true;
				  }
			return false;
			}
		return false;
		
	}
	

	
	
	
	public function getLog()
	{
	 return $this->doc_log;
	}
	public function death()
	{
		return $this->__destruct();
	}
}
?>
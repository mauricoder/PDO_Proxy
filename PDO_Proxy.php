<?php

class PDO_Proxy {
	private $master;
	private $slave;
	private $currentPDO;
	
	public function __construct()	{
		return true;
	}
	
	public function SetupMaster($dsn, $user, $password)	{
		$this->master = new PDO($dsn, $user, $password);	
		$this->currentPDO = $this->master;
	}
	
	public function SetupSlave($dsn, $user, $password) {
		$this->slave = new PDO($dsn, $user, $password);	
	}
	
	public function __call($name, $arguments)	{
		switch($name) {
			case 'setAttribute':
				return $this->CallBoth($name, $arguments);
				break;
			case 'quote':
				return $this->CallSlave($name, $arguments);
				break;
			case 'Execute':
				return $this->ChooseBySQLAndCall($arguments[0], $name, $arguments);
				break;
			case 'prepare':
				return $this->ChooseBySQLAndCall($arguments[0], $name, $arguments);
				break;
			default:
				return $this->CallMaster($name, $arguments);
				break;
		}
	}
	
	private function ChooseBySQLAndCall($sql, $name, $arguments) {
    $sql = trim($sql);
    // select to SLAVE!
    if(stripos($sql, 'SELECT') === 0 || stripos($sql, 'ROW_COUNT')) {
      return $this->CallSlave($name, $arguments); 
      //everything else to master
    } else if(stripos($sql, 'INSERT') === 0 || stripos($sql, 'UPDATE') === 0 
        || stripos($sql, 'LAST_INSERT_ID') || stripos($sql, 'ROW_COUNT')) {
      return $this->CallMaster($name, $arguments);
    } else {
    	return $this->CallMaster($name, $arguments);
    }
    
	}
	
	private function CallBoth($name, $arguments) {
		$this->CallMaster($name, $arguments);
		$this->CallSlave($name, $arguments);	
	}
	
	private function CallMaster($name, $arguments)	{
		FB::log('On Master '.$name);
		return call_user_func_array(array($this->master,$name), $arguments);
	}

	private function CallSlave($name, $arguments)	{
		FB::log('On Slave '.$name);
		return call_user_func_array(array($this->master,$name), $arguments);
	}	
	
}
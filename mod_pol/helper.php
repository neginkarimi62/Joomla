<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_pol
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class Vote {	
	private $_host 	      = 'localhost';	
	private $_database    = 'vote_system';	
	private $_dbUser      = 'negin';	
	private $_dbPwd       = 'negin';
	private $_con 	   	  = false;		
    private $_optionTable = 'options';	
    private $_voterTable  = 'voters';   
     /**         * Constructor         */       
 public function __construct()   
     {	      
  if(!$this->_con)	   
     {	         
   $this->_con = mysql_connect($this->_host,$this->_dbUser,$this->_dbPwd);	            if(!$this->_con){	
     	die('Could not connect: ' . mysql_error());	     
       }	       
     mysql_select_db($this->_database,$this->_con)|| die('Could not select database: ' . mysql_error());	
        }      
   }      
  //private functions	
private function  _query($sql)		
{		
	$result = mysql_query($sql,$this->_con);     
   	if(!$result){      
  		die('unable to query'. mysql_error());    
    	}       
  	$data= array();	
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {	 
       	$data[]=$row;	
		  			}		
	return $data; 	
	}	
	private function _alreadyVote($ip)	
	{		
	$sql    = 'SELECT * FROM '.$this->_voterTable.' WHERE ip="'.$ip.'"';			$result = $this->_query($sql);	
		return sizeof($result)>0;	
	}	
	//public functions   
     public function vote($optionId,$gid)     
   {        	 
       	$ip  = $_SERVER['REMOTE_ADDR'];	
	
		



 // 	if(!$this->_alreadyVote($ip,))
//{	  
if(!$this->_alreadyVoteAll($ip,$gid))  {
    	$sql ='INSERT INTO '.$this->_voterTable.' (id,option_id,ip,option_group) '.' VALUES(NULL,"' .mysql_real_escape_string($optionId). '","'.mysql_real_escape_string($ip).'","'.mysql_real_escape_string($gid).'")';	  
      	$result = mysql_query($sql,$this->_con);	
        	if(!$result){	        	
	die('unable to insert'. mysql_error());	 
       	} 
}       	
//}   

             }      
          public function getList()   
     {        
	$sql    = 'SELECT MAX(group_id) AS Latestquestions FROM '.$this->_optionTable;   
	
      	$data=$this->_query($sql); 
		$a=$data [0]['Latestquestions'];
		$sql1    = 'SELECT * FROM '.$this->_optionTable.' WHERE group_id >="'.$a.'"';  
		return $this->_query($sql1); 
		//return	$this->_query($sql);  
		
      	        }       
         public function showResults($gid)    
    {        
	
	$sql ='SELECT * FROM  '.$this->_optionTable.' LEFT JOIN '.'(SELECT option_id, COUNT(*) as number FROM  '.$this->_voterTable.' GROUP BY option_id) as votes'.        	' ON '.$this->_optionTable.'.id = votes.option_id WHERE group_id="'.mysql_real_escape_string($gid). '"';
	
    	return	$this->_query($sql);    
    }     
   public function getTotal($gid)   
   { 
   	
	$sql    = 'SELECT count(*)as total FROM '.$this->_voterTable.' WHERE option_group= "'.mysql_real_escape_string($gid). '"';	
		$data	= $this->_query($sql);		
	if(sizeof($data)>0){			
	return $data[0]['total'];		
	}			return 0;    
    }
	private function _alreadyVoteAll($ip,$gid)	
	{	
	//echo 'ip===';
	//echo $ip;
	//echo 'gid===';
	//echo $gid;
	
	$sql    = 'SELECT * FROM '.$this->_voterTable.' WHERE ip= "'.$ip.'" AND option_group= "'.$gid. '"';			$result = $this->_query($sql);	
	if (sizeof($result)>0) {
	echo ' Sorry!!! You allready have voted this group';
	}
	
	return sizeof($result)>0;	
	
	
		
			
	} 
	
	 public function getTotalVotes($gid)   
   { 
   
	$sql    = 'SELECT count(*)as total FROM '.$this->_voterTable.' WHERE option_group= "'.mysql_real_escape_string($gid). '"';	
		$data	= $this->_query($sql);		
	if(sizeof($data)>0){			
	return $data[0]['total'];		
	}			return 0;    
    }
	
	
	
	
	
	
}
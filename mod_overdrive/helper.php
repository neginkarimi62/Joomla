<?php

defined('_JEXEC') or die;


class Lib {	
	
    /**
	 * Checks if the  Api Plugin is activated
	*/   
   
    public function __construct(){	
	if (!(JComponentHelper::isEnabled('com_librarycard')))
		{
			JFactory::getApplication()->enqueueMessage(' Library Card Component is not installed.','notice'); 
			return;
		}
				
	}      
    	
	/**
	 * Assigns the user Info
	 * @return multitype:NULL array of the user info
	 */
	 
    public function register() {
    $user = JFactory::getUser();
	$userInfo=array('username'=>$user->email,'userID'=>$user->id);
	return $userInfo;
	} 
	
	/**
	 * searches the table inorder to find out if the user has a library card or not
	 * @param unknown $email
	 * @return unknown
	 */
	
	 public function searchDatabase($email) {
	 
					$db = JFactory::getDbo();
					$query = $db->getQuery(true)
					->select('librarycard,expiarydate,status')
					->from('#___library_card')
					->where('email='.$db->quote($email));
					$db->setQuery($query);
					$result = $db->loadObject();
					return $result;
	}	
	
	/**
	 * Finds the smallest empty library card record
	 * @return unknown
	 */
		public function asignLibrarycard() {	
		 $db = JFactory::getDbo();
					$query = $db->getQuery(true)
					->select('MIN(`id`) as small,librarycard,expiarydate') 
					->from('#___library_card')
					->where('email=" " ');
					$db->setQuery($query);
					$result1 = $db->loadObject();
					return $result1;
		}
		
		/**
		 * Get the Library Card Id and retunrs the Library card
		 * @param unknown $id
		 */
		public function getLibrarycard($id) {	
		 $db = JFactory::getDbo();
					$query = $db->getQuery(true)
					->select('librarycard') 
					->from('#___library_card')
					->where('id='.$db->quote($id));
					$db->setQuery($query);
					$result1 = $db->loadObject();
					$result1->librarycard;	
					return ($result1->librarycard);
		}
		
		/**
		 * Counts the number of librarycards in the table
		 * @return unknown
		 */		
		public function countRows(){	
		 $db = JFactory::getDbo();
					$query = $db->getQuery(true)
					->select('COUNT(*) as crows') 
					->from('#___library_card');
					$db->setQuery($query);
					$result2 = $db->loadObject();
							
     			return $result2;
		}
		
		/**
		 * checks if a record is valid to be assigned to a user
		 * @param unknown $id
		 * @return number
		 */	
	public function CheckValidity($id){
		
		$result1='';
		$currentTime = new JDate('now'); 
		$db = JFactory::getDbo();
					$query = $db->getQuery(true)
					->select('expiarydate,status,email') 
					->from('#___library_card')
					->where('id='.$db->quote($id));
					$db->setQuery($query);
					$result1 = $db->loadObject();
					
				if(empty($result1->email)&&(($result1->status)=='Yes')&&(($currentTime)<($result1->expiarydate))){
				
				return 1;
				}
				else 
				{
				return 0;
				}
						
	}
	/**
	 * updates the record with the user information
	 * @param unknown $libCardNumber
	 */			
	public function updateTable($libCardNumber){
		
		$user = JFactory::getUser();
		$UserDate=$user->registerDate;
		$email=$user->email;
		$id=$user->id;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true); 
 		$fields = array(
		$db->quoteName('dateactivated') . ' = '. $db->quote($UserDate),  
		$db->quoteName('email') . ' = ' . $db->quote($email),
		$db->quoteName('userID') . ' = ' . $db->quote($id)		
		);
		// Conditions for which records should be updated.
		$conditions = array(
		$db->quoteName('id') .  ' = ' . $db->quote($libCardNumber)
		);
		$query->update($db->quoteName('#___library_card'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->query(); 
	
	} 
		
	 
               }



	
	

	
    
	
	
	
	
	
	

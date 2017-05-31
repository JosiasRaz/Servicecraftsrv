<?php 
/**
* 
*/
namespace Box\Mod\Servicegamecraftsrv\Api;

class Client extends \Api_Abstract
{
	
	public function get_user()
	{
        return $this->di['db']->findOne('client', 'id = :id', array('id'=>$this->getIdentity()->id)) ; 
	}

	public function sign_up($data)
	{
		$error = false ;
		$craftsvr_user_id = $this->di['api_admin']->craftsrv_createUser($data)->id ;
		$user = $this->get_user() ;
		$user->craftsrv_user_id  = $craftsvr_user_id ;
        $this->di['db']->store($user) ;
		if ($error)
	        throw new \Box_Exception('Error sign up', null, 701);
		return true ;
	}
}
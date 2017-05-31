<?php 
/**
* 
*/
namespace Box\Mod\Servicegamecraftsrv\Controller ;

class Client implements \Box\InjectionAwareInterface
{
	protected $di ;

	public function setDi($di)
	{
		$this->di = $di ;
	}

	public function getDi()
	{
		return $this->di ;
	}

	public function register(\Box_App &$app)
	{
		$app->get('/servicegamecraftsrv/sign_up', 'get_signup', array(), get_class($this)) ;
	}

	public function get_signup(\Box_App $app)
	{
		return $app->render('mod_servicegamecraftsrv_sign_up') ;
	}
}
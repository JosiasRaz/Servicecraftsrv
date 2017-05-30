<?php 
/**
* 
*/
namespace Box\Mod\Servicegamecraftsrv ;

class Service implements \Box\InjectionAwareInterface
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

	public function validateOrderData(&$data)
    {
        if(!isset($data['name']) || empty($data['name'])) {
            throw new \Box_Exception('Server name is required', null, 701);
        }
    }

    public function install()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `servicegamecraftsrv` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) DEFAULT NULL,
                `game` varchar(255) DEFAULT NULL,
                `plan` int(5),
                `suspended` boolean,
                `user` int(5) NOT NULL,
                `ip` varchar(255) DEFAULT NULL,
                `port` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE(`name`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            )
        " ;

        $this->di['db']->exec($sql);
    }

    public function uninstall()
    {
        $this->di['db']->exec("DROP TABLE IF EXISTS `servicegamecraftsrv`");
    }

  //   public function create($order)
  //   {
  // //   	$product = $this->di['db']->load('product', $order->product_id) ;
  //       $config = json_decode($order->config, 1);
  //   	// $config = json_decode($product->config,true) ;
  // //   	foreach ($config['craftsrv_id'] as $craftsrv_id) {
  // //   		$api_admin = $this->di['api_admin'] ;
  // //   		$craftsrv = array_shift($api_admin->craftsrv_get_list(array('search'=>$craftsrv_id))['list']) ;
  // //   	}
  //       $model->name = $config['name'] ;
  //       return $model ;
  // //       var_dump($craftsrv) ; die() ;
		// // var_dump($config) ;die() ;
  //   }

  //   public function activate($order, $server)
  //   {
  //       var_dump($server) ; die() ;
  //       array_shift($server) ;

  //       $this->di['api_admin']->craftsrv_createServer($server) ;
  //   }
}
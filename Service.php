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
            ) ;
        " ;
        $this->di['db']->exec($sql);

        $sql = "
            ALTER TABLE `client` ADD `craftsrv_user_id` INT(5) DEFAULT NULL  ;
        " ;

        $this->di['db']->exec($sql);
    }

    public function uninstall()
    {
        $this->di['db']->exec("DROP TABLE IF EXISTS `servicegamecraftsrv`") ;
        $this->di['db']->exec("ALTER TABLE `client` DROP `craftsrv_user_id`") ;
    }

    public function create($order)
    {
        $product = $this->di['db']->load('product', $order->product_id) ;
        $product_config = json_decode($product->config) ;
        $config = json_decode($order->config);
        var_dump($config) ; var_dump($product_config) ; die() ;
        $model = $this->di['db']->dispense('servicegamecraftsrv') ;
        $model->name = $config->name ;
        $model->game = $product_config->game ;
        $model->plan = $product_config->plan_id ;
        // if (isset($config['suspended']))
            $model->suspended = true ;
        // else
            // $model->suspended = false ;
        $model->user = $order->client_id ;

        $api_admin = $this->di['api_admin'] ;
        foreach ($product_config->craftsrv_id as $craftsrv_id) {
            $craftsrv = array_shift($api_admin->craftsrv_get_list(array('search'=>$craftsrv_id))['list']) ;
        }
        var_dump($model) ; var_dump($api_admin->get_setting($craftsrv)->serverDefaultNetworkAddress) ; die() ;
        $model->ip   = $config['ip'] ;
        $model->port = $config['port'] ;
        $this->di['db']->store($model);
        return $model ;
    }

  //   public function activate($order, $server)
  //   {
  //       var_dump($server) ; die() ;
  //       array_shift($server) ;

  //       $this->di['api_admin']->craftsrv_createServer($server) ;
  //   }
}
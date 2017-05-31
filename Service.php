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
            CREATE TABLE IF NOT EXISTS `service_gamecraftsrv` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) DEFAULT NULL,
                `game` varchar(255) DEFAULT NULL,
                `plan` int(5),
                `suspended` boolean,
                `user` int(5) NOT NULL,
                `ip` varchar(15) NOT NULL,
                `port` int(6) NOT NULL,
                PRIMARY KEY (`id`)
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
        $this->di['db']->exec("DROP TABLE IF EXISTS `service_gamecraftsrv`") ;
        $this->di['db']->exec("ALTER TABLE `client` DROP `craftsrv_user_id`") ;
    }

    public function create($order)
    {
        $product = $this->di['db']->load('product', $order->product_id) ;
        $product_config = json_decode($product->config) ;
        $config = json_decode($order->config);
        $model = $this->di['db']->dispense('service_gamecraftsrv') ;
        $model->name = $config->name ;
        $model->game = $product_config->game ;
        $model->plan = $product_config->plan_id ;
        $model->suspended = false ;
        $model->user = $order->client_id ;
        $craftsrv = $this->di['api_admin']->craftsrv_get_list(array('search'=>$product_config->craftsrv_id))['list'] ;
        $craftsrv = array_shift($craftsrv) ;
        $craftsrv['deep'] = true ;
        $craftsrv = $this->di['api_admin']->craftsrv_get($craftsrv) ;
        $model->ip = $craftsrv['ip'] ;
        $model->port = $this->di['api_admin']->craftsrv_getUnusedPort($craftsrv) ;
        $this->di['db']->store($model);
        return $model ;
    }

    public function activate($order, $model)
    {
        $product = $this->di['db']->load('product', $order->product_id) ;
        $product_config = json_decode($product->config) ;
        $craftsrv = $this->di['api_admin']->craftsrv_get_list(array('search'=>$product_config->craftsrv_id))['list'] ;
        $craftsrv = array_shift($craftsrv) ;
        $config = json_decode($order->config) ;
        $server = array() ;
        $server['name'] = $model->name ;
        $server['user'] = $config->craftsrv_user_id ;
        $server['game'] = $model->game ;
        $server['plan'] = $model->plan ;
        $server['suspended'] = $model->suspended ;
        $server['ip'] = $model->ip ;
        $server['port'] = $model->port ;
        $server['craftsrv'] = $craftsrv ;
        $this->di['api_admin']->craftsrv_createServer($server) ;
        return true ;
    }
}
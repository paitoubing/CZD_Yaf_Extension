<?php
class Bootstrap extends Yaf_Bootstrap_Abstract{
	protected $config;

	public function _initConfig() {
		$this->config = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $this->config);
		Yaf_Loader::import(APPLICATION_PATH.'/conf/defines.inc.php');
	}

	public function _initError(Yaf_Dispatcher $dispatcher){
		if($this->config->application->debug){
			define('DEBUGMODE',false);
			ini_set('display_errors', 'On');
		}
		else{
			define('DEBUGMODE',false);
			ini_set('display_errors', 'Off');
		}
	}
	
	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		if(isset($this->config->application->benchmark) && $this->config->application->benchmark==true){
			$benchmark=new BenchmarkPlugin();
			$dispatcher->registerPlugin($benchmark);
		}
	
		$cookie = new CookiePlugin();
		$dispatcher->registerPlugin($cookie);
	}
	
	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		$routes=$this->config->routes;
		if(!empty($routes)){
			$router=$dispatcher->getRouter();
			$router->addConfig($routes);
		}
	}

	public function _initMemcache(){
		if(!empty($this->config->cache->caching_system)){
			Yaf_Registry::set('cache_exclude_table',explode('|', $this->config->cache->cache_exclude_table));
			Yaf_Loader::import(APPLICATION_PATH.'/library/Cache/Cache.php');
			if(isset($this->config->cache->prefix)){
				define('CACHE_KEY_PREFIX', $this->config->cache->prefix);
			}
			if(isset($this->config->cache->object_cache_enable) && $this->config->cache->object_cache_enable){
				define('OBJECT_CACHE_ENABLE',true);
			}
			else{
				define('OBJECT_CACHE_ENABLE',false);
			}
		}
		else{
			define('MYSQL_CACHE_ENABLE',false);
			define('OBJECT_CACHE_ENABLE',false);
		}
	}

	public function _initDatabase(){
		$servers=array();
		$database=$this->config->database;
		$servers[]=$database->master->toArray();
		$slaves=$database->slaves;
		if(!empty($slaves)){
			$slave_servers=explode('|', $slaves->servers);
			$slave_users=explode('|',$slaves->users);
			$slave_passwords=explode('|', $slaves->passwords);
			$slave_databases=explode('|', $slaves->databases);
			$slaves=array();
			foreach ($slave_servers as $key=>$slave_server){
				if(isset($slave_users[$key]) && isset($slave_passwords[$key]) && isset($slave_databases[$key])){
					$slaves[]=array('server'=>$slave_server,'user'=>$slave_users[$key],'password'=>$slave_passwords[$key],'database'=>$slave_databases[$key]);
				}
			}
			$servers[]=$slaves[array_rand($slaves)];
		}
		Yaf_Registry::set('database',$servers);
		if(isset($database->mysql_cache_enable) && $database->mysql_cache_enable && !defined('MYSQL_CACHE_ENABLE')){
			define('MYSQL_CACHE_ENABLE',true);
		}
		Yaf_Loader::import(APPLICATION_PATH.'/library/Db/Db.php');
		Yaf_Loader::import(APPLICATION_PATH.'/library/Db/DbQuery.php');
	}

	public function _initMailer(Yaf_Dispatcher $dispatcher){
		if(isset($this->config->smtp)){
			if(isset($this->config->smtp->server)){
				define('SMTP_SERVER', $this->config->smtp->server);
			}
			if(isset($this->config->smtp->ssl)){
				define('SMTP_SSL',$this->config->smtp->ssl);
			}
			if(isset($this->config->smtp->username)){
				define('SMTP_USERNAME', $this->config->smtp->username);
			}
			if(isset($this->config->smtp->password)){
				define('SMTP_PASSWORD', $this->config->smtp->password);
			}
			if(isset($this->config->smtp->helo)){
				define('SMTP_HELO',$this->config->smtp->helo);
			}
			Yaf_Loader::import(APPLICATION_PATH.'/library/PHPMailer/PHPMailer.php');
		}
	}
	
	public function _initView(Yaf_Dispatcher $dispatcher){
		$smarty=new Smarty_Adapter(null,$this->config->smarty);
		$smarty->registerFunction('function','truncate', 'truncate');
		$dispatcher->setView($smarty);
	}

	public function _initSite(Yaf_Dispatcher $dispatcher){
		define('SITE_URL',Tools::getHttpHost(true));
	}

}

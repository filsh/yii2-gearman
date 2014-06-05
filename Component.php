<?php

namespace filsh\yii2\gearman;

use GearmanHandler\Application;
use GearmanHandler\Worker;
use GearmanHandler\Config;

class Component extends \yii\base\Component
{
    public $servers;
    
    public $workers;
    
    public $jobs = [];
    
    private $_server;
    
    private $_worker;
    
    public function getServer()
    {
        if($this->_server === null) {
            $config = $this->createConfig($this->servers);
            $this->_server = new Application($config);
        }
        
        return $this->_server;
    }
    
    public function getWorker()
    {
        if($this->_worker === null) {
            $config = $this->createConfig($this->workers);
            $this->_worker = new Worker($config);
        }
        
        return $this->_worker;
    }
    
    protected function createConfig(array $config)
    {
        if(!isset($config['host']) || !isset($config['port'])) {
            throw new \yii\base\InvalidConfigException('Invalid config configuration.');
        }
        
        return new Config([
            'gearmanHost' => $config['host'],
            'gearmanPort' => $config['port']
        ]);
    }
}
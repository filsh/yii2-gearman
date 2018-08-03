<?php

namespace filsh\yii2\gearman;

use Yii;
use Sinergi\Gearman\Application;
use Sinergi\Gearman\Dispatcher;
use Sinergi\Gearman\Config;
use Sinergi\Gearman\Process;

class GearmanComponent extends \yii\base\Component
{
    public $servers;
    
    public $user;

    public $loopTimeout = 10;
    
    public $jobs = [];
    
    private $_application;
    
    private $_dispatcher;
    
    private $_config;
    
    private $_process;
    
    public function getApplication()
    {
        if($this->_application === null) {
            $app = new Application($this->getConfig(), $this->getProcess());
            foreach($this->jobs as $name => $job) {
                $job = Yii::createObject($job);
                if(!($job instanceof JobInterface)) {
                    throw new \yii\base\InvalidConfigException('Gearman job must be instance of JobInterface.');
                }
                
                $job->setName($name);
                $app->add($job);
            }
            $this->_application = $app;
        }
        
        return $this->_application;
    }
    
    public function getDispatcher()
    {
        if($this->_dispatcher === null) {
            $this->_dispatcher = new Dispatcher($this->getConfig());
        }
        
        return $this->_dispatcher;
    }
    
    public function getConfig()
    {
        if($this->_config === null) {
            $servers = [];
            foreach($this->servers as $server) {
                if(is_array($server) && isset($server['host'], $server['port'])) {
                    $servers[] = implode(Config::SERVER_PORT_SEPARATOR, [$server['host'], $server['port']]);
                } else {
                    $servers[] = $server;
                }
            }

            $this->_config = new Config([
                'servers' => $servers,
                'user' => $this->user,
                'loopTimeout' => $this->loopTimeout,
            ]);
        }
        
        return $this->_config;
    }
    
    public function setConfig(Config $config)
    {
        $this->_config = $config;
        return $this;
    }
    
    /**
     * @return Process
     */
    public function getProcess()
    {
        if ($this->_process === null) {
            $this->setProcess((new Process($this->getConfig())));
        }
        return $this->_process;
    }
    
    /**
     * @param Process $process
     * @return $this
     */
    public function setProcess(Process $process)
    {
        if ($this->getConfig() === null && $process->getConfig() instanceof Config) {
            $this->setConfig($process->getConfig());
        }
        $this->_process = $process;
        return $this;
    }
}
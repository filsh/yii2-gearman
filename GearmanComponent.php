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

    /**
     * @var string
     */
    public $jobPath = '@app/jobs';

    /**
     * @var string
     */
    public $jobNameSpace = 'app\jobs';

    public $jobs = [];

    private $_application;

    private $_dispatcher;

    private $_config;

    private $_process;

    /**
     * @return Application
     * @throws \yii\base\InvalidConfigException
     */
    public function getApplication()
    {
        if($this->_application === null) {
            $app = new Application($this->getConfig(), $this->getProcess());
            foreach($this->getJobs() as $name => $job) {
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

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        if($this->_dispatcher === null) {
            $this->_dispatcher = new Dispatcher($this->getConfig());
        }

        return $this->_dispatcher;
    }

    /**
     * @return Config
     */
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
                'user' => $this->user
            ]);
        }

        return $this->_config;
    }

    /**
     * @param Config $config
     * @return $this
     */
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

    /**
     * @param $name
     * @param array $params
     * @param int $priority
     * @return string
     */
    public function background($name, $params = [], $priority = Dispatcher::NORMAL)
    {
        return $this->getDispatcher()->background($name, new JobWorkload([
            'params' => $params
        ]), $priority);
    }

    /**
     * @param $name
     * @param array $params
     * @param int $priority
     * @return mixed
     */
    public function execute($name, $params = [], $priority = Dispatcher::NORMAL)
    {
        return $this->getDispatcher()->execute($name, $params, $priority);
    }

    /**
     * @return array
     * @throws \yii\base\ErrorException
     */
    public function getJobs()
    {
        $dir = Yii::getAlias($this->jobPath);

        if (!is_readable($dir)) {
            throw new \yii\base\ErrorException("Job directory ($dir) does not exist");
        }

        $files = array_diff(scandir($dir), array('..', '.'));

        foreach ($files as $fileName) {
            // strip out the file extension to derive the class name
            $className = preg_replace('/\.[^.]*$/', '', $fileName);

            // validate class name
            if (preg_match('/^[a-zA-Z0-9_]*$/', $className)) {
                $this->jobs[$className] = [
                    'class' => implode('\\', [$this->jobNameSpace, $className])
                ];
            }
        }

        return $this->jobs;
    }
}
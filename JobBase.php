<?php

namespace filsh\yii2\gearman;

abstract class JobBase extends \yii\base\Component implements JobInterface
{
    protected $name;
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @var $name string
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * @param \GearmanJob $job
     * @return \filsh\yii2\gearman\JobWorkload
     */
    protected function getWorkload(\GearmanJob $job)
    {
        $workload = null;
        if($data = $job->workload()) {
            $workload = unserialize($data);
        }
        return $workload;
    }
}
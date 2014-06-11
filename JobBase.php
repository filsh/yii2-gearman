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
}
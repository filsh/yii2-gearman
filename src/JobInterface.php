<?php

namespace filsh\yii2\gearman;

interface JobInterface extends \Sinergi\Gearman\JobInterface
{
    /**
     * @var $name string
     */
    public function setName($name);
}
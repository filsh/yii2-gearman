yii2-gearman
============

  A wrapper for https://github.com/websightnl/gearman
  Thanks for Gabriel Bull

## Installation

It is recommended that you install the Gearman library [through composer](http://getcomposer.org/). To do so, add the following lines to your ``composer.json`` file.

```json
{
    "require": {
       "filsh/yii2-gearman": "dev-master"
    }
}
```

## Configuration

```php
'components' => [
  'gearman' => [
      'class' => 'filsh\yii2\gearman\GearmanComponent',
      'servers' => [
          ['host' => '127.0.0.1', 'port' => 4730],
      ],
      'loopTimeout' => 1000,
      'user' => 'www-data',
      'jobs' => [
          'syncCalendar' => [
              'class' => 'common\jobs\SyncCalendar'
          ],
          ...
      ]
  ]
],
...
'controllerMap' => [
    'gearman' => [
        'class' => 'filsh\yii2\gearman\GearmanController',
        'gearmanComponent' => 'gearman'
    ],
    ...
],
```

## Job example

```php
namespace common\jobs;

use filsh\yii2\gearman\JobBase;

class SyncCalendar extends JobBase
{
    public function execute(\GearmanJob $job = null)
    {
        // Do something
    }
}
```

## Manage workers

```cmd
yii gearman/start --fork=true // start the workers as a daemon and fork proces
yii gearman/restart --fork=true // restart workers
yii gearman/stop // stop workers
```

## Example using Dispatcher

```php
Yii::$app->gearman->getDispatcher()->background('syncCalendar', new JobWorkload([
    'params' => [
        'data' => 'value'
    ]
])); // run in background
Yii::$app->gearman->getDispatcher()->execute('syncCalendar', ['data' => 'value']); // run synchronize
```

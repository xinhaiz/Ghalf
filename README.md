## Ghalf v0.8.0 (Beta)
- 参考Yaf以纯PHP实现的PHP框架

## Yaf
- https://github.com/laruence/php-yaf

## 环境
- PHP 5.4 +

## 目录
```
+ public
  | - index.php 
+ conf
  | - application.ini
- application/ 
  + controllers
     - Index.php 
  + views    
     |+ Index   
        - index.phtml
  - library
  - models
  - modules
  - plugins
  - Bootstrap.php  
```

```php
<?php
define('DS', DIRECTORY_SEPARATOR);
define('APP_PATH', dirname(dirname(__FILE__)));

require(APP_PATH . '/Ghalf/Application.php');

$app = new \Ghalf\Application(APP_PATH . '/conf/application.ini');
$app->bootstrap()->run();
```
## 文档
- 暂时没有
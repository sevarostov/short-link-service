<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
	/**
	 * List of routes command.
	 *
	 *
	 * @return int Exit code
	 */
	public function actionIndex()
	{
		$paths = [];
		$routes = [];

		$modules = Yii::$app->Modules;
		foreach ($modules as $key => $value) {
			array_push($routes, $key);
			if ($key !== 'debug' && $key !== 'gii') {
				$class = new \ReflectionClass($value['class']);
				$paths[] = substr($class->getFileName(), 0, strrpos($class->getFileName(), '/')) . '/controllers';
			}
		}
		array_push($paths, dirname(__DIR__) . '/controllers');

		foreach ($paths as $path) {
			$controllerlist = [];
			if ($handle = scandir($path)) {
				foreach ($handle as $file) {
					if (
						($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php')
						|| ($file === 'api')
					) {
						if ($file !== 'api') {
							$controllerlist[] = $file;
						} else {
							$handle = scandir(dirname(__DIR__) . '/controllers/' . $file);
							foreach ($handle as $file) {dump($file);
								if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php') {
									$controllerlist[] = 'api/'.$file;
								}
							}
						}
					}
				}
			}

			foreach ($controllerlist as $controller) {
				array_push($routes, lcfirst(substr($controller, 0, -14)));
				$handle = fopen($path . '/' . $controller, "r");
				if ($handle) {
					while (($line = fgets($handle)) !== false) {
						if (preg_match('/public function action(.*?)\(/', $line, $display)) {
							if (strlen($display[1]) > 2) {
								array_push($routes, strtolower($display[1]));
							}
						}
					}
				}
				fclose($handle);
			}
		}

		$routes = array_unique($routes);

		var_dump($routes);

		return ExitCode::OK;
	}
}

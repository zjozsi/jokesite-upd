<?php
/**
 * vezérlést működtető osztály
 */

namespace Ninja;

use \Ninja\Routes as Routes;


class EntryPoint
{

	private $route;
	private $routes;
	private $method;

	
	public function __construct(string $route, string $method, Routes $routes){
		
		$this->route = $route;
		$this->method = $method;
		$this->routes = $routes;

		$this->checkUrl();

	}

	public function run() {

		$routes = $this->routes->getRoutes();

		$controller = $routes[$this->route][$this->method]['controller'];
		$action = $routes[$this->route][$this->method]['action'];

		$page = $controller->$action();

		$title = $page['title'];

		if (isset($page['variables'])) {
			$output = $this->loadTemplate($page['template'], $page['variables']);
		}
		else {
			$output = $this->loadTemplate($page['template']);
		}

		include __DIR__ . '/../../templates/layout.html.php';

	}

//ha a megadott url nagybetűs, átirányítjuk uarra az url-re kisbetűvel
	private function checkUrl() {

		if ($this->route !== strtolower($this->route)){
			
			http_response_code(301);
			header('Location: ' . strtolower($this->route));
		
		}

	}
//betölti a template-et (view-t) a megfelelő változókkal
	private function loadTemplate(string $templateFileName, array $variables = []){

		extract($variables);

		ob_start();

		include __DIR__ . '/../../templates/' . $templateFileName;

		return ob_get_clean();

	}

}
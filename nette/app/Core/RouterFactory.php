<?php
declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		// FE routes
		$router->addRoute('', 'Home:default');
		$router->addRoute('deletePet/<id>', 'Home:deletePet');
		$router->addRoute('editPet/<id>', 'Home:editPet');

		// API routes
		$router->addRoute('api/pet', 'Pet:default'); // GET - list, POST - add, PUT - update
		$router->addRoute('api/pet/findByStatus', 'Pet:list'); // Route for listing pets by status
		$router->addRoute('api/pet/<id>', 'Pet:default'); // GET - list, DELETE - delete
		return $router;
	}
}

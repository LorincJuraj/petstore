<?php
declare(strict_types=1);

namespace App\UI\Pet;

use Nette;
use App\Model\PetManager;
use Nette\Application\Responses\JsonResponse;
use \Tracy\Debugger;
Debugger::enable(Debugger::Detect, __DIR__ . '/../../../log');

class PetPresenter extends Nette\Application\UI\Presenter
{
    private $petManager;

    public function __construct(PetManager $petManager)
    {
        parent::__construct();  // Ensure the parent constructor is called
        $this->petManager = $petManager;
    }

	/**
	 * Handle the default action.
	 */
	public function actionDefault()
	{
		$httpRequest = $this->getHttpRequest();
		$method = $httpRequest->getMethod();
		if ($method === 'GET') {
			// Handle GET request - List pets
			$this->actionList();
		} elseif ($method === 'POST') {
			// Handle POST request - Add a new pet
			$this->actionAdd();
		} elseif ($method === 'PUT') {
			// Handle PUT request - Update a pet
			$this->actionUpdate();
		} elseif ($method === 'DELETE') {
			// Handle DELETE request - Delete a pet
			$this->actionDelete();
		} else {
			// Handle unsupported HTTP methods
			$this->error('Method Not Allowed', 405);
		}
	}

	/**
	 * List pets.
	 */
    public function actionList($status = null)
    {
		$id = $this->getParameter('id');
		if($id) {
			$pet = $this->petManager->getPetById($id);
			$this->sendResponse(new JsonResponse($pet));
		}
        $pets = $status ? $this->petManager->getPetsByStatus($status) : $this->petManager->getAllPets();
        $this->sendResponse(new JsonResponse($pets));
    }

	/**
	 * Add a new pet.
	 */
    public function actionAdd()
    {
        $data = $this->getHttpRequest()->getPost();
		$pet = new \App\Model\Pet('', $data['name'], $data['category'], $data['photoUrls'], $data['tags'], $data['status']);

		$result = $this->petManager->addPet($pet);
		$this->sendResponse(new JsonResponse($result));
    }

	/**
	 * Update a pet.
	 */
    public function actionUpdate()
    {
		$data = $this->parsePutRequest();
		$pet = new \App\Model\Pet($data['id'], $data['name'], $data['category'], $data['photoUrls'], $data['tags'], $data['status']);
		$result = $this->petManager->updatePet($pet);
		$this->sendResponse(new JsonResponse($result));
    }

	/**
	 * Delete a pet.
	 */
    public function actionDelete()
    {
		$id = $this->getParameter('id');
        if($this->petManager->deletePet($id)) {
			$this->sendResponse(new JsonResponse(['status' => 'success']));
		} else {
			$this->error('Invalid pet value', 400);
		}
    }

	/**
	 * Parse the PUT request data.
	 */
	function parsePutRequest() {
		$input = file_get_contents('php://input');
		$data = [];
		$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
		if (strpos($contentType, 'application/json') !== false) {
			$data = json_decode($input, true); // Decode JSON into associative array
		} elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
			parse_str($input, $data); // Parse query string into an associative array
		}

		return $data;
	}
}

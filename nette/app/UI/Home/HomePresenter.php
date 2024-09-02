<?php
declare(strict_types=1);

namespace App\UI\Home;

use Nette;
use Nette\Application\UI\Form;
use App\Model\HomeManager;
use \Tracy\Debugger;
Debugger::enable(Debugger::Detect, __DIR__ . '/../../../log');

class HomePresenter extends Nette\Application\UI\Presenter
{
    public $httpRequest;
	public $httpResponse;
    private $homeManager;

    public function __construct(HomeManager $homeManager)
    {
        parent::__construct();

		$this->homeManager = $homeManager;
    }

    /**
     * Render the homepage with a list of pets.
     */
    public function renderDefault($status = null)
    {
		$pets = $this->homeManager->fetchPets($status);
		$this->template->pets = $pets;

		if($this->getParameter('isPetAdded')) {
			$this->template->isPetAdded = true;
		} else {
			$this->template->isPetAdded = false;
		}
    }


    /**
     * Create a form for adding a new pet.
     */
    protected function createComponentAddPetForm(): Form
    {
        $form = new Form;
		$form->addHidden('id'); // Hidden field to keep track of the pet ID

		$form->addText('name', 'Name:')
            ->setRequired('Please enter the pet\'s name.');

        $form->addText('category', 'Category:')
            ->setRequired('Please enter the pet\'s category.');

        $form->addTextArea('photoUrls', 'Photo URLs:')
            ->setRequired('Please enter at least one photo URL.')
		    ->setOption('description', 'Enter each photo URL on a new line.');

        $form->addTextArea('tags', 'Tags:')
            ->setRequired('Please enter at least one tag.')
		    ->setOption('description', 'Enter each tag on a new line.');

        $form->addSelect('status', 'Status:', [
            'available' => 'Available',
            'pending' => 'Pending',
            'sold' => 'Sold'
        ])->setRequired('Please select a status.');

        $form->addSubmit('send', 'Save');

        $form->onSuccess[] = [$this, 'addPetFormSucceeded'];

        return $form;
    }

    /**
     * Form submission handler for adding a new pet.
     */
    public function addPetFormSucceeded(Form $form, $values)
    {
		$postData = [
            'name' => $values->name,
            'category' => [['id' => 0, 'name' => $values->category]], // Adjust category format as needed
            'photoUrls' => explode("\n", $values->photoUrls), // Convert textarea to array
            'tags' => array_map(function ($tag, $id) {
                return ['id' => $id, 'name' => $tag];
            }, explode("\n", $values->tags), array_keys(explode("\n", $values->tags))),
            'status' => $values->status,
        ];

		if ($values->id) {
			$postData['id'] = $values->id;
        	$result = $this->homeManager->updatePet($postData);
			if ($result) {
				$this->flashMessage('Pet updated successfully.', 'success');
			} else {
				$this->flashMessage('Failed to update pet.', 'error');
			}
        	$this->redirect('default', ['isPetUpdated' => true]);
		} else {
        	$result = $this->homeManager->addPet($postData);
			if ($result) {
        		$this->flashMessage('Pet added successfully.', 'success');
			} else {
				$this->flashMessage('Failed to add pet.', 'error');
			}
        	$this->redirect('default', ['isPetAdded' => true]);
		}
    }

	/**
	 * Create a form for editing a pet.
	 */
	public function actionDeletePet($id)
	{
		$petData = $this->getPetById($id);

		if (!$petData) {
			$this->flashMessage('Pet not found.', 'error');
			$this->redirect('default'); // Redirect to the default action if pet is not found
		}

		$this->homeManager->deletePet($id);

		$this->flashMessage('Pet deleted successfully.', 'success');
		$this->redirect('default'); // Redirect back to the default action (list of pets)
	}

	/**
	 * Create a form for editing a pet.
	 */
	public function actionEditPet($id)
	{
		$petData = $this->getPetById($id);

		if (!$petData) {
			$this->flashMessage('Pet not found.', 'error');
			$this->redirect('default'); // Redirect to the default action if pet is not found
		}

		$prepairedPetData = [
			'id' => $petData['id'],
			'name' => $petData['name'],
			'category' => $petData['category'][0]['name'],
			'photoUrls' => implode("\n", $petData['photoUrls']),
			'tags' => implode("\n", array_map(function ($tag) {
				return $tag['name'];
			}, $petData['tags'])),
			'status' => $petData['status'],
		];

		$this['addPetForm']->setDefaults($prepairedPetData);
	}

	/**
	 * Create a form for deleting a pet.
	 */
	private function getPetById($id)
	{
		return $this->homeManager->getPetById($id);
	}
}

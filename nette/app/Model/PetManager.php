<?php
declare(strict_types=1);

namespace App\Model;

use SimpleXMLElement;
use Tracy\Debugger;

class PetManager
{
    private $xmlFilePath;

    public function __construct($xmlFilePath)
    {
        $this->xmlFilePath = $xmlFilePath;
    }

	/**
	 * Fetch the list of pets from the XML file.
	 */
    public function getAllPets(): array
    {
        $pets = [];
        $xml = new SimpleXMLElement(file_get_contents($this->xmlFilePath));

        foreach ($xml->pet as $petXml) {
			$pets[] = $this->processPet($petXml);
		}
        return $pets;
    }

	/**
	 * Process the XML data for a pet.
	 */
	public function processPet($petXml)
	{
		$category = [];
		$category[] = [
			'id' => (int)$petXml->category->id,
			'name' => (string)$petXml->category->name
		];

		$photoUrls = [];
		foreach ($petXml->photoUrls[0] as $photoUrl) {
			$photoUrls[] = (string)$photoUrl;
		}

		$tags = [];
		foreach ($petXml->tags[0] as $tag) {
			$tags[] = [
				'id' => (int)$tag->id,
				'name' => (string)$tag->name
			];
		}

		return new Pet(
			(int)$petXml->id,
			(string)$petXml->name,
			(array)$category,
			(array)$photoUrls,
			(array)$tags,
			(string)$petXml->status
		);
	}

	/**
	 * Add a new pet to the XML file.
	 */
    public function addPet(Pet $pet)
    {
        $xml = new SimpleXMLElement(file_get_contents($this->xmlFilePath));
        $newPet = $xml->addChild('pet');

		$pet->setBasicAttributes($newPet, $pet, $this->getLatestPetId());
		$pet->addPhotoUrls($newPet, $pet->getPhotoUrls());
		$pet->addTags($newPet, $pet->getTags());
		$pet->addCategories($newPet, $pet->getCategory());

		$xml->asXML($this->xmlFilePath);
		return $newPet;
    }

	/**
	 * Get the latest pet ID from the XML file.
	 */
	public function getLatestPetId() {
		$xml = new SimpleXMLElement(file_get_contents($this->xmlFilePath));
		$latestPet = $xml->xpath('//pet[last()]');
		return $latestPet[0]->id;
	}

	/**
	 * Update an existing pet in the XML file.
	 */
    public function updatePet(Pet $pet)
    {
		$xml = new SimpleXMLElement(file_get_contents($this->xmlFilePath));
		$petXml = $xml->xpath("//pet[id={$pet->getId()}]")[0];

		$petXml->name = $pet->getName();
		$petXml->status = $pet->getStatus();

		if($pet->getCategory()[0]['name']) {
			$petXml->category->name = $pet->getCategory()[0]['name'];
		} else {
			$petXml->category->name = $pet->getCategory()[0];
		}

		unset($petXml->photoUrls);
		$pet->addPhotoUrls($petXml, $pet->getPhotoUrls());

		unset($petXml->tags);
		$pet->addTags($petXml, $pet->getTags());

		unset($petXml->category);
		$pet->addCategories($petXml, $pet->getCategory());

		$xml->asXML($this->xmlFilePath);

		return $petXml;
    }

	/**
	 * Delete a pet from the XML file.
	 */
	public function deletePet($id)
	{
		$xml = new SimpleXMLElement(file_get_contents($this->xmlFilePath));
		$petXml = $xml->xpath("//pet[id='{$id}']");

		if (empty($petXml)) {
			Debugger::log('Pet not found.');
			return false;
		}

		$dom = dom_import_simplexml($petXml[0]);
		$dom->parentNode->removeChild($dom);

		$xml->asXML($this->xmlFilePath);
		return true;
	}

    /**
	 * Get pets by status.
	 */
	public function getPetsByStatus($status): array
    {
        return array_filter($this->getAllPets(), function ($pet) use ($status) {
            return $pet->getStatus() === $status;
        });
    }

	/**
	 * Get a pet by ID.
	 */
	public function getPetById($id)
	{
		$xml = new SimpleXMLElement(file_get_contents($this->xmlFilePath));
		$petXml = $xml->xpath("//pet[id='{$id}']");

		if (empty($petXml)) {
			Debugger::log('Pet not found.');
			return false;
		}

		return $this->processPet($petXml[0]);
	}
}

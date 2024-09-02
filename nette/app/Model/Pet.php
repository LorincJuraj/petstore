<?php
declare(strict_types=1);

namespace App\Model;

use SimpleXMLElement;
use Tracy\Debugger;

class Pet
{
    public $id;
	public $name;
    public $category;
    public $photoUrls;
	public $tags;
    public $status;

    public function __construct($id, $name, $category, $photoUrls, $tags, $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->photoUrls = $photoUrls;
		$this->tags = $tags;
        $this->status = $status;
    }

	/**
	 * Sets basic attributes for a pet.
	 */
	public function setBasicAttributes(SimpleXMLElement $newPet, Pet $pet, $latestPetId)
	{
		$newPet->addChild('id', (string) ($latestPetId + 1));
		$newPet->addChild('name', $pet->getName());
		$newPet->addChild('status', $pet->getStatus());
	}

	/**
	 * Adds photo URLs to the XML.
	 */
	public function addPhotoUrls(SimpleXMLElement $newPet, array $photoUrls)
	{
		$photoUrlsXml = $newPet->addChild('photoUrls');

		foreach ($photoUrls as $photoUrl) {
			$photoUrlsXml->addChild('photoUrl', $photoUrl);
		}
	}

	/**
	 * Adds tags to the XML.
	 */
	public function addTags(SimpleXMLElement $newPet, array $tags)
	{
		$tagsXml = $newPet->addChild('tags');

		foreach ($tags as $id => $tag) {
			if (is_array($tag)) {
				$id = $tag['id'];
				$tag = $tag['name'];
			}
			$tagXml = $tagsXml->addChild('tag');
			$tagXml->addChild('id', (string) $id);
			$tagXml->addChild('name', $tag);
		}
	}

	/**
	 * Adds categories to the XML.
	 */
	public function addCategories(SimpleXMLElement $newPet, array $categories)
	{
		$categoriesXml = $newPet->addChild('category');

		foreach ($categories as $id => $category) {
			if (is_array($category)) {
				$id = $category['id'];
				$category = $category['name'];
			}
			$categoriesXml->addChild('id', (string) $id);
			$categoriesXml->addChild('name', $category);
		}
	}

	public function getCategory() {
		return $this->category;
	}

	public function getPhotoUrls() {
		return $this->photoUrls;
	}

	public function getTags() {
		return $this->tags;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}
}

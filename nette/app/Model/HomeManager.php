<?php
declare(strict_types=1);

namespace App\Model;

use Tracy\Debugger;

class HomeManager
{
	private $apiBaseUrl;
    public function __construct()
    {
		$this->apiBaseUrl = 'http://nginx/api'; // Docker
    }

	/**
	 * Fetch the list of pets from the API.
	 */
	public function fetchPets($status = null)
	{
		$ch = curl_init();

		if ($status) {
			curl_setopt($ch, CURLOPT_URL, $this->apiBaseUrl . '/pet/findByStatus?status=' . $status);
		} else {
			curl_setopt($ch, CURLOPT_URL, $this->apiBaseUrl . '/pet');
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		$curlError = curl_error($ch);

		if ($curlError) {
			Debugger::log('Error: ' . $curlError, 'api');
			$this->flashMessage('There was an error: ' . $curlError, 'error');
			$this->redirect('this');
		}

		curl_close($ch);

		return json_decode($response, true);
	}

	/**
	 * Send a POST request to the API to add a new pet.
	 */
	public function addPet($data)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->apiBaseUrl . '/pet');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		$curlError = curl_error($ch);

		if ($curlError) {
			Debugger::log('Error: ' . $curlError, 'api');
			$this->flashMessage('There was an error: ' . $curlError, 'error');
			$this->redirect('this');
		}

		curl_close($ch);

		return json_decode($response, true);
	}

	/**
	 * Send a PUT request to the API to update a pet.
	 */

	public function updatePet($data)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->apiBaseUrl . '/pet');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		$curlError = curl_error($ch);

		if ($curlError) {
			Debugger::log('Error: ' . $curlError, 'api');
			$this->flashMessage('There was an error: ' . $curlError, 'error');
			$this->redirect('this');
		}

		curl_close($ch);

		return json_decode($response, true);
	}

	/**
	 * Send a DELETE request to the API to delete a pet by ID.
	 */
	public function deletePet($id)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->apiBaseUrl . '/pet/' . $id);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		$curlError = curl_error($ch);

		if ($curlError) {
			Debugger::log('Error: ' . $curlError, 'api');
			$this->flashMessage('There was an error: ' . $curlError, 'error');
			$this->redirect('this');
		}

		curl_close($ch);

		return json_decode($response, true);
	}

	/**
	 * Get a pet by ID from the API.
	 */
	public function getPetById($id)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->apiBaseUrl . '/pet/' . $id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		$curlError = curl_error($ch);

		if ($curlError) {
			Debugger::log('Error: ' . $curlError, 'api');
			$this->flashMessage('There was an error: ' . $curlError, 'error');
			$this->redirect('this');
		}

		curl_close($ch);

		return json_decode($response, true);
	}
}
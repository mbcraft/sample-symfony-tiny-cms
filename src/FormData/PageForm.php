<?php

namespace App\FormData;

use App\Entity\Page;

class PageForm {
	

	private ?string $title = null;

	private ?Page $chosen = null;


	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getChosen() {
		return $this->chosen;
	}

	public function setChosen(Page $chosen) {
		$this->chosen = $chosen;
	}

}
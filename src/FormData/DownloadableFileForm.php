<?php

namespace App\FormData;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\DownloadableFile;


class DownloadableFileForm {


	private ?UploadedFile $attachment = null;

	private ?DownloadableFile $chosen = null;

	public function getAttachment() {
		return $this->attachment;
	}

	public function setAttachment(?UploadedFile $attachment) {
		$this->attachment = $attachment;
	}

	public function getChosen() {
		return $this->chosen;
	}

	public function setChosen(DownloadableFile $chosen) {
		$this->chosen = $chosen;
	}


}
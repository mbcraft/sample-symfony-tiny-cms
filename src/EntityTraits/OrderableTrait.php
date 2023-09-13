<?php

namespace App\EntityTraits;


trait OrderableTrait {
	
	private $is_first = false;
    private $is_last = false;

    public function isFirst() {
        return $this->is_first;
    }

    public function markAsFirst() {
        $this->is_first = true;
    }

    public function isLast() {
        return $this->is_last;
    }

    public function markAsLast() {
        $this->is_last = true;
    }
}
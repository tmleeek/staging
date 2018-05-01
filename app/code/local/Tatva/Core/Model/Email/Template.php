<?php

class Tatva_Core_Model_Email_Template extends Mage_Core_Model_Email_Template {
	
	public function addPdfAttachment($file) {
		if (is_file($file)) {
			$attachment = $this->getMail ()->createAttachment ( file_get_contents($file) );
			$attachment->type = 'application/pdf';
			$attachment->filename = basename($file);			
		}

	}

}
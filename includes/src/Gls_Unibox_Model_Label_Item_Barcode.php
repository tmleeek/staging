<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Model_Label_Item_Barcode 
{
	public $height;
	public $barThickWidth;		
	public $barThinWidth;
	public $factor;
	public $type;
	
	public function __construct() {
		$this->height = null;
		$this->barThickWidth = null;
		$this->barThinWidth = null;
		$this->factor = null;
		$this->type = null;
	}

	public function setHeight($val) { $this->height = $val; return $this; }
	public function setBarThickWidth($val) { $this->barThickWidth = $val; return $this; }
	public function setBarThinWidth($val) { $this->barThinWidth = $val; return $this; }	
	public function setFactor($val) { $this->factor = $val; return $this; }
	public function setType($val) { $this->type = $val; return $this; }
	
	public function getHeight() { return $this->height; }
	public function getBarThickWidth() { return $this->barThickWidth; }
	public function getBarThinWidth() { return $this->barThinWidth; }
	public function getFactor() { return $this->factor; }
	public function getType() { return $this->type; }

}
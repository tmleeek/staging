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
class Gls_Unibox_Model_Label_Item_Font 
{
	public $name;		//(String) Name of the Font, defaults to "Swiss721_Cn_BT"
	public $size;		//(Integer) Fontsize
	public $face;		//(String) inverse, bold
	public $rotation; 	//rotation im Uhrzeigersinn (integer)

	public function __construct() {
		$this->name = "Swiss721_Cn_BT";
		$this->size = null;
		$this->face = null;
		$this->rotation = null;
	}
	public function setName($val) { $this->name = $val; return $this; }
	public function setSize($val) { $this->size = $val; return $this; }
	public function setFace($val) { $this->face = $val; return $this; }
	public function setRotation($val) { $this->rotation = $val; return $this; }

	public function getName() { return $this->name; }
	public function getSize() { return $this->size; }
	public function getFace() { return $this->face; }
	public function getRotation() { return $this->rotation; }
}
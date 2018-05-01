<?php
class MDN_ClientComputer_FrontController extends Mage_Core_Controller_Front_Action
{
	/**
	 * List actions to perform
	 *
	 */
	public function ListAction()
	{
		try 
		{
			$this->checkPassword();
			$user = $this->getRequest()->getParam('username');
			$xml = mage::helper('ClientComputer')->getActionsAsXml($user);
			die($xml);			
		}
		catch (Exception $ex)
		{
			die($this->returnError($ex->getMessage()));
		}
	}
	
	/**
	 * Download file
	 *
	 */
	public function downloadFileAction()
	{
		try 
		{
			$this->checkPassword();
			$user = $this->getRequest()->getParam('username');
			$fileName = $this->getRequest()->getParam('filename');
			$filepath = mage::helper('ClientComputer')->getUserExchangeDirectory($user).$fileName;
			if (file_exists($filepath))
			{
				die(file_get_contents($filepath));
			}
			else 
				throw new Exception('unable to find file');		
		}
		catch (Exception $ex)
		{
			die($this->returnError($ex->getMessage()));
		}
		

	}
	
	/**
	 * delete an action
	 *
	 */
	public function deleteFileAction()
	{
		try 
		{
			$this->checkPassword();
	
			$fileName = $this->getRequest()->getParam('filename');
			$user = $this->getRequest()->getParam('username');
			$filepath = mage::helper('ClientComputer')->getUserExchangeDirectory($user).$fileName;
			
			if (file_exists($filepath))
				unlink($filepath);
		}
		catch (Exception $ex)
		{
			die($this->returnError($ex->getMessage()));
		}
	}
	
	/**
	 * check password
	 *
	 */
	private function checkPassword()
	{
		$password = $this->getRequest()->getParam('password');
		if ($password != mage::getStoreConfig('clientcomputer/general/password'))
		{
			throw new Exception('Wrong password');
		}
	}
	
	/**
	 * Return error
	 *
	 * @param unknown_type $errorMessage
	 */
	private function returnError($errorMessage)
	{
		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>'."\n";
		$xml .= '<data>';
		$xml .= '<error msg="'.$errorMessage.'" />';
		$xml .= '</data>';
		return $xml;
	}
}
    
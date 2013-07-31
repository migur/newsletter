<?php

/**
 * Allows you to connect Automail manager's methods to event in code.
 */
class plgMigurAutomail extends JPlugin
{
    protected $_automailer = null;
	
    /**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterSubscribe($sid, $lid)
    {
		$args = func_get_args();
		return call_user_func_array(
			array($this->_getAutomailer(), 'processSubscription'),
			$args);
    }
    
	/**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
	public function onMigurAfterSubscriberImport($options)
    {
		$args = func_get_args();
		return call_user_func_array(
			array($this->_getAutomailer(), 'processSubscription'),
			$args);
    }

	
	/**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterSubscriberAssign($options)
    {
		$args = func_get_args();
		return call_user_func_array(
			array($this->_getAutomailer(), 'processSubscription'),
			$args);
    }
	

    /**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterUnsubscribe($sid, $lids)
    {
		$args = func_get_args();
		return call_user_func_array(
			array($this->_getAutomailer(), 'processUnsubscription'),
			$args);
    }
	

    /**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterSubscriberUnbind($sid, $lids)
    {
		$args = func_get_args();
		return call_user_func_array(
			array($this->_getAutomailer(), 'processUnsubscription'),
			$args);
    }
	
	
    /**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterSubscriberDelete($sid)
    {
		$args = func_get_args();
		return call_user_func_array(
			array($this->_getAutomailer(), 'processSubscriberDeletion'),
			$args);
    }
	
	
	/**
	 * Get instance of automailer manager
	 * @return type 
	 */
	protected function _getAutomailer()
	{
		if (!$this->_automailer) {
			JLoader::import('models.automailing.manager', JPATH_COMPONENT_ADMINISTRATOR, '');
			$this->_automailer = new NewsletterAutomailingManager();
		}
		
		return $this->_automailer;
	}
	
	/**
	 * Set an instance of automailer manager
	 * @return type 
	 */
	public function setAutomailer($instance = null)
	{
		if (!$instance) {
			$instance = new NewsletterAutomailingManager();
		}
		
		$this->_automailer = $instance;
		return $this->_automailer;
	}
	
}

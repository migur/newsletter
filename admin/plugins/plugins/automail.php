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
		return call_user_func_array(
			array($this->_getAutomailer(), 'processSubscription'),
			func_get_args());
    }
    
	/**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
	public function onMigurAfterSubscriberImport($options)
    {
		return call_user_func_array(
			array($this->_getAutomailer(), 'processSubscription'),
			func_get_args());
    }

	
	/**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterSubscriberAssign($options)
    {
		return call_user_func_array(
			array($this->_getAutomailer(), 'processSubscription'),
			func_get_args());
    }
	

    /**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterUnsubscribe($sid, $lids)
    {
		return call_user_func_array(
			array($this->_getAutomailer(), 'processUnsubscription'),
			func_get_args());
    }
	

    /**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterSubscriberUnbind($sid, $lids)
    {
		return call_user_func_array(
			array($this->_getAutomailer(), 'processUnsubscription'),
			func_get_args());
    }
	
	
    /**
     * Proxies to Automailing handler
     * 
     * @param type $options 
     */
    public function onMigurAfterSubscriberDelete($sid)
    {
		return call_user_func_array(
			array($this->_getAutomailer(), 'processSubscriberDeletion'),
			func_get_args());
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
}

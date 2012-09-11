<?php

class NewsletterPlugin extends JPlugin 
{
    // Description of what plugin intended for
    protected $_description = "PLG_MIGUR_{PLUGINEXTENSION}_DESCRIPTION";

    // Verbal name
    protected $_title = "PLG_MIGUR_{PLUGINEXTENSION}_TITLE";

    // Prefix for *State methods
    protected $_statePrefix = 'com_newsletter.plugins.';
    
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);
        
        $this->loadLanguage();
    }

    
    /**
     * Getter for a type
     * 
     * @return string 
     */
    public function getType() 
    {
        return $this->_type;
    } 
    
    
    /**
     * Getter for a name
     * 
     * @return string 
     */
    public function getName() 
    {
        return $this->_name;
    } 


    /**
     * Getter for a description
     * 
     * @return string 
     */
    public function getDescription() 
    {
        return $this->_description;
    } 

    
    /**
     * Getter for a title
     * 
     * @return string 
     */
    public function getTitle() 
    {
        return $this->_title;
    } 

    
    /**
     * Getter for a name
     * 
     * @return string 
     */
    public function getDispatcher() 
    {
        return $this->_subject;
    } 

    
    /**
     * Use it to store all variables you need in future
     * 
     * @param string $key
     * @param type $value 
     */
    public function setState($key, $value) 
    {
        $key = $this->_statePrefix . $this->getType() . '.' . $this->getName() . '.' . $key;
        JFactory::getApplication()->setUserState($key, $value);
    }
  
    
    /**
     * Allows to get stored variables
     * 
     * @param string $key
     * @return type 
     */
    public function getState($key) 
    {
        $key = $this->_statePrefix . $this->getType() . '.' . $this->getName() . '.' . $key;
        return JFactory::getApplication()->getUserState($key);
    }

    
    /**
     * Must return the info about plugin for rendering of its icon
     * 
     * @param JObject $list - object of a List
     * @param array $options
     * 
     * @return array 
     */
    public function onMigurImportShowIcon($options = array()) 
    {}
    

    /**
     * Handler for onMigurImportPrepareShowRules event.
     * Must return the list of rules
     * 
     * @param JObject $list - object of a List
     * @param array $options
     * 
     * @return array 
     */
    public function onMigurImportShowRules($listId, $options = array()) 
    {}

    
    /**
     * Handler for onMigurImportPrepareRule event.
     * This method returns content that should be rendered 
     * after selection of an appropriate rule.
     * 
     * @return array
     */
    public function onMigurImportShowRule($list, $options = array()) 
    {}
    
    
    /**
     * This method should implement the functionality 
     * that privide the component for a list of data.
     * 
     * As proposal. Add ability to return the rendered form content
     * that component should only display.
     * 
     * @return array
     */
    public function onMigurImportBeforeExecRule($list, $options = array()) 
    {}

    /**
     * This method performs main functionality based on input data.
     * In Import plugins for example it should return 
     * list of USER objects to import into component.
     * 
     * @return array List of objects
     */
    public function onMigurImportExecRule($list, $options = array()) 
    {}
}

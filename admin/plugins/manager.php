<?php

class NewsletterPluginManager extends JDispatcher
{
    protected $_dispatched = false;
    
    public $pluginName = null;
    
    public $pluginGroup = null;

    public $pluginEvent = null;
    
    public function factory($group)
    {
        include_once(realpath(__DIR__) . DIRECTORY_SEPARATOR . 'manager' . DIRECTORY_SEPARATOR . $group . '.php');
        $class = 'NewsletterPluginManager'.ucfirst($group);
        return new $class;
    }

    
    public function trigger($options, $data = array()) 
    {
		if (empty($options['group']) || empty($options['event'])) {
            return false;
        }

        $this->pluginName = !empty($options['name'])? $options['name'] : null;
        $this->pluginGroup = $options['group'];
		$this->pluginEvent = $options['event'];
        
        $this->import($this->pluginGroup, $this->pluginName);

        $res = array();
        for($this->_dispatched = false; $this->_dispatched !== true;) {
            // Trigger and finish with it
            $this->_dispatched = true;
            $res = parent::trigger($this->pluginEvent, $data);
        }
        
        
        // Wrapping of event...
        if (is_callable(array($this, $options['event']))) {
            
            // Process result of each plugin
            foreach($res as $idx => $plgResult) {
                $res[$idx] = $this->$options['event']($options, $data, $plgResult);
            }    
        }
        
        return $res;
    }
    
    
    public function getPlugin($name, $group = null) 
    {
        foreach ($this->_observers as $plg) {
            if ($plg instanceof NewsletterPlugin && $plg->getName() == $name && $plg->getType() == $group) {
                return $plg;
            }
        }
        
        return null;
    }
    
    
    public function forward($event) 
    {
        $this->_dispatched = false;
        $this->pluginEvent = $event;
    }

    
    public function redirect($event, $options = array()) 
    {
        $availables = array('pluginname','plugingroup','pluginevent','option','format','tmpl');
        
        $uri = JUri::getInstance();
        foreach (JRequest::get() as $name => $val) {
            if (in_array($name, $availables)) {
                $uri->setVar($name, $val);
            }
        }

        // Workaround for J! exploding of TASK variable
        if(JRequest::getVar('completetask')) {
            $uri->setVar('task', JRequest::getVar('completetask')); 
        }

        // Adding additional parameters
        foreach($options as $name => $val) {
            $uri->setVar($name, $val);
        }
        
        // Adding event
        $uri->setVar('pluginevent', $event);
        
        JFactory::getApplication()->redirect($uri->toString(array(
            'scheme', 'host', 'port', 'path', 'query', 'fragment')));
        jexit();
    }
    
    
    public function import($pluginGroup, $pluginName)
    {
        JPluginHelper::importPlugin($pluginGroup, $pluginName, true, $this);
    }
}

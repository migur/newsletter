<?php

class NewsletterPluginManagerImport extends NewsletterPluginManager
{
    
    /**
     * Doing importing after plugin returns data.
     * Assumed that data came from only ONE plugin
     * (only one plugin has been handled).
     * 
     * @param type $options 
     */
    public function onMigurImportExecRule($options, $data, $resultset)
    {
        // Gets data of first plugin
        $collection = (array)$resultset;
        
        $listId = $data[0];
        
        $list = JModel::getInstance('List', 'NewsletterModel');
        
        // Check if collection of objects is in LIST subarray
        if (isset($collection['list']) && is_array($collection['list'])) {
            $collection = $collection['list'];
        }
        
        $result = $list->importCollection(
            $listId,
            $collection, 
            array(
                'overwrite'      => JRequest::getBool('import_overwrite',false),
                'subscriberType' => JRequest::getString('subscriber_type', 'migur'),
				'autoconfirm'    => true,
				'sendRegmail'    => false
        ));
        
        if ($result['errors'] == 0) {
            JFactory::getApplication()->enqueueMessage(
                JText::sprintf('COM_NEWSLETTER_N_SUBSCRIBERS_IMPORTED_SUCCESSFULLY', count($collection))
            );
        } else {
            JFactory::getApplication()->enqueueMessage(
                JText::_('COM_NEWSLETTER_IMPORT_ERROR'),
                'error'
            );
            
        }
        
        return $result;
    }
    

    /**
     * Adds the list_id to default parameters.
     * 
     * @param type $event
     * @param type $options
     * @return type 
     */
    public function redirect($event, $options = array()) 
    {
        if (!isset($options['list_id'])) {
            $options['list_id'] = JRequest::getInt('list_id', 0);
        }
        
        return parent::redirect($event, $options);
    }
}

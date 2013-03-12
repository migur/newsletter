<?php

class plgListImportexample extends NewsletterPlugin
{
    // Description of what plugin intended for
    protected $_description = "PLG_MIGUR_IMPORTEXAMPLE_DESCRIPTION";

    // Verbal name
    protected $_title = "PLG_MIGUR_IMPORTEXAMPLE_TITLE";
	
	protected $_name = 'importexample';
    
    /**
     * Handler for onMigurImportPrepareShowRules event.
     * Must return the list of rules
     * 
     * @param JObject $list - object of a List
     * @param array $options
     * 
     * @return array 
     */
    public function onMigurImportShowIcon($options = array()) 
    {
        return array(
			'icon' => JUri::root().'/administrator/components/com_newsletter/extensions/plugins/list/importexample/icon.png',
			'title' => JText::_('PLG_MIGUR_IMPORTEXAMPLE_TITLE'),
			'name' => $this->getName()
		);
    }
    

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
    {
        //  If post then do some checks... 
        if (JRequest::getMethod() == 'POST') {
            
            if (empty($options['rules'])) {
                
                // If an error occured then notice about it
                JFactory::getApplication()->enqueueMessage('Please select a rule', 'error');
                
            } else {   
                
                // All ok. Let's store state and go to next step!
                $this->setState('rule', $options['rules']);
                $this->getDispatcher()->redirect('onMigurImportShowRule');
            }    
        }

        $this->setState('rule', null);
		
        // Return data for controls to display
		return array(
            'options' => array(
                'rules' => array(
					'list' => array(
						array(
							'value'    => '0',
							'text'     => '- Please select -',
						),
						array(
							'value'    => 'rule1',
							'text'     => 'The title of a rule',
						),
						array(
							'value'    => 'rule2',
							'text'     => 'The title of a second rule',
							'selected' => true, // optional
						)
					),
					'selected' => !empty($options['rules'])? $options['rules'] : null
                )
            ),
            'helpText' => 'Choose the rule you want to use for importing:'
        );    
    }

    
    /**
     * This method returns content that should be rendered 
     * after selection of an appropriate rule.
     * 
     * Assumed that plugin uses templates for rendering rules.
     * It may be just text or a complex page with lot of controls.
     * All the data from this form if it provided will be proxied 
     * to execOption AS IS.
     * 
     * @return array
     */
    
    /**
     * Handler for onMigurImportPrepareRule event.
     * There should be placed all
     * 
     * @param JObject $list - object of a List
     * @param array $options
     * 
     * @return array 
     */
    public function onMigurImportShowRule($list, $options = array()) 
    {
        // Check if ID is present
        $rule = $this->getState('rule');
        if(!$rule) {
            JFactory::getApplication()->enqueueMessage('Please select rule first!', 'error');
            $this->getDispatcher()->redirect('onMigurImportShowRules');
        }

        if($rule != 'rule1') {
            JFactory::getApplication()->enqueueMessage('Rule is not implemented. Please select another one.', 'error');
            $this->getDispatcher()->redirect('onMigurImportShowRules');
        }
		
        if (JRequest::getMethod() == 'POST') {
        
            switch($rule) {
                
                case 'rule1' : 
                    
                    // Some validation
                    if (empty($options['category']) || empty($options['isactive'])) {
                        JFactory::getApplication()->enqueueMessage('Please fill all fields', 'error');
                        $this->getDispatcher()->redirect('onMigurImportShowRule');
                    }

                    // Storing of a state for further usage
                    $this->setState('category', $options['category']);
                    $this->setState('isactive', $options['isactive']);
                    break;
                    
                default:
                    JFactory::getApplication()->enqueueMessage('Unknown rule', 'error');
                    $this->getDispatcher()->redirect('onMigurImportShowRules');
                    break;
            }
            
            $this->getDispatcher()->forward('onMigurImportBeforeExecRule');
			return;
        }
        
		return array(
            'options' =>array(
                'category' => array(
					'list' => array(
						array(
							'value'    => '0',
							'text'     => '- Please select -',
						),
						array(
							'value'    => 'cat1',
							'text'     => 'Category1',
							'selected' => true, // optional
							'disabled' => true, // optional 
							'type'     => 'select', // optional [select, radio, checkbox]
							'template' => 'tpl/rule1.phtml', // optional [name of a template file]
						),
						array(
							'value'      => 'cat2',
							'text'       => 'Category2',
							'noTemplate' => true // optional. If there is no need to render additional forms for this rule
						)
					),
					'select' => 'cat1',
					'label' => 'Select category'
                ),
                'isactive' => array(
					'list' => array(
						array(
							'value'    => '0',
							'text'     => '- Please select -',
						),
						array(
							'value'      => '1',
							'text'       => 'JYES',
						),
						array(
							'value'      => '0',
							'text'       => 'JNO',
						)
					),
					'label' => 'Import only selected users'
				)
            ),
            'helpText' => 'Choose which users to import by selecting some of the cases below:'
        );    
    }
    
    
    /**
     * This method should implement the functionality 
     * that privide the component for a list of data.
     * 
     * As proposal. Add ability to return the rendered form content
     * that component should only display.
     * 
     * 
     * @return array
     */
    public function onMigurImportBeforeExecRule($list, $options = array()) 
    {
        // There you can return data for previous reviewing
        return $this->_getData();
	}

    /**
     * This method should implement the functionality 
     * that creates a collection of objects that will be imported into component.
     * 
     * @return array Collection of objects with properties 'head' and 'list'.
     * Head - object = {field_name: verbal_field_name, field_name2: verbal_field_name2,... }.
     * List contains object = {field_name: value, field_name2: value2}
     * Available fields: 'name', 'email', 'id'.
     * Required fields: email.
     * All other fields will be ignored. 
     * If 'id' provided and J! user with 'id' present
     * in system then 'name' and 'email' fields will be ignored. 
     * J! user with this 'id' will be used.
     * Example:{
     *          'head': {
     *               'name': 'User name',
     *               'email': 'User email'
     *          },
     *          'list': [
     *               {'name': 'Elvis Presley',
     *                'email': 'flyelvisfly@gmail.com'},
     * 
     *               {'name': 'Albert Einstein',
     *                'email': 'greatalrbert@gmail.com',
     *                'category': 'scientist'}, <-- ignored
     * 
     *               {'id': '12345', <-- all other fields wil be ignored
     *                'email': 'no matter'}
     *          ]
     *         }
     */
    public function onMigurImportExecRule($list, $options = array()) 
    {
        // There you can return data for previous reviewing
        return $this->_getData();
	}
    

    public function _getData()
    {
        $rule = $this->getState('rule');
        
        if (empty($rule)) {
            JFactory::getApplication()->enqueueMessage('Unknown rule', 'error');
            $this->getDispatcher()->redirect('onMigurImportShowRules');
        }
        
        if($rule == 'rule1') {
            
            if(!$this->getState('category') || !$this->getState('isactive')) {
                JFactory::getApplication()->enqueueMessage('Please fill all fields', 'error');
                $this->getDispatcher()->redirect('onMigurImportShowRule');
            }
            
            // Do seletct or something else that returns a list of objects
            $category = $this->getState('category');
            $isactive = $this->getState('isactive');
            
            return (object)array(
                'head' => (object)array(
                    'name' => JText::_('User name'),
                    'email' => JText::_('User email'),
                    'category' =>  JText::_('Category')
                ),
                'list' => array(
                    (object)array(
                        'id' => '12345',
                        'name' => 'Elvis Presley',
                        'email'  => 'flyelvisfly@gmail.com',
                        'category' => $category
                    ),
                    (object)array(
                        'id' => '23456',
                        'name' => 'Albert Einstein',
                        'email'  => 'greatalrbert@gmail.com',
                        'category' => $category
                    ),
                    (object)array(
                        'id' => '34567',
                        'name' => 'William Shakespeare v2.0',
                        'email'  => 'shakespeare@gmail.com',
                        'category' => $category
                    ),
                    (object)array(
                        'id' => '46',
                        'name' => 'Ignored name',
                        'email'  => 'ignored@gmail.com',
                        'category' => $category
                    ),
                    (object)array(
                        'id' => '47',
                        'name' => 'Ignored name2',
                        'email'  => 'ignored2@gmail.com',
                        'category' => $category
                    ),
                    (object)array(
                        'name' => 'Assigned by email2',
                        'email'  => 'august-ru3@mail.ru',
                        'category' => $category
                    ),
                    (object)array(
                        'id' => '12345678',
                        'name' => 'Should be skipped!'
                    )                    
                )    
            );
        }
    }
}


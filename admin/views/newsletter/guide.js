
Migur.define("guide", function() {
	
    // You want the request dialog instance to set the onRequest message,
    // so you have to do it in two steps.

	var steps = [
			{
				target:  {
					dom: function(){
						return $$('#tabs-newsletter > .tabs')[1];
					},
					event: 'click'
				},
				needle:  {
					dom: function(){
						return $$('#tabs-newsletter > .tabs')[1];
					}
				},
                overlay: {
                    content: Joomla.JText._('CLICK_THE_HTML_TAB','Click the HTML tab!')
                }
            }, {
                target:  {
                    dom: '#jform_t_style_id',
                    event: 'change'
                },
                needle:  {
                    dom: '#jform_t_style_id'
                },
                overlay: {
                    content: Joomla.JText._('CHOOSE_A_TEMPLATE_FIRST','Choose a template first!')
                }
            }, {
                target:  {
                    dom: '#html-area',
                    event: 'drop'
                },
                needle:  {
                    dom: '#acc-modules-native'
                },
                overlay: {
                    content: Joomla.JText._('PICK_A_MODULE_AND_DRAG_IT_INTO_THE_TEMPLATE','Pick a module and drag it into the template!')
                }
            }, {
                target:  {
                    dom: function(){
                        return $$('#html-area .module .settings')[0];
                    },
                    event: 'click'
                },
                needle:  {
                    dom: function(){
                        return $$('#html-area .module .settings')[0];
                    },
                    xCorrection: -10,
                    yCorrection: 10
                },
                overlay: {
                    content: Joomla.JText._(
						'YOU_CAN_MODIFY_SETTINGS_FOR_THIS_MODULE_BY_CLICKING_HERE',
						'You can modify settings for this module by clicking here!'
					)
                }
            }, {
                needle:  {
                    dom: function(){
                        return $$('#html-area .module .settings')[0];
                    }
                },
				overlay: {
					content: Joomla.JText._('WELL_DONE', 'Well done!')+'<br />'+Joomla.JText._('NOW_YOU_KNOW_ALL_YOU_NEED','Now you know all you need!')
				}
			}
        ];

        Migur.createWidget(
            new Element('div'),
            {
                body: '<div class="guide-stop"></div><div class="guide-tip"><div class="guide-content"></div></div><div class="guide-pointer"></div>',
                stopControl: true,
                steps: steps
			},
			Migur.widgets.guide
		);
});


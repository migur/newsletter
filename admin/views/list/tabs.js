window.addEvent('domready', function() {

	var tabs = $$('ul.nav.nav-tabs a');

	var activeTab = Cookie.read('migur-tab-active');
	if (activeTab !== undefined && tabs[activeTab] !== undefined) {
		tabs[activeTab].fireEvent('click');
	}
});

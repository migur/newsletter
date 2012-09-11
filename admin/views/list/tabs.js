window.addEvent('domready', function() {

	var activeTab = Cookie.read('migur-tab-active');
	if (activeTab) {
		$$(activeTab)[0].fireEvent('click');
		Cookie.write('migur-tab-active', null);
	}
});

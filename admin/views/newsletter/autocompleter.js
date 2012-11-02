
Migur.define("autocompleter", function() {
	
	// Test source, list of tags from http://del.icio.us/tag/
	//var tokens = [['.net', 'net2', 0], ['2008', '20082', 1], ['3d', 'advertising', 2]];

	// Our instance for the element with id "demo-local"
	this.autocompleter = new Autocompleter.Local('jform_newsletter_preview_email', [], {
		'minLength': 1, // We need at least 1 character
		'selectMode': false, // Instant completion
		'multiple': true // Tag support, by default comma separated
	});

	//previewTextBox.container.addClass('textboxlist-loading');
	new Request({
		url: '?option=com_newsletter&task=newsletter.autocomplete',
		onSuccess: function(res){

			this.autocompleter.tokens = JSON.decode(res);
		}
	}).send();


// sample data loading with json, but can be jsonp, local, etc.
// the only requirement is that you call setValues with an array of this format:
// [
//	[id, bit_plaintext (on which search is performed), bit_html (optional, otherwise plain_text is used), autocomplete_html (html for the item displayed in the autocomplete suggestions dropdown)]
// ]
// read autocomplete.php for a JSON response exmaple
});

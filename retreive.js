function retrieval(array, jUrl){ 
	function Story (title, url, ppl){
			this.i = title;
			this.path = url;
			this.visitors = ppl;
		}
	array = [];
	$.getJSON(jUrl, function(data){
		$.each( data.pages, function(i,v){
			var story = {};
			var pURL = v.path,
			people = v.stats.people,
			pageRaw = v.title,
			page = pageRaw.split('|')[0],
			story = new Story(page, pURL, people);
			array.push(story);
			})
		output = "chartbeatCallback(" + JSON.stringify(array) + ");";
		document.write(output);
	});
}




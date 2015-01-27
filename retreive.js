function retrieval(array, jUrl){
	function Story (title, url, ppl){
			this.i = title;
			this.path = url;
			this.visitors = ppl;
		}
	array = [];
	var hostName = "fox10tv.com";
	hnLen = hostName.length;
	$.getJSON(jUrl, function(data){
		$.each( data.pages, function(i,v){
			var story = {};
			var pURL = v.path.slice(hnLen).replace(/\//g, '\\/'),
			people = v.stats.people,
			pageRaw = v.title,
			page = pageRaw.split('|')[0].replace("'", "\u2019"),
			story = new Story(page, pURL, people);
			array.push(story);
			});
		var pre = document.createElement("PRE");
		output = document.createTextNode("chartbeatCallback(" + JSON.stringify(array) + ");");
		pre.appendChild(output);
		document.body.appendChild(pre);
	});
}
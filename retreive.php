<!DOCTYPE html>
<html>
<head></head>
<body>
<pre style="word-wrap: break-word; white-space: pre-wrap;">chartbeatCallback(<?php
$url = 'http://api.chartbeat.com/live/toppages/v3/?apikey=536ea3158f1deac5c3437e7a5957ee3a&limit=20&host=fox10tv.com';
$json = file_get_contents($url);
$dec = json_decode($json);
//print_r($dec);
class Story
	{
	public $i;
	public $path;
	public $visitors;
	public function __construct($i, $path, $visitors){
	$this->i = $i;
	$this->path = $path;
	$this->visitors = $visitors;
	}
}

function writeChartbeat($json){
	$stories = array();
	foreach ($json->pages as $page){
			$link = $page->path;
			$users = $page->stats->people;
			$title = substr($page->title, 0, -20);
			$story = new Story($title, $link, $users);
			array_push($stories, $story);
		}
	$storiesjson = json_encode($stories);
	print_r($storiesjson);
}

writeChartbeat($dec);

/*function retrieval(array, jUrl){
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
}*/

?>);
</pre>
</body>
</html>
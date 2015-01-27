<?php 
	header('Content-Type:application/javascript');//set MIME type to javascript
//settings
$cache_ext  = '.php'; //file extension
$cache_time     = 180;  //Cache file expires afere these seconds (1 hour = 3600 sec)
$cache_folder   = 'cache/proxy/'; //folder to store Cache files
// $ignore_pages   = array('', ''); //don't need only one page to output

$dynamic_url    = 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] /*. $_SERVER['QUERY_STRING']*/; // requested dynamic page (full url)
$cache_file     = $cache_folder.md5($dynamic_url).$cache_ext; // construct a cache file
$ignore = (in_array($dynamic_url,$ignore_pages))?true:false; //check if url is in ignore list

if (!$ignore && file_exists($cache_file) && time() - $cache_time < filemtime($cache_file)) { //check Cache exist and it's not expired.
    ob_start('ob_gzhandler'); //Turn on output buffering, "ob_gzhandler" for the compressed page with gzip.
    readfile($cache_file); //read Cache file
    echo '<!-- cached page - '.date('l jS \of F Y h:i:s A', filemtime($cache_file)).', Page : '.$dynamic_url.' -->';
    ob_end_flush(); //Flush and turn off output buffering
    exit(); //no need to proceed further, exit the flow.
}
//Turn on output buffering with gzip compression.
ob_start('ob_gzhandler'); 
######## Your Website Content Starts Below #########
?>
<?php
	header('Content-Type:application/javascript');//set MIME type to javascript
?>
chartbeatCallback(<?php
$host = $_GET["host"] . ".com"; //hostname in Chartbeat - i.e. "fox10tv"
$url = 'http://api.chartbeat.com/live/toppages/v3/?apikey=536ea3158f1deac5c3437e7a5957ee3a&limit=10&host=' . $host;
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

function writeChartbeat($json, $hostName){
	$stories = array();
	$len = strlen($hostName);
	foreach ($json->pages as $page){
			$link = substr($page->path, $len);
			$users = $page->stats->people;
			$titleLong = $page->title;
			$titleChunk = explode("- F",$titleLong);
			$title = $titleChunk[0];
			$story = new Story($title, $link, $users);
			array_push($stories, $story);
		}
	$storiesjson = json_encode($stories);
	echo($storiesjson);
}

writeChartbeat($dec, $host);

?>);

<?php
######## Your Website Content Ends here #########

if (!is_dir($cache_folder)) { //create a new folder if we need to
    mkdir($cache_folder);
}
if(!$ignore){
    $fp = fopen($cache_file, 'w');  //open file for writing
    fwrite($fp, ob_get_contents()); //write contents of the output buffer in Cache file
    fclose($fp); //Close file pointer
}
ob_end_flush(); //Flush and turn off output buffering

?>

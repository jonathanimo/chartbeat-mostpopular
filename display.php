<?php header('Cache-Control: max-age=1000, public, must-revalidate');
    header('Connection: keep-alive');
?>
<?php
$host = $_GET["host"] != ''?"'".$_GET["host"] . "'": "'fox10tv'";
  //  $host = "'".$_GET["host"] . "'"; //hostname in Chartbeat - i.e. "fox10tv"
    //$key = "'" . $_GET["key"] . "'"; //your API key, get it from the chartbeat API explorer (https://chartbeat.com/docs/api/explore/)
?>
<!DOCTYPE html>
<html>
<head>
  <style>
    body {
     margin:0;
     padding: 0;
     border:0;
     outline:none;
     vertical-align: top;
     font-size: 1em;
     vertical-align: baseline;
     background: transparent;
 }

 a:visited {
     color: #7197ae;
     text-decoration: none;
 }
 a {
     color: #347ca8;
     text-decoration: none;
 }
 a.active {margin-right: 8px;color:black;position:relative;}
 .trending li{padding-left:17px;position:relative; min-height: 58px}

@-webkit-keyframes fadeIn {
  0%   { opacity: 0; }
  100% { opacity: 1; }
}
@-moz-keyframes fadeIn {
  0%   { opacity: 0; }
  100% { opacity: 1; }
}
@-o-keyframes fadeIn {
  0%   { opacity: 0; }
  100% { opacity: 1; }
}
@keyframes fadeIn {
  0%   { opacity: 0; }
  100% { opacity: 1; }
}

 .trending-up + div{
    background-color:green;
    color:#282828 ;
 }
 .trending-down + div{
    background-color:red;
        color:#282828;
 }
 .trending-right + div {
    background-color:white;
 }


 </style>
</head>
<body>

  
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

 <div id="MostPopularStories"></div>

 <script type="text/javascript">

 (function($)
 {
    $.fn.MostPopularStories = function( mppOptions )
    {

        var mppDefaultSettings =
        {
            numberOfHeadlines   :   5
        };
        var mppOptions = $.extend(mppDefaultSettings, mppOptions);
        //Change _url to location of proxy with the host variable.

        var _url = 'proxy.php?host=fox10tv';
        var _currentPageTitle = '',
        _currentLinkPath = '',
        _currentPageVisitors = '',

        _oldRank = 0,
        _currentRank = 0,

        _currentPageRankList = new Array(),
        _currentPageTitleList = new Array(),
        _currentLinkPathList = new Array(),
        _currentPageVisitorsList = new Array(),
        _currentTrendingList = new Array(),

        _oldPageRankList = new Array(),
        _oldPageTitleList = new Array(),
        _oldLinkPathList = new Array(),
        _oldPageVisitorsList = new Array(),
        _oldTrendingList = new Array();

        setInterval
        (
            function()
            {
                getChartbeatData();

            }, 5000
            );

        getChartbeatData();

        function getChartbeatData()

        {

            $.ajax(
            {
               url:            _url,
               dataType:        "jsonp",
               jsonp:           false,
               jsonpCallback:   "chartbeatCallback",
               async:           true,
               cache:           'true',
               contentType:     "application/json",
               dataType:        "jsonp",
               success: function(json) {
                 chartbeatCallback(json);
             },
             error: function(json) {
				 alert("jsonDataerror = " + jsonData);
                 console.dir(json);
             }
         });

        }

        function chartbeatCallback(jsonData)
        {
			//alert("jsonData = " + jsonData);
            _currentNumberOfElements = 0;
            _oldRank = 0;
            _currentRank = 0;

            cloneLists();
            clearLists();

            $.each(jsonData, function(ndx, item)
            {
				
                _currentPageTitle = item.i;
                _currentLinkPath = item.path;
                _currentPageVisitors = item.visitors;

                mppOptions.hostUrl = <?php echo $host ?> +  ".com";
                if (_currentLinkPath.indexOf(mppOptions.hostUrl) < 0) {
                    urlPrex = mppOptions.hostUrl;
                    if (_currentLinkPath.indexOf("/") != 0) {
                        urlPrex = urlPrex + "/";
                    }
                    _currentLinkPath = urlPrex + _currentLinkPath;
                } else if (_currentLinkPath.indexOf(mppOptions.hostUrl) == 0){
                    _currentLinkPath = _currentLinkPath;
                }
                
               
                if (_currentLinkPath.indexOf('-') == -1 || _currentPageTitle == "" ||  _currentLinkPath.indexOf("-staging") > -1 || _currentLinkPath.indexOf("cmstechops") > -1 ||
                    _currentNumberOfElements >= mppOptions.numberOfHeadlines || _currentPageTitle == "ERROR")
                {
                }
                else
                {
                    if ($.inArray(_currentPageTitle, _currentPageTitleList) < 0)
                    {
                        _currentNumberOfElements++;
                        _currentPageRankList.push(_currentNumberOfElements);
                        tempPageTitle = _currentPageTitle;
                        _currentPageTitleList.push(tempPageTitle);
                        _currentLinkPathList.push(_currentLinkPath);
                        _currentPageVisitorsList.push(_currentPageVisitors);
                        _oldRank = $.inArray(_currentPageTitle, _oldPageTitleList) + 1;
                        _currentRank = _currentNumberOfElements;

                        if (_oldRank == 0)
                        {
                            _currentTrendingList.push('^');
                            $('a.active')
                        }
                        }
                        else if (_currentRank == _oldRank)
                        {
                            _currentTrendingList.push('-');
                        }
                        else if (_currentRank > _oldRank)
                        {
                            _currentTrendingList.push('down');
                            $('a.active').animate({
                                    backgroundColor:"red",
                                    color:"white"
                                }, 1500, function(){
                                    console.log('story' + idx + 'moved up!')
                                })
                        }
                        else if (_currentRank < _oldRank)
                        {
                            _currentTrendingList.push('^');
                            $('a.active').animate({
                                    backgroundColor:"green",
                                    color:"white"
                                }, 1500, function(){
                                    console.log('story' + idx + 'moved up!')
                            })
                        }
                    }

               displayChartbeatWidget();

            });

}

function displayChartbeatWidget()
{

    $('#MostPopularStories').html('');
    $('#MostPopularStories')
    .css('font-size', '1em')
    .css('background-repeat', 'no-repeat')
    .css('background-position', 'right top')
    .css('width', '100%')
    .css('maxWidth', '300px')
    .css('color','black')
    .addClass('trending');


   $('<div/>')
    .attr('id', 'MostPopularStoriesWidgetShadow')
    .appendTo('#MostPopularStories');

    if (mppOptions.displayModuleTitle) {
        $('<div/>')
        .appendTo('#MostPopularStoriesWidgetShadow');

        $('<span/>')
        .css('float', 'left')
        .css('color', '#454545')
       // .css('width', '100%')
        .css('font-family', 'Helvetica, sans-serif')

        .html(mppOptions.header)
        .appendTo('#MostPopularStoriesWidgetTitle');

    }

    for (idx=0; idx < _currentPageTitleList.length; idx++)
    {
        //var wide = 100-(idx*100/5+10);
        //console.log(wide);

        if (_currentTrendingList[idx] == '-')
        {
            _animationBackgroundColor = '#154e50';
            _trendingClass = 'trending-right';
             console.log('story ' + idx + ' flat!')
        }
        else if (_currentTrendingList[idx] == '^')
        {
            _animationBackgroundColor = '#028b0b';
            _trendingClass = 'trending-up';
            console.log('story ' + idx + ' moved up!')
        }
        else if (_currentTrendingList[idx] == 'down')
        {
            _animationBackgroundColor = '#b10803';
            _trendingClass = 'trending-down';
            console.log('story ' + idx + ' moved down!')
        }


        $('<li/>')
        .attr('id', 'row' + idx)
        .css({
                width:'100%',
                display:'block',
                overflow:'hidden',
                clear:'both',
                marginLeft: '0px'
        })
        .appendTo('#MostPopularStoriesWidgetShadow');

        if (idx != 0)
        {

            $('#row' + idx)
            .css('border-top', '1px solid #eaeaea');

        }
        else
        {

            $('#row' + idx)
           .css('padding-top', '6px');

        }

        $('<div/>')
        .attr('id', 'cell' + idx)
        .css({
            width: "100%",
            display: 'table-cell',
            paddingBottom: '6px'
        })
        .addClass('trending' + idx)
        .appendTo('#row' + idx);

        $('<div/>')
            .css({
                width: '100%',
                float: 'left',
                fontFamily: 'Helvetica,Sans-Serif',
                fontSize: '1.1em',
                marginBottom: '5px',
                marginTop: '5px',
                display: 'inline-block',
                verticalAlign: 'middle',
                color: '#347ca8',
                fontWeight: 'bold',
                letterSpacing: '-1px',
            })
            .html('<a style="display:inline-block;vertical-align:middle;" class="active ' + _trendingClass + '" href="http://' + _currentLinkPathList[idx] + '" target="_top">' + _currentPageTitleList[idx] + '</a>')
            .appendTo('.trending'+idx)
            .addClass(_trendingClass);

        $('a.active')
            .css({
                color: 'black',
                width: '80%'
            })
    }
}

function cloneLists()
{

    _oldPageRankList = _currentPageRankList.slice(0);
    _oldPageTitleList = _currentPageTitleList.slice(0);
    _oldLinkPathList = _currentLinkPathList.slice(0);
    _oldPageVisitorsList = _currentPageVisitorsList.slice(0);
    _oldTrendingList = _currentTrendingList.slice(0);

}

function clearLists()
{

    _currentPageRankList = [];
    _currentPageTitleList = [];
    _currentLinkPathList = [];
    _currentPageVisitorsList = [];
    _currentTrendingList = [];

}

};

})(jQuery); 

</script>

<script language="javascript">
jQuery(document).ready (
    function() {
		
        jQuery("#MostPopularStories").MostPopularStories( {
        }
        );
    });
</script>






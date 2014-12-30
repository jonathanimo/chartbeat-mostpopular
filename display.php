
<html>
<head>
</head>
<body>
<style>
body {
   margin:0;
   padding: 0;
   border:0;
   outline:none;
   vertical-align: top;
   font-size: 100%;
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
a.active {margin-right: 8px;}
.trending li{padding-left:40px;position:relative}
.trending-right,.trending-down,.trending-up{background:url(http://media.khon2.com/sprite.png) 0 -359px no-repeat;height:20px;width:20px;display:block}
.trending-down{background-position:-40px -359px}
.trending-up{background-position:-20px -359px}
.trending .trending-right,.trending .trending-down,.trending .trending-up{position:absolute;top:0;left:10px}
</style>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

<div id="MostPopularStories"></div>

<script type="text/javascript">

(function($)
{

    $.fn.linMostPopularStories = function( mppOptions )
    {

        var mppDefaultSettings =
        {
            numberOfHeadlines   :   5
        };

        var mppOptions = $.extend(mppDefaultSettings, mppOptions);

        //Change _url to location of proxy with the host variable.

        var _url = 'store.html';

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

            }, 15000
        );

        getChartbeatData();

        function getChartbeatData()

        {

	           $.ajax(
	            {
	                url:            _url,
	                dataType:		"jsonp",
					jsonp: 			false,
					jsonpCallback: 	"chartbeatCallback",
					async: 			false,
					cache: 'true',
					contentType: 	"application/json",
					dataType: 		"jsonp",
			        success: function(json) {
			            chartbeatCallback(json);
			        },
			        error: function(json) {
			            // console.dir(json);
			        }
	            });
	
	    }

        function chartbeatCallback(jsonData)
        {
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

                // Make sure the path starts with the host (turnto23.com, and 10news.com are not returnig)
		mppOptions.hostUrl = 'fox10tv.com';
                if (_currentLinkPath.indexOf(mppOptions.hostUrl) < 0) {
                    urlPrex = mppOptions.hostUrl;
                    if (_currentLinkPath.indexOf("/") != 0) {
                        urlPrex = urlPrex + "/";
                    }
                    _currentLinkPath = urlPrex + _currentLinkPath;
                } else if (_currentLinkPath.indexOf(mppOptions.hostUrl) == 0){
                    _currentLinkPath = _currentLinkPath;
                }
                
                // Strip the call letters and section information out of the title, we just want headlines
                //_currentPageTitle = _currentPageTitle.replace(/\s-\s/g, ' ').replace(/-/g, ' ');
                
                
                // LIN don't need to do this, LIN's using story page_title as page title, and layout html title as page title.
                // For most sites, the page_title of story is the one(story headline) we want, but for KXAN, it's not.
                // In common, KXAN story editors prefer using "story title | KXAN.com" as story title, and if we want "|" works, the " | KXAN.com" will be displayed in the most popular stories, if the " | KXAN.com" don't want to be displayed in most popular stories module, we'll need to do customization for kxan only, and another work-around is that KXAN story editors will not use that format like "story title | KXAN.com" as story title any more.
                // We'll fix this "|" issue for most sites, and KXAN is acceptable for " | KXAN.com" displaying in most popular module. 

                var lastVerticalBarTitle = _currentPageTitle.lastIndexOf("|");
                /*if (lastVerticalBarTitle > -1) {
                    _currentPageTitle = $.trim(_currentPageTitle.substr(0, lastVerticalBarTitle));
                    _currentPageTitle = _currentPageTitle.replace(/\s-\s/g, ' ').replace(/-/g, ' ');

                } else {
                    var firstDashInTitle = _currentPageTitle.indexOf("-");
                    if (firstDashInTitle > -1) {
                        _currentPageTitle = _currentPageTitle.substr(firstDashInTitle + 1);
                        
                        var lastDashInTitle = _currentPageTitle.indexOf(" - ");
                        if (lastDashInTitle > -1)
                            _currentPageTitle = _currentPageTitle.substr(0, lastDashInTitle);
                    }
                }*/
                
                // TODO: if the subindex url is like http://www.kxan.com/high-school-football-livestream, it'll also display in most popular module, but actually, it's not a story.
                // var slashCount = _currentLinkPath.match(/\//g) ? _currentLinkPath.match(/\//g).length : 0;
                // Per Bryant: "we have to keep using *-* and risk including some non-story pages."
                if (_currentLinkPath.indexOf('-') == -1 || _currentPageTitle == "" ||  _currentLinkPath.indexOf("-staging") > -1 || _currentLinkPath.indexOf("cmstechops") > -1 ||
                    _currentNumberOfElements >= mppOptions.numberOfHeadlines || _currentPageTitle == "ERROR")
                {
                    // Ignore these elements.
                }
                else
                {
                    if ($.inArray(_currentPageTitle, _currentPageTitleList) < 0)
                    {
                        _currentNumberOfElements++;

                        _currentPageRankList.push(_currentNumberOfElements);
                        
                        tempPageTitle = _currentPageTitle;
                        // LIN don't need to do this if "#" is in story healine.
                        /*
                        hashPos = tempPageTitle.indexOf('#');
                        if (hashPos > 0)
                        {
                            tempPageTitle = tempPageTitle.substr(0, hashPos);
                        }
                        */
                        _currentPageTitleList.push(tempPageTitle);
                        _currentLinkPathList.push(_currentLinkPath);
                        _currentPageVisitorsList.push(_currentPageVisitors);

                        _oldRank = $.inArray(_currentPageTitle, _oldPageTitleList) + 1;
                        _currentRank = _currentNumberOfElements;

                        if (_oldRank == 0)
                        {
                            _currentTrendingList.push('^');
                        }
                        else if (_currentRank == _oldRank)
                        {
                            _currentTrendingList.push('-');
                        }
                        else if (_currentRank > _oldRank)
                        {
                            _currentTrendingList.push('down');
                        }
                        else if (_currentRank < _oldRank)
                        {
                            _currentTrendingList.push('^');
                        }
                    }
                }

                displayChartbeatWidget();

            });

        }

        function displayChartbeatWidget()
        {
        
            $('#MostPopularStories').html('');
            $('#MostPopularStories')
                .css('font-size', '100%')
                .css('background-repeat', 'no-repeat')
                .css('background-position', 'right top')
                .css('background-color', '#eaeaea')
                .addClass('trending');

            $('<div/>')
                .attr('id', 'MostPopularStoriesWidgetShadow')
                .css('border', '1px solid #eaeaea')
                .css('background-color', '#fff')
                .css('padding', '0px')
                .css('margin', '0px 0px 0px 0px')
                .appendTo('#MostPopularStories');

            if (mppOptions.displayModuleTitle) {
                $('<div/>')

                .appendTo('#MostPopularStoriesWidgetShadow');

                $('<span/>')
                    .css('float', 'left')
                    .css('background-color', '#fff')
						  .css('background-color', '#eaeaea')
						  .css('color', '#454545')
						  .css('height', '14px')
						  .css('width', '100%')
						  .css('padding-bottom', '5px')
						  .css('font-family', '"istok_webregular", Arial, sans-serif')
						                     
                    .html(mppOptions.header)
                    .appendTo('#MostPopularStoriesWidgetTitle');
            
            }

            for (idx=0; idx < _currentPageTitleList.length; idx++)
            {

                if (_currentTrendingList[idx] == '-')
                {
                    _animationBackgroundColor = '#154e50';
                    _trendingClass = 'trending-right';
                }
                else if (_currentTrendingList[idx] == '^')
                {
                    _animationBackgroundColor = '#028b0b';
                    _trendingClass = 'trending-up';
                }
                else if (_currentTrendingList[idx] == 'down')
                {
                    _animationBackgroundColor = '#b10803';
                    _trendingClass = 'trending-down';
                }


                $('<li/>')
                    .attr('id', 'row' + idx)
                    .css('display', 'block')
                    .css('overflow', 'hidden')
                    .css('clear', 'both')
                    .css('padding-top', '4px')
                    .css('margin-left', '0px')
                    .appendTo('#MostPopularStoriesWidgetShadow');

                if (idx != 0)
                {

                    $('#row' + idx)
                        .css('border-top', '1px solid #eaeaea');

                }
                else
                {

                    $('#row' + idx)
                        .css('padding-top', '8px');

                }

                $('<div/>')
                    .attr('id', 'cell' + idx)
                    .css('width', '100%')
                    .css('display', 'table-cell')
                    .css('padding-bottom', '10px')
                    .appendTo('#row' + idx);

                if (idx != 0) 
                {
                    $('<div/>')
                        .css('margin-top', '10px')
                        .addClass(_trendingClass)
                        .appendTo('#cell' + idx);
                } 
                else 
                {
                    $('<div/>')
                        .css('margin-top', '14px')
                        .addClass(_trendingClass)
                        .appendTo('#cell' + idx);
                }
                

                $('<div/>')
                    .css('width', '100%')
                    .css('float', 'left')
                    .css('font-family', 'Arial,Helvetica,Sans-Serif')
                    .css('font-size', '9pt')
                    .css('margin-bottom', '5px')
                    .css('margin-top', '8px')
                    .css('display', 'inline-block')
                    .css('vertical-align', 'middle')
                    .css('color', '#347ca8')
		    .html('<a style="display:inline-block;vertical-align:middle;" class="active" href="http://' + _currentLinkPathList[idx] + '" target="_top">' + _currentPageTitleList[idx] + '</a>')
                    .appendTo('#cell' + idx);

            }

            $('<div/>')
                .attr('id', 'BottomShadow')
                .css('width', '100%')
                .css('height', '0px')
                .css('background-color', '#fff')
                .appendTo('#MostPopularStories');

            $('<span/>')
                .css('width', '100%')
                .css('float', 'right')
                .css('height', '0px')
                .css('background-color', '#eaeaea')
                .html('')
                .appendTo('#BottomShadow')   

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
            jQuery("#MostPopularStories").linMostPopularStories( {
            }
        );
    });
</script>


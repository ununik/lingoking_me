<?php
//require 'vendor/autoload.php';

//$app = new \Slim\Slim();
//$app->setName('Maintenance');

//$app->response->headers->set('Content-Type', 'application/json');
header('Content-Type: application/json');

$keyWordsError = array('unable', 'degradation');
$keyWordsFixed = array('complete', 'success');
$content = file_get_contents ('https://trust.salesforce.com/rest/rss/EU2');
$items = array();

$pattern = '~<title>([^<]*)</title>~';
preg_match_all($pattern, $content, $title, PREG_OFFSET_CAPTURE, 3);

$pattern = '~<description>([^<]*)</description>~';
preg_match_all($pattern, $content, $description, PREG_OFFSET_CAPTURE, 3);

$pattern = '~<pubDate>([^<]*)</pubDate>~';
preg_match_all($pattern, $content, $pubDate, PREG_OFFSET_CAPTURE, 3);

$items['title'] = $title[1];
$items['description'] = $description[1];
$items['pubDate'] = $pubDate[1];


$errorFixed = true;
for($i = count($items['title'])-1; $i >= 0 ; $i--)
{
	if (eregi('EU2', $items['title'][$i][0]))
	{
		if(eregi('Maintenance Complete', $items['title'][$i][0]))
		{
			$errorFixed = true;
			break;
		}
		if($errorFixed == true)
		{
			foreach($keyWordsError as $keyWord)
			{
				if (eregi($keyWord, $items['description'][$i][0]))
				{
					$errorFixed = false;
					break;
				}
			}
		} else {
			foreach($keyWordsFixed as $keyWord2)
			{
				if (eregi($keyWord2, $items['description'][$i][0]))
				{
					$errorFixed = true;
					break;
				}
			}
		}
	}
}

$result = array('success' => $errorFixed);

echo json_encode($result, JSON_PRETTY_PRINT);

//$app->run();
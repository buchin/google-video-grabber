<?php namespace Buchin\GoogleVideoGrabber;

use Buchin\GoogleImageGrabber\GoogleImageGrabber;
use Smoqadam\Video;
use PHPHtmlParser\Dom;
use __;
/**
* 
*/
class GoogleVideoGrabber
{
	public static function getVideoIdFromUrl($url)
	{
		parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
		return $my_array_of_vars['v'] ?? null;
	}
	
	public static function grab($keyword, $options = ['maxResults' => 10, 'api_key' => 'AIzaSyDfLPH6Y09edcZYmLPvbANg7AwQIOtO-nY'])
	{
		$hack = ' site:youtube.com/watch';
		$items = GoogleImageGrabber::grab($keyword . $hack);
		$items = array_slice($items, 0, $options['maxResults']);

		$results = [];
		$ids = [];
		foreach ($items as $item) {
			$result = [];
			$id = self::getVideoIdFromUrl($item['source']);

			if(is_null($id)){
				continue;
			}

			$result['videoid']       = $id;
			$ids[] = $result['videoid'];

			$result['link'] = 'https://www.youtube.com/watch?v=' . $result['videoid'];

			$result['thumbnail']     = "https://i.ytimg.com/vi/" . $result['videoid'] . "/default.jpg";
			$result['thumbnail_mq']  = "https://i.ytimg.com/vi/" . $result['videoid'] . "/mqdefault.jpg";
			$result['thumbnail_hq']  = "https://i.ytimg.com/vi/" . $result['videoid'] . "/hqdefault.jpg";

			$result['description'] = $item['domain'];
			$result['title'] = ucwords($item['alt']);
			$video = new Video($id);
			$details = $video->getVideoInfo()['videoDetails'];
			unset($details['thumbnail']);

			$result = array_merge($result, $details);

			$result['pubdate'] = '';
			$result['uploader'] = $result['author'];
			$result['duration_in_seconds'] = $result['lengthSeconds'];
			$result['duration'] = gmdate("H:i:s", $result['duration_in_seconds']);

			$results[] = $result;
		}


		return $results;
	}
}
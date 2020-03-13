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
	
	public static function strip($title)
	{
		$title = strtolower($title);
		$title = str_replace(['youtube.com', 'youtube'], '', $title);
		$title = explode(' ', $title);
		$title = array_filter($title);
		$title = array_unique($title);
		$title = implode(' ', $title);

		return $title;
	}

	public static function grab($keyword, $options = ['maxResults' => 10, 'full_data' => true])
	{
		$default = ['maxResults' => 10, 'full_data' => true ];

		$options = array_merge($default, $options);

		$hack = ' site:youtube.com/watch';
		$items = @GoogleImageGrabber::grab($keyword . $hack);
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

			$description = ucfirst(self::strip($item['domain']));

			$result['description'] = $description;

			$title = self::strip($item['alt'] . ' ' . $item['title']);
			
			$result['title'] = ucwords(self::strip($title));

			$result['pubdate'] = '';
			$result['uploader'] = 'Unknown';
			$result['duration_in_seconds'] = 0;
			$result['duration'] = gmdate("H:i:s", $result['duration_in_seconds']);
			
			if($options['full_data']){
				$video = new Video($id);
				$details = $video->getVideoInfo()['videoDetails'];
				unset($details['thumbnail']);

				$result = array_merge($result, $details);

				$result['uploader'] = $result['author'];
				$result['duration_in_seconds'] = $result['lengthSeconds'];
				$result['duration'] = gmdate("H:i:s", $result['duration_in_seconds']);
			}
			

			$results[] = $result;
		}


		return $results;
	}
}
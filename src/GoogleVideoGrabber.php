<?php namespace Buchin\GoogleVideoGrabber;

use PHPHtmlParser\Dom;
use duncan3dc\Laravel\Dusk;
use __;
/**
* 
*/
class GoogleVideoGrabber
{
	
	public static function grab($keyword, $options = [])
	{
		$client = new \Google_Client();
		$client->setDeveloperKey($options['api_key']);

		$youtube = new \Google_Service_YouTube($client);

		$options = array_merge(['maxResults' => 10, 'api_key' => 'AIzaSyDfLPH6Y09edcZYmLPvbANg7AwQIOtO-nY'], $options);

		$response = $youtube->search->listSearch('snippet', array(
			'q' => $keyword,
			'type' => 'video',
			'maxResults' => $options['maxResults'],
		));

		$results = [];
		$ids = [];
		foreach ($response->items as $item) {
			$result = [];
			$result['videoid']       = $item->id->videoId;
			$ids[] = $result['videoid'];

			$result['link'] = 'https://www.youtube.com/watch?v=' . $result['videoid'];

			$result['thumbnail']     = "https://i.ytimg.com/vi/" . $result['videoid'] . "/default.jpg";
			$result['thumbnail_mq']  = "https://i.ytimg.com/vi/" . $result['videoid'] . "/mqdefault.jpg";
			$result['thumbnail_hq']  = "https://i.ytimg.com/vi/" . $result['videoid'] . "/hqdefault.jpg";

			$result['description'] = $item->snippet->description;
			$result['title'] = $item->snippet->title;

			$result['pubdate'] = $item->snippet->publishedAt;
			$result['uploader'] = $item->snippet->channelTitle;

			$results[] = $result;
		}


		$videos = $youtube->videos->listVideos('contentDetails', ['id' => implode(',', $ids)]);

		foreach ($videos as $video) {
			foreach ($results as $key => $result) {
				if($result['videoid'] == $video->id){
					$duration = $video->contentDetails->duration;
					$interval = new \DateInterval($duration);
					$results[$key]['duration_in_seconds'] = $interval->h * 3600 + $interval->i * 60 + $interval->s;

					$results[$key]['duration'] = $interval->format('%H:%I:%S');
				}
			}
		}

		return $results;
	}
}
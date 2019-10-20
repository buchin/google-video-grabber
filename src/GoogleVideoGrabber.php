<?php namespace Buchin\GoogleVideoGrabber;

use PHPHtmlParser\Dom;
use __;
/**
* 
*/
class GoogleVideoGrabber
{
	
	public static function grab($keyword, $options = [])
	{
		$url = "https://www.google.com/search?hl=en&num=100&tbm=vid&q=site:youtube.com+" . urlencode($keyword);

		$ua = \Campo\UserAgent::random([
		    'os_type' => ['Windows', 'OS X'],
		    'device_type' => 'Desktop'
		]);

		$options  = [
			'http' => [
				'method'     =>"GET",
				'user_agent' =>  $ua,
			],
			'ssl' => [
				"verify_peer"      => FALSE,
				"verify_peer_name" => FALSE,
			],
		];

		$context  = stream_context_create($options);

		$response = file_get_contents($url, FALSE, $context);

		$htmldom = new Dom;
		$htmldom->loadStr($response, []);


		$results = [];

		foreach ($htmldom->find('.rc') as $n => $element) {

			$result = [];
			$result['link'] = $element->find('div.r > a:nth-child(1)')->getAttribute('href');

			parse_str(parse_url($result['link'], PHP_URL_QUERY), $vquery);
			if (!empty($vquery['v'])) {

				$result['videoid']       = $vquery['v'];
	            $result['thumbnail']     = "https://i.ytimg.com/vi/" . $vquery['v'] . "/default.jpg";
	            $result['thumbnail_mq']  = "https://i.ytimg.com/vi/" . $vquery['v'] . "/mqdefault.jpg";
	            $result['thumbnail_hq']  = "https://i.ytimg.com/vi/" . $vquery['v'] . "/hqdefault.jpg";

				$result['description'] = strip_tags(html_entity_decode($element->find('div.s > div > span')->innerHtml));
				$result['title'] = html_entity_decode($element->find('div.r > a:nth-child(1) > h3')->innerHtml);

				$result['duration'] = str_replace('&#9654;&nbsp;', '', $element->find('span.vdur')->innerHtml);

				$duration_array = explode(':', $result['duration']);
				$result['duration_in_seconds'] = ($duration_array[0] * 60) + $duration_array[1];
				$result['meta'] = $element->find('.slp')->text;

				$meta_arr = explode(' - ', $result['meta']);
				$result['pubdate'] = $meta_arr[0];
				$result['uploader'] = str_replace('Uploaded by ', '', $meta_arr[1]);

				$results[] = $result;
			}
		}

		return $results;
	}
}
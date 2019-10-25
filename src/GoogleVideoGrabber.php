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

		// create curl resource
		$ch = curl_init();

		// set options
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if($options['proxy']){
			curl_setopt($ch, CURLOPT_PROXY, $options['proxy']);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // read more about HTTPS http://stackoverflow.com/questions/31162706/how-to-scrape-a-ssl-or-https-url/31164409#31164409
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);

		// $output contains the output string
		$response = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch); 


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

				$result['description'] = $element->find('div.s > div > span')->innerHtml;
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
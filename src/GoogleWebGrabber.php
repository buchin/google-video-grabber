<?php namespace Buchin\GoogleWebGrabber;

use PHPHtmlParser\Dom;
use __;
/**
* 
*/
class GoogleWebGrabber
{
	
	public static function grab($keyword, $options = [])
	{
		$url = "https://www.google.com/search?q=" . urlencode($keyword);// . "&source=lnms&tbm=isch&tbs=";

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
			$result['description'] = html_entity_decode($element->find('div.s > div > span')->innerHtml);
			$result['title'] = html_entity_decode($element->find('div.r > a:nth-child(1) > h3')->innerHtml);

			$results[] = $result;
		}

		return $results;
	}
}
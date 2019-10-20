<?php
use Buchin\GoogleVideoGrabber\GoogleVideoGrabber;

describe('GoogleVideoGrabber', function ()
{
	describe('::grab($keyword, $options)', function ()
	{
		it('get web data from google web', function()
		{
			$videos = GoogleVideoGrabber::grab('makan nasi');
			var_dump($videos); die;

			expect(count($videos))->toBeGreaterThan(0);
		});
	});
});
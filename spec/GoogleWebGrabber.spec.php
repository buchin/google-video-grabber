<?php
use Buchin\GoogleWebGrabber\GoogleWebGrabber;

describe('GoogleWebGrabber', function ()
{
	describe('::grab($keyword, $options)', function ()
	{
		it('get web data from google web', function()
		{
			$websites = GoogleWebGrabber::grab('makan nasi');

			expect(count($websites))->toBeGreaterThan(0);
		});
	});
});
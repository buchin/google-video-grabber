# Google Web Grabber

Scrape google web using PHP

## Getting Started

Get this up and running

### Prerequisites

composer

### Installing

```bash
composer require buchin/google-web-grabber
```

### Usage

```php
use Buchin\GoogleVideoGrabber\GoogleVideoGrabber;

$keyword = 'makan nasi';

$results = GoogleVideoGrabber::grab($keyword);

```

## Test

```bash
./vendor/bin/kahlan --reporter=verbose
```

## Authors

* **Mochammad Masbuchin** - *Initial work* - [buchin](https://github.com/buchin)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

Halal

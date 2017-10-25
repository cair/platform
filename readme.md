## Cair Platform

# Installation

```json
{
  "require": {
    "cair/platform": "0.1.*"
  }
}
```

`composer install`

# Usage

```php
$provider = new \Cair\Platform\Provider([
  'posts' => ['title', 'content']
]);

$provider->posts()->create([
  'title' => 'Welcome to Cair',
  'content' => 'This is the underlying layer of the cair CMS.'
]);

echo $provider->find(1)->title;
```

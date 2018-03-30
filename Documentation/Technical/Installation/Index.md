<img align="left" src="../../Resources/Public/Icons/lux.svg" width="100" />

### Installation

#### Requirements

* TYPO3 8.7 or newer
* TYPO3 should use the composer mode
* PHP 7.0 - 7.2

#### Installation via composer

Example composer.json file:

```
{
  "require": {
    "php": ">=7.0.0 <7.3.0",
    "typo3/cms": "^8.7",
    "in2code/lux": "^1.0",
  },
  "repositories": [
    {
      "type": "composer",
      "url": "https://composer.typo3.org"
    },
    {
      "type": "git",
      "url": "git@github.com:in2code-de/lux.git"
    }
  ]
}
```

Note: You need a github user that has access to the private lux repository for an installation.
Note: Lux itself will also load the php package [jlawrence/eos](https://packagist.org/packages/jlawrence/eos) for
some scoring calculation magic.

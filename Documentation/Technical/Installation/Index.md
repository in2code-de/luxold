<img align="left" src="../../../Resources/Public/Icons/lux.svg" width="50" />

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

After you have added the repository and the package name, you can do a `composer update in2code/lux` for example to
install the package. Don't forget to activate (e.g. in the extension manager) the extension once it is installed.

**Note:** You need a github user that has access to the private lux repository for an installation.

**Note:** Lux itself will also load the php package [jlawrence/eos](https://packagist.org/packages/jlawrence/eos) for
some scoring calculation magic.

#### Extension Manager settings

<img src="../../../Documentation/Images/documentation_installation_extensionmanager.png" width="800" />

If you click on the settings symbol for extension lux, you can change some basic settings in lux extension.

<img src="../../../Documentation/Images/documentation_installation_extensionmanager1.png" width="800" />
<img src="../../../Documentation/Images/documentation_installation_extensionmanager2.png" width="800" />
<img src="../../../Documentation/Images/documentation_installation_extensionmanager3.png" width="800" />

| Setting                                  | Description                                                                                             |
| ---------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| Basic: Scoring Calculation               | Define a calculation model for the basic lead scoring.<br>Available variables are - numberOfSiteVisits, numberOfPageVisits, downloads, lastVisitDaysAgo.<br>Note - you should run a commandController (e.g. every night) and calculate the scoring again, if you are using the variable "lastVisitDaysAgo".|
| Basic: Add on pagevisit                  | Categoryscoring: Add this value to the categoryscoring if a lead visits a page of a lux-category        |
| Basic: Add on download                   | Categoryscoring: Add this value to the categoryscoring if a lead downloads an asset of a lux-category   |
| Module: Disable analysis module          | Toggle the backend module Analysis in general                                                           |
| Module: Disable lead module              | Toggle the backend module Leads in general                                                              |
| Module: Disable workflow module          | Toggle the backend module Workflows in general                                                          |
| Advanced: Disable box with latest leads  | Toggle the box with latest lead visits in page module in general                                        |
| Advanced: Disable ip logging             | Disable the logging of the visitors IP address                                                          |
| Advanced: Anonymize IP                   | As an alternative to disableIpLogging, you can anonymize the visitors IP-address when saving. The last part of the IP will be anonymized with "***" |
| Advanced: Disable ip-api.com information | Toggle information enrichment based on the visitors IP by ip-api.com                                    |

#### Add TypoScript

If you have already activated lux in your TYPO3 instance, you can add the static TypoScript file *Main TypoScript (lux)*
in your root template. Most of the TypoScript configuration is used for frontend and for backend configuration.

If you want to see what kind of TypoScript will be included and how to overwrite some parts, look at
[the Lux folder](../../../Configuration/TypoScript/Lux)

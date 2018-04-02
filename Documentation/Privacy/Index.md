<img align="left" src="../../Resources/Public/Icons/lux.svg" width="50" />

This part of the documentation gives you some information about privacy on websites in general.

## Privacy documenation

First of all let your visitors know what kind of information you are collecting and why you are collection those
information. The best place for this explanation is the privacy site (Datenschutzerklärung). This will follow the
rules of GDPR (General Data Protection Regulation) / DSGVO (Datenschutzgrundverordnung).

### User information

Every visitor has the right to see every data that you've stored about him/her. In addition the right that all
information must be removed.

Beside a *CommandController* to remove leads and all there data, there is a *Remove completely* button in the detail
view of a lead. Both will result in a complete remove of all data of the lead.

### Tracking Opt-Out

#### Opt-Out Plugin
As known from Matomo (former known as Piwik) also Lux offers a Plugin fo an Opt-Out possibility for visitors.

<img src="../Images/documentation_plugin_optout_frontend1.png" width="800" />

#### DoNotTrack Header

Browsers support a (per default turned off) option to inform the website that the visitor don't wants to be tracked.
This is the *DoNotTrack* or *DNT* setting. Even if this rare used feature of the browser is only a recommendation, Lux
will respect this setting of course!

<img src="../Images/documentation_marketing_donottrack.png" width="800" />

**Note:** While Firefox turns on the DNT by default for anonymous tabs, Chrome and Internet Explorer never turn this
setting on by default.

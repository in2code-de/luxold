<img align="left" src="../../Resources/Public/Icons/lux.svg" width="50" />

This part of the documentation gives you some information about privacy on websites in general.

## Privacy documenation

Lux respects the privacy protection of website visitors. Please follow our example.

First of all let your visitors know what kind of information you are collecting and why you are collection those
information. The best place for this explanation is the privacy site (Datenschutzerklärung). This will follow the
rules of GDPR (General Data Protection Regulation) / DSGVO (Datenschutzgrundverordnung).

### Example part for your privacy page

This could be an example for your German "Datenschutzerklärungsseite":

```
<h2>Verwendung des Marketing-Automation-Tools lux</h2>

Diese Website benutzt das Marketing-Automation-Tool lux. Lux verwendet so genannte "Cookies".
Das sind Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website durch
Sie ermöglichen. Dazu werden die durch den Cookie erzeugten Informationen über die Benutzung dieser Website auf unserem
Server gespeichert. Zusätzlich speichern wir Ihre Eingaben und Vorlieben um Ihnen entsprechende Inhalte anzubieten
und die Bedienbarkeit der Website zu vereinfachen.

Diese Informationen werden nicht an Dritte weitergegeben. Sie können die Speicherung der Cookies durch eine
entsprechende Einstellung Ihrer Browser-Software verhindern - z.B. durch Aktivierung des "DoNotTrack" Headers; wir
weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht sämtliche Funktionen dieser Website
vollumfänglich werden nutzen können.

Wenn Sie mit der Speicherung und Nutzung Ihrer Daten nicht einverstanden sind, können Sie die Speicherung und Nutzung
hier deaktivieren. In diesem Fall wird in Ihrem Browser ein Opt-Out-Cookie hinterlegt der verhindert, dass Lux
Nutzungsdaten speichert. Wenn Sie alle Ihre Cookies löschen, hat dies zur Folge, dass auch das Lux Opt-Out-Cookie
gelöscht wird. Das Opt-Out muss bei einem erneuten Besuch unserer Seite wieder aktiviert werden.
```

We would recommend to place an Opt-Out Plugin also nerby this explanation
(see [Plugins](../Technical/Plugins/Index.md)).

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

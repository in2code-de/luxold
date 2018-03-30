<img align="left" src="Resources/Public/Icons/lux.svg" width="100" />

# Living User eXperience - LUX - the Marketing Automation tool for TYPO3

## Introduction

LUX is an enterprise software solution to fill the gap between your TYPO3-website and a standalone marketing automation
tool. LUX will track, identify, analyse your leads and give the visitors some improved user experience for your website
like showing relevant information at the right time.

## Screenshots

Example dashboard overview:

<img src="Documentation/Screenshots/dashboard.png" width="800" />

Example leadlist:

<img src="Documentation/Screenshots/list.png" width="800" />

Show some details:

<img src="Documentation/Screenshots/list2.png" width="800" />

Show relevant information in page view:

<img src="Documentation/Screenshots/pageoverview.png" width="800" />

Workflow - Define your own trigger(s) and combine them via AND or OR:

<img src="Documentation/Screenshots/workflow_trigger.png" width="800" />

Workflow - Do one or more Actions if a trigger is activated:

<img src="Documentation/Screenshots/workflow_action.png" width="800" />

Ask for the visitors email-address when he/she wants to download an asset:

<img src="Documentation/Screenshots/email4link.png" width="800" />

... with a CK editor plugin:

<img src="Documentation/Screenshots/email4link_ckeditor_plugin.png" width="800" />

## Documentation

Find a full documentation (technical, marketing and privacy) under [Documentation/Index.md]

## Features

### Tracking

- Page views
- Number of website visits
- Pagefunnel
- First and last visit
- Store attributes from any form on the website
- Enricht information via IP: Country, Region, Company
- Track any asset download

### Identification

- Identify a lead with any webform email field
- Offer via CkEditor plugin a email4link popup (give me your email and we are going to send you the asset via email)
- Automaticly merge cookie-ids on second identification (if cookie was removed)

### Analyses

- Last leads per page
- Dashboard (most important information)
- Listview
- Detailview with pagefunnel and activity-log
- Show pageviews
- Show asset downloads

### Scoring

- General scoring (with individual calculation)
- Category Scoring
- Contextual content (based on category scoring)

### Workflow & User Experience

- Workflow backend module with a GUI and easy extension possibility
- Triggers:
-- On page visit (define on which number of pagevisit)
-- On a minimum scoring
-- If in a time frame
-- If visitor enters a page from a category
-- If visitor gets identified
- Actions:
-- Lightbox with a content element
-- Send an email with lead details
-- Redirect to any URL
-- Send publication to a slack channel

### CommandControllers & Scheduler

- Cleanup commands (to really erase data)
- Service commands (calculate scoring for all leads)

### Privacy Features

- There is a plugin which allows the visitor to opt from tracking
- The doNotTrack header of the browser will be respected
- Toogle IP anonymize function
- Toggle IP information enrichment over ipapi.com
- Toggle Tracking of Pagevisits
- Toggle Tracking of Downloads
- Toggle Field identification of any form
- Toogle Email4link functionality
- CommandController to anonymize records (for developing or for a presentation)

### Possible Enterprise Features

- Todo: Blacklisting
- Todo: Newsletter tool (replace or extend direct_mail? New tool - usable without Lux?)
- Todo: Contacts (Import?)
- Todo: API (Im- and Export)
- Todo: A/B Tests
- Todo: SocialMedia Connection (Twitter)

## Technical requirements

lux needs minimum *TYPO3 8.7* as a modern basic together with *composer mode*. Every kind of form extension is supported
for the identification feature (powermail, form, formhandler, felogin, etc...).

## Changelog

| Version    | Date       | State      | Description                                                                     |
| ---------- | ---------- | ---------- | ------------------------------------------------------------------------------- |
| 1.14.0     | 2018-03-26 | Bugfix     | Small bugfixes (CKeditor Plugin, Dateformat)                                    |
| 1.13.2     | 2018-03-18 | Bugfix     | Small bugfixes.                                                                 |
| 1.13.1     | 2018-03-15 | Bugfix     | Small bugfixes.                                                                 |
| 1.13.0     | 2018-03-14 | Task       | Add css grid for dashboard. Small bugfixes.                                     |
| 1.12.0     | 2018-03-13 | Feature    | Disable tracking if be-user is logged in. Small bugfixes.                       |
| 1.11.0     | 2018-03-12 | Feature    | Some privace features. Some brush up. Add contextual content plugin.            |
| 1.10.0     | 2018-03-10 | Task       | Some small improvements. Add a opt-out plugin.                                  |
| 1.9.0      | 2018-03-08 | Task       | Some changes to see categoryscorings.                                           |
| 1.8.0      | 2018-03-07 | Feature    | Optical refactoring of pageoverview. Bugfix in category scoring.                |
| 1.7.0      | 2018-03-07 | Feature    | Add identified trigger and slack action.                                        |
| 1.6.0      | 2018-03-06 | Task       | Add categoryscoring. Bugfix: Don't track downloads with email4link twice.       |
| 1.5.1      | 2018-03-05 | Bugfix     | Prevent exception in backend.                                                   |
| 1.5.0      | 2018-03-05 | Task       | Finish workflow modules with initial triggers/actions. Small bugfixes.          |
| 1.4.0      | 2018-03-04 | Task       | Split backend modules, add content analysis, integrate nearly complete workflow |
| 1.3.0      | 2018-03-02 | Task       | Don't show full download path in frontend with email4download                   |
| 1.2.0      | 2018-03-01 | Task       | Some small fixes in backend analysis show identified and recurring.             |
| 1.1.1      | 2018-02-27 | Bugfix     | Some small fixes in backend analysis and email4link functionality.              |
| 1.1.0      | 2018-02-26 | Task       | Show more relevant information in detail view. Small fixes.                     |
| 1.0.1      | 2018-02-26 | Bugfix     | Fix some smaller bugs that occurs with live data                                |
| 1.0.0      | 2018-02-26 | Task       | Initial Release with a stable tracking, identification and analyses             |

# Project Version (EXT:project_version)  

## What is project version?
Project version is a TYPO3  extension that adds an entry to the TYPO3 system information in the toolbar. This entry is based either on the common 'VERSION' file or on the local GIT revision.

## How do I install it? 
First make sure you match the requirements:
| Requirement | Version |
|--|--|
| TYPO3 | >= 8.7.0 <=8.7.15 |
| php | >= 7.0 |

### Composer (recommended)
Simply require the extension from packagist: 
`composer require kamiyang/ext-projectversion`

Or if you prefer typo3-ter:
`composer require typo3-ter/projectversion`

### GIT
Go into your `typo3conf/ext/` folder and clone the project directly from github:
`git clone git@github.com:KamiYang/project_version.git`

You now only have to manually activate the extension in your Extension Manager.

## How do I use it?
### "VERSION"-file
Now, this is the easiest part. Create a file called `VERSION` (case sensitive) in your TYPO3 frontend docroot with the project version. This can be done like this: 
`/var/www/html$ echo 1.0.0-rc.3 > VERSION`

## Roadmap to v1.0.0
 
 - [x] Static VERSION file support
 - [ ] Add ability to configure "VERSION"-file path
 - [ ] GIT revision support
 - [ ] GIT tag/branch based on revision support 
 - [x] Upload extension to packagist.org
 - [x] Upload extension to TER

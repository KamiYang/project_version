# Project Version (EXT:project_version)  

## What is project version?
Project version is a TYPO3  extension that adds an entry to the TYPO3 system information in the toolbar. This entry is based either on the common 'VERSION' file or on the local GIT revision.

## How do I install it? 
First make sure you match the requirements:

| Requirement | Version |
| --- | --- |
| TYPO3 | \>= 8.7.0 \<=8.7.15 |
| php | \>= 7.0 |

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

### Custom Path
You can use your own filename and path. Note that this path still has to be accessible from your web docroot.
The configuration can be done in the ExtensionConfiguration. It is also possible to change the directory of the 'VERSION'-file. Only provide a directory path (with trailing slash '/') and it will automatically search for an file called 'VERSION'.

Examples:
We assume that the web document root (`PATH_site`) is `/var/www/html`.

| Configured Path | Absolute filename | 
| --- | --- |
| (empty - default) | /var/www/html/VERSION |
| MyVersion | /var/www/html/MyVersion |
| typo3conf/ | /var/www/html/typo3conf/VERSION |
| ./My/Custom/Version/File/In/Some/Nested/File/Structure | /var/www/html/./My/Custom/Version/File/In/Some/Nested/File/Structure|

## Roadmap to v1.0.0
 
 - [x] Static VERSION file support
 - [x] Add ability to configure "VERSION"-file path
 - [ ] GIT revision support
 - [ ] GIT tag/branch based on revision support
 - [ ] Add documentation of this extensions features
 - [ ] Extension Icon
 - [x] Upload extension to packagist.org
 - [x] Upload extension to TER

### Milestone for v0.3.0
The next milestone is the integration of basic GIT revisions. 

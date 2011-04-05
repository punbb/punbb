# PunBB

PunBB is a fast and lightweight PHP-powered discussion board. It is released under the GNU General Public License. Its primary goals are to be faster, smaller and less graphically intensive as compared to other discussion boards. PunBB has fewer features than many other discussion boards, but is generally faster and outputs smaller, semantically correct XHTML-compliant pages. 

## Quick install
 1. [Download the latest revision of PunBB](http://punbb.informer.com/downloads.php). Decompress the PunBB archive to a directory.
 2. Copy (or upload) all the files contained in this archive into the directory where you want to run your forums. (e.g. /home/user/www/punbb/)
 3. Run install.php from the forum admin directory (e.g. open http://example.com/punbb/admin/install.php in your browser). Follow the instructions.

## Requirements
 - A webserver
 - PHP 4.3.0 or later (PHP 5 included)
 - A database where forum data is to be stored, created in one of: MySQL 4.1.2 or later, PostgreSQL 7.0 or later or SQLite 2

## Extension installation
 1. Download an extension's archive from the PunBB extensions repository or any other place. Extract it into your forum’s extensions directory. (e.g. /home/user/example.com/punbb/extensions)
 2. Log into the forum and go to "Administration" console, "Extensions" section, choose "Install extensions" tab (e.g. http://example.com/punbb/admin/extensions.php?section=install). The downloaded extension will be listed there.
 3. Click the "Install extension" link to install the extension.

NOTE: You may use the pun_repository official PunBB extension to download and install extensions from PunBB repository with one click.

## Contributing

Please report issues on the [Github issue tracker](https://github.com/punbb/punbb/issues).
Personal email addresses are not appropriate for bug reports.

## Links
 - [Documentation](http://punbb.informer.com/wiki/)
 - [Internationalization](http://punbb.informer.com/wiki/punbb13/language_packs)
 - [Styles](http://punbb.informer.com/wiki/punbb13/syles)
 - [Extensions repository](http://punbb.informer.com/extensions/)
 - [Community Forums](http://punbb.informer.com/forums/)
 - [Development](https://github.com/punbb/punbb/)
 - [Reporting PunBB core SECURITY bugs (only!)](http://punbb.informer.com/bugreport.php)

## Copyright and disclaimer
This package and its contents are (C) 2002-2011 PunBB, all rights reserved.
Partially based on code (C) 2008-2009 FluxBB.org.

PunBB is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

PunBB is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA.

Good luck.

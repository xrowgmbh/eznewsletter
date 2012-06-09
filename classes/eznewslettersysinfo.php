<?php
//
// Created on: <02-Nov-2006 14:38:45 tos>
//
// SOFTWARE NAME: eZ Newsletter
// SOFTWARE RELEASE: 1.0.1
// BUILD VERSION: 
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
// 
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
// 
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//

/*!
  \brief contains the eZ Newsletter version.
*/

class eZNewsletterSysInfo
{
    const VERSION_MAJOR           = 1;
    const VERSION_MINOR           = 6;
    const VERSION_RELEASE         = 0;
    const VERSION_STATE           = '';
    const VERSION_DEVELOPMENT     = true;
    const VERSION_REVISION_STRING = '';
    const VERSION_ALIAS           = '1.6';
    const VERSION_REVISION        = ''; // = preg_replace( "#\\\$Rev:\s+([0-9]+)\s+\\\$#", '$1', self::VERSION_REVISION_STRING );

    /*!
      \return the eZNewsletter version as a string
      \param withRelease If true the release version is appended
      \param withAlias If true the alias is used instead
    */
    static function version( $withRelease = true, $asAlias = false, $withState = true )
    {
        if ( $asAlias )
        {
            $versionText = self::alias();
            if ( $withState )
                $versionText .= "-" . self::state();
        }
        else
        {
            $versionText = self::majorVersion() . '.' . self::minorVersion();
            if ( $withRelease )
                $versionText .= "." . self::release();
            if ( $withState )
                $versionText .= self::state();
        }
        return $versionText;
    }

    /*!
     \return the major version
    */
    static function majorVersion()
    {
        return self::VERSION_MAJOR;
    }

    /*!
     \return the minor version
    */
    static function minorVersion()
    {
        return self::VERSION_MINOR;
    }

    /*!
     \return the state of the release
    */
    static function state()
    {
        return self::VERSION_STATE;
    }

    /*!
     \return the development version or \c false if this is not a development version
    */
    static function developmentVersion()
    {
        return self::VERSION_DEVELOPMENT;
    }

    /*!
     \return the release number
    */
    static function release()
    {
        return self::VERSION_RELEASE;
    }

    /*!
     \return the SVN revision number
    */
    static function revision()
    {
        return self::VERSION_REVISION;
    }

    /*!
      \return the version of the database.
      \param withRelease If true the release version is appended
    */
    static function databaseVersion( $withRelease = true )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT value as version FROM ezsite_data WHERE name='eznewsletter-version'" );
        $version = false;
        if ( count( $rows ) > 0 )
        {
            $version = $rows[0]['version'];
            if ( $withRelease )
            {
                $release = self::databaseRelease();
                $version .= '-' . $release;
            }
        }
        return $version;
    }

    /*!
      \return the release of the database.
    */
    static function databaseRelease()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT value as release FROM ezsite_data WHERE name='eznewsletter-release'" );
        $release = false;
        if ( count( $rows ) > 0 )
        {
            $release = $rows[0]['release'];
        }
        return $release;
    }
}

?>

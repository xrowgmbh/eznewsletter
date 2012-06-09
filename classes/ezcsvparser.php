<?php
//
// Definition of eZCSVParser class
//
// Created on <6-jan-2006 rla>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
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
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file eZCSVParser.php
*/

/*!
  \class eZCSVParser
  \brief The class ezCSVParser converts a cvs encoded file to a php array

*/

class eZCSVParser
{
    /*!
     Constructor
    */
    function __construct( $filename, $firstRowLabels = true, $delimiter = ',' )
    {
        $fp = fopen( $filename, "r" );
        $rows = array();
        $c = 0;
        $row = array();

        // Load the file
        while ( ( $row = fgetcsv( $fp, 1000, $delimiter )) !== FALSE )
        {
            $rows[$c] = $row;
            $c++;
        }
        fclose ( $fp );

        // Reorganize data from row by row to colum by colum
        $colcount = count ( $rows[0] );
        $rowcount = count ( $rows );
        // - Main data array
        $data = array();

        for ( $i=0; $i<$colcount; $i++ )
        {
            if ( $firstRowLabels )
            {
                $colname = $rows[0][$i];
            }
            else
            {
                $colname = $i;
            }
            $col = array();

            // Fetch data from rows
            for ( $irow = ( $firstRowLabels ? 1 : 0 ); $irow< $rowcount; $irow++ )
            {
                // get rowdata
                $rowdata=$rows[$irow];
		$j=$i+1;
		if (count($rowdata) >= $j)
		{
		    $rowdata=$rows[$irow];
		    $datavalue=$rowdata[$i];
                    array_push( $col , $datavalue );
		}
            	else
		{
		}
	    }

            // Insert colum into main array
            $colname = trim($colname);
	    $data[$colname] = $col;

        }
        $this->Data = $data;
    }

    /*!
     return data
    */
    function data()
    {
        return $this->Data;
    }


    /// Member variables

    var $Data;
}

?>

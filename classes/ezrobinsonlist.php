<?php
//
// Definition of eZRobinsonListEntry class
//
// Created on: <13-Jan-2006 02:49:01 aw>
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

/*! \file ezrobinsonlistentry.php
*/

/*!
  \class eZRobinsonListEntry ezrobinsonlistentry.php
  \brief The class eZRobinsonListEntry does

*/

class eZRobinsonListEntry extends eZPersistentObject
{
    const EMAIL = 0;
    const MOBILE = 1;
    
    const IMPORT_LOCAL = 0;  
    const IMPORT_GLOBAL = 1;
    
    const SYNC = 0;
    const ADD = 1;
    
    /*!
     Constructor
    */
    function __construct( $row )
    {
        $this->eZPersistentObject( $row );
    }

    static function definition()
    {       
        return array( 'fields' => array(
                      'id' => array( 'name' => 'ID',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                      'value' => array( 'name' => 'Value',
                                                           'datatype' => 'string',
                                                           'default' => '',
                                                           'required' => true ),
                      'type' => array( 'name' => 'Type',
                                                           'datatype' => 'integer',
                                                           'default' => 0,
                                                           'required' => true ),
                      'global' => array( 'name' => 'Global',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array(
                            'type_map' => 'typeNameMap',
                            'global_map' => 'globalNameMap',
                            'import_map' => 'importNameMap' ),
                      'increment_key' => 'id',
                      'sort' => array( 'value' => 'asc' ),
                      'class_name' => 'eZRobinsonListEntry',
                      'name' => 'ezrobinsonlist' );
    }

    static function addCondition ($type, $condition)
    {
        $condArray = array();
        
        if ( $type == self::MOBILE )
        {
            $condArray = array( 'type' => self::MOBILE );
        }
        else
        {
            $condArray = array( 'type' => self::EMAIL );  
        }   
        
        return array_merge($condition, $condArray);
    }

   /*!
      \a $offset Offset from start of dataset.
      \a $limit Number of elements to return in each batch.
      \a $asObject Specifies whether to return datasat as objects or rows.
      \return Array of eZNewsletterType.
     */
    static function fetchByOffset( $type, $condition, $offset, $limit, $asObject = true )
    {    
        $robinsonlistEntrys = eZPersistentObject::fetchObjectList( eZRobinsonListEntry::definition(),
                                                                   null,
                                                                   eZRobinsonListEntry::addCondition( $type, $condition ),
                                                                   array( 'value' => 'ASC' ),
                                                                   array( 'offset' => $offset, 'length' => $limit ),
                                                                   $asObject );
        return $robinsonlistEntrys;
    }

    static function fetchValues( $type, $global )
    {    
        $robinsonlistEntrys = eZPersistentObject::fetchObjectList( eZRobinsonListEntry::definition(),
                                                                   array( 'value' ),
                                                                   array( 'type' => $type, 
                                                                          'global' => $global ),
                                                                   array( 'value' => 'ASC' ),
                                                                   null,
                                                                   $asObject );
        $result = array();
        
        foreach ($robinsonlistEntrys as $entry)
        {
            $result = array_merge( $result, $entry[ 'value' ] );
        }
        
        return $result;
    }


    /*!
      Fetches ans eZRobinsonListEntry object by its newslettersenditem_id
     */
    static function inList( $value, $list_type = null, $list_global = null )
    {   
        if ( $value == "" )
        {
            return false;
        }
    
        $cond = array();
    
        if ( $list_global != null )
        {
            $cond = array( 'global' => $list_global );
        }
    
        if ( $list_type != null )
        {
            $cond = array_merge( $cond, array( 'type' => $list_type ) );
        }
        $robinsonlist = eZPersistentObject::fetchObject( eZRobinsonListEntry::definition(),
                                                         null,
                                                         array_merge( array( 'value' => $value ), $cond ) );
        if ($robinsonlist)
        {
            return true;
        }
        else
        {
                return false;
        }
    }

    /*!
      Fetches an eZRobinsonListEntry object
     */
    static function fetchByValue( $value, $asObject = true )
    {
        $robinsonlistEntry = eZPersistentObject::fetchObject( eZRobinsonListEntry::definition(),
                                                   null,
                                                   array( 'value' => $value ),
                           $asObject );
        return $robinsonlistEntry;
    }

    /*!
      Fetches an eZRobinsonListEntry object
     */
    static function fetchById( $id, $asObject = true )
    {
        $robinsonlistEntry = eZPersistentObject::fetchObject( eZRobinsonListEntry::definition(),
                                                   null,
                                                   array( 'id' => $id ),
                                                   $asObject );
        return $robinsonlistEntry;
    }

    /*!
     \static

     Get robinsonlistentry list count

     \return eZRobinsonListEntry count
    */
    static function countAll($type, $cond = array() )
    {
        $rows = eZPersistentObject::fetchObject( eZRobinsonListEntry::definition($type),
                                                 array(),
                                                 array_merge( $cond, eZRobinsonListEntry::addCondition( $type, array() ) ),
                                                 false,
                                                 false,
                                                 array( array( 'operation' => 'count( * )',
                                                               'name' => 'count' ) ) );
        return $rows['count'];
    }

   /*!
     Create new object
    */
    static function create( $value = '', $type = self::EMAIL, $global = self::IMPORT_LOCAL )
    {
        if ( !eZRobinsonListEntry::inList($value) )
        {
            $rows = array( 'value'  => $value,
                           'type'   => $type,
                           'global' => $global );
    
            $entry = new eZRobinsonListEntry( $rows );
            $entry->store();

            return $entry;
        }
        else
        {
            return null;
        }
    }

    /*
     \param ID
    */
    static function removeById( $id )
    {
        eZPersistentObject::removeObject( eZRobinsonListEntry::definition(),
                                          array( 'id' => $id ) );
    }

    /*
     \param ID
    */
    static function removeByValue( $value, $type, $global )
    {
        eZPersistentObject::removeObject( eZRobinsonListEntry::definition(),
                                           array( 'value' => $value,
                          'type' => $type,
                          'global' => $global ) );
    }

    /*!
     \static
     Get Status name map
    */
    static function typeNameMap()
    {
        return array( self::EMAIL => ezpI18n::tr( 'eznewsletter/robinsonlist_entrytype', 'Email address' ),
                      self::MOBILE => ezpI18n::tr( 'eznewsletter/robinsonlist_entrytype', 'Mobile phone number' ) );
    }

    /*!
     \static
     Get Status name map
    */
    static function globalNameMap()
    {
        return array( self::IMPORT_LOCAL => ezpI18n::tr( 'eznewsletter/robinsonlist_entrysource', 'Local' ),
                      self::IMPORT_GLOBAL => ezpI18n::tr( 'eznewsletter/robinsonlist_entrysource', 'External data' ) );
    }

    /*!
     \static
     Get Status name map
    */
    static function importNameMap()
    {
        return array( self::SYNC => ezpI18n::tr( 'eznewsletter/robinsonlist_action', 'Synchronize' ),
                      self::ADD => ezpI18n::tr( 'eznewsletter/robinsonlist_action', 'Only add new' ) );
    }

    /*!
     \static
     Import Data
    */
    static function importData($data, $labels, $rows, $global, $type, $options)
    {
    
    if ( $type == self::EMAIL )
    {
        $mapping=$labels['email'];
    }
    else if ( $type == self::MOBILE )
    {
        $mapping=$labels['mobile']; 
    }

    //add new entries
    if ( $options == self::ADD )
    {
        foreach( $rows as $rowIndex)
        //foreach( $data[$mapping] as $value )
        {
        //add new from new data
        $value = $data[$mapping][$rowIndex];
        //echo "Adding: ".$value." ".$type." ".$global."<br>";
        eZRobinsonListEntry::create($value, $type, $global);
        }
    } 
    else if ( $options == self::SYNC )
    {
        $new_values = array();
        foreach( $rows as $rowIndex)
        {   
        $new_values = array_merge($new_values, $data[$mapping][$rowIndex]);
        }
    
        foreach( $new_values as $value)
        {
        //add new from new data
        //echo "Sync Adding: ".$value." ".$type." ".$global."<br>";
        eZRobinsonListEntry::create($value, $type, $global);
        }
        
        //get all current data
        $old_values = eZRobinsonListEntry::fetchValues($type, $global);

        foreach( $old_values as $value )
        {
        if ( !in_array( $value, $new_values) )
        {
            //remove from database
            //echo "Sync Removing: ".$value." ".$type." ".$global."<br>";
            eZRobinsonListEntry::removeByValue($value, $type, $global);
        }
        }
    }
    //echo "<br>";
    }
}

?>

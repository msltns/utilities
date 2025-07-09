<?php

namespace msltns\utilities;

/**
 * iCal_Scanner is an easy to use class, loads an "ics" 
 * format string and returns an array with the traditional 
 * iCal fields.
 *
 * @category 	Class
 * @package  	Utilities
 * @author 		msltns <info@msltns.com>
 * @version  	0.0.1
 * @since   	0.0.1
 * @license 	GPL 3
 *          	This program is free software; you can redistribute it and/or modify
 *          	it under the terms of the GNU General Public License, version 3, as
 *          	published by the Free Software Foundation.
 *          	This program is distributed in the hope that it will be useful,
 *          	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          	GNU General Public License for more details.
 *          	You should have received a copy of the GNU General Public License
 *          	along with this program; if not, write to the Free Software
 *          	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if ( ! class_exists( '\msltns\utilities\iCal_Scanner' ) ) {
    
    class iCal_Scanner {
	
    	private $ical = false;
    	private $_lastitem = false;

    	public function scan($data) {
    		$this->ical = false;
    		$regex_opt = 'mib';

    		// Lines in the string
    		$lines = mb_split( '[\r\n]+', $data );

    		// Delete empty ones
    		$last = count( $lines );
    		for($i = 0; $i < $last; $i ++) {
    			if (trim( $lines[$i] ) == '')
    				unset( $lines[$i] );
    		}
    		$lines = array_values( $lines );

    		// First and last items
    		$first = 0;
    		$last = count( $lines ) - 1;

    		if (! ( mb_ereg_match( '^BEGIN:VCALENDAR', $lines[$first], $regex_opt ) and mb_ereg_match( '^END:VCALENDAR', $lines[$last], $regex_opt ) )) {
    			$first = null;
    			$last = null;
    			foreach ( $lines as $i => $line ) {
    				if (mb_ereg_match( '^BEGIN:VCALENDAR', $line, $regex_opt ))
    					$first = $i;

    				if (mb_ereg_match( '^END:VCALENDAR', $line, $regex_opt )) {
    					$last = $i;
    					break;
    				}
    			}
    		}

    		// Procesing
    		if (! is_null( $first ) and ! is_null( $last )) {
    			$lines = array_slice( $lines, $first + 1, ( $last - $first - 1 ), true );
    			$this->ical = [];
    			foreach ( $lines as $line ) {
    				if ($line === 'BEGIN:VEVENT') {
    					$event = [];
    				} else if ($line === 'END:VEVENT') {
    					$this->ical[md5($event['UID'])] = $event;
    				}
    				// start processing
    				if (strpos($line,'UID:') !== false) {
    					$event['UID'] = str_replace('UID:', '', $line);
    				} 
                    else if (strpos($line,'DTSTAMP:') !== false) {
    					$event['DTSTAMP'] = $this->clean_timestamp(str_replace('DTSTAMP:', '', $line));
    				} 
                    else if (strpos($line,'DESCRIPTION:') !== false) {
    					$event['DESCRIPTION'] = str_replace('DESCRIPTION:', '', $line);
    				} 
                    else if (strpos($line,'DTSTART:') !== false) {
    					$event['DTSTART-ORG'] = str_replace('DTSTART:', '', $line);
    					$event['DTSTART'] = $this->clean_timestamp(str_replace('DTSTART:', '', $line));
    				} 
                    else if (strpos($line,'DTSTART;VALUE=DATE:') !== false) {
    					if ( preg_match( '/DTSTART;VALUE=DATE\:(\d{4})(\d{2})(\d{2})/', $line, $matches ) ) {
    						$year 	= $matches[1];
    						$month 	= $matches[2];
    						$day 	= $matches[3];
    						$event['DTSTART-ORG'] = str_replace('DTSTART;VALUE=DATE:', '', $line);
    						$event['DTSTART'] 	= "{$year}-{$month}-{$day} 00:00:00";
    						$event['DTEND'] 	= "{$year}-{$month}-{$day} 00:00:00";
    					}
    				} 
                    else if (strpos($line,'DTEND:') !== false) {
    					$event['DTEND-ORG'] = str_replace('DTEND:', '', $line);
    					$event['DTEND'] = $this->clean_timestamp(str_replace('DTEND:', '', $line));
    				} 
                    else if (strpos($line,'DTEND;VALUE=DATE:') !== false) {
    					if ( preg_match( '/DTEND;VALUE=DATE\:(\d{4})(\d{2})(\d{2})/', $line, $matches ) ) {
    						$year 	= $matches[1];
    						$month 	= $matches[2];
    						$day 	= $matches[3];
    						$event['DTEND-ORG'] = str_replace('DTSTART;VALUE=DATE:', '', $line);
    						$event['DTEND'] 	= "{$year}-{$month}-{$day} 00:00:00";
    					}
    				} 
                    else if (strpos($line,'LOCATION:') !== false) {
    					$event['LOCATION'] = $this->clean_location( str_replace('LOCATION:', '', $line) );
    				} 
                    else if (strpos($line,'SUMMARY:') !== false) {
    					$event['SUMMARY'] = str_replace('SUMMARY:', '', $line);
    				} 
                    else if (strpos($line,'URL:') !== false) {
    					$event['URL'] = str_replace('URL:', '', $line);
    				}
    			}
    		}

    		return $this->ical;
    	}
	
    	private function clean_timestamp( $timestamp ) {
    		$result = false;
    		if ( preg_match( '/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z/', $timestamp, $matches ) ) {
    			$year 	= $matches[1];
    			$month 	= $matches[2];
    			$day 	= $matches[3];
    			$hour	= $matches[4];
    			$min 	= $matches[5];
    			$sec 	= $matches[6];
    			$result = "{$year}-{$month}-{$day} {$hour}:{$min}:{$sec}";
    			$result = date( 'Y-m-d H:i:s', strtotime( $result . " +1 HOUR") );
    		}
		
    		return $result;
    	}
	
    	private function clean_location( $location ) {
    		if ( preg_match( '/(\w+)\s+\(.*?\)/', $location, $matches ) ) {
    			$location = $matches[1];
    		}
		
    		return $location;
    	}
	
    }
    
}

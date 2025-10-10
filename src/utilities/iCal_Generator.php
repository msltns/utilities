<?php

namespace msltns\utilities;

/**
 * iCal_Generator is an easy to use class that generates an "ics" 
 * format file.
 *
 * @category 	Class
 * @package  	Utilities
 * @author 		msltns <info@msltns.com>
 * @version  	0.0.1
 * @since   	0.0.2
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
if ( ! class_exists( '\msltns\utilities\iCal_Generator' ) ) {
    
    class iCal_Generator {
	
        public function generate_ical( array $items, string $name = 'Calendar', string $timezone = 'Europe/Berlin' ): string {
        
            // date_default_timezone_set( $timezone );
            // setlocale ( LC_ALL, 'de_DE' );
            
            $iCal  = '';
    		$iCal .= "BEGIN:VCALENDAR\r\n";
    		$iCal .= "PRODID:-//MSLTNS//Events Calendar//EN\r\n";
    		$iCal .= "VERSION:2.0\r\n";
    		$iCal .= "CALSCALE:GREGORIAN\r\n";
    		$iCal .= "METHOD:PUBLISH\r\n";
    		$iCal .= "X-WR-CALNAME:{$name}\r\n";
    		$iCal .= "X-WR-TIMEZONE:{$timezone}\r\n";
	
    		if ( ! empty( $items ) ) {
			
    			foreach ( $items as $item ) {
				
    				$summary = $this->clean_calendar_string( $item['summary'] );
    			    $created = $this->convert_to_cal( time() );
                
                    $start_timestamp = $item['start_ts'];
                    $formatted_start_date = date( 'Ymd\THis', $start_timestamp );
                    
                    $formatted_end_date = $formatted_start_date;
                    $end_timestamp = ! empty( $item['end_ts'] ) ? $item['end_ts'] : false;
                    if ( $end_timestamp ) {
                        $formatted_end_date = date( 'Ymd\THis', $end_timestamp );
                    }
                    
                    $allday = isset( $item['allday'] ) ? $item['allday'] : false;
                    if ( $allday ) {
                        $formatted_start_date = date( 'Ymd', $start_timestamp );
                        if ( $end_timestamp ) {
                            $formatted_end_date = date( 'Ymd', $end_timestamp );
                        }
                    }
                    
                    $location = ! empty( $item['location'] ) ? $item['location'] : false;
                    if ( $location ) {
                        $location = str_replace( ',', '\,', $location );
                    }
                    
                    $trigger = false;
                    $alarm = isset( $item['alarm'] ) ? $item['alarm'] : false;
                    $alarm = in_array( $alarm, [ true, 'true', 'yes', 'on', '1', 1, 'active' ], true );
                    if ( $alarm ) {
                        if ( ! empty( $item['trigger'] ) ) {
                            /* notification at the trigger time */
                            $trigger = 'TRIGGER:' . $item['trigger'];
                        } else if ( ! empty( $item['trigger_ts'] ) ) {
                            /* notification at the trigger time */
                            $trigger_dt = date( 'Ymd\THis', $item['trigger_ts'] );
                            $trigger = "TRIGGER;VALUE=DATE-TIME:{$trigger_dt}Z";
                        } else {
                            /* default notification at the event start time */
                            $trigger = 'TRIGGER:-P0DT0H0M0S';
                        }
                    }
                    
    				$uuid = vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( random_bytes( 16 ) ), 4 ) );

    				$iCal .= "BEGIN:VEVENT\r\n";
    				$iCal .= "CREATED:{$created}Z\r\n";
    				$iCal .= "UID:{$uuid}\r\n";
    				$iCal .= "DTSTAMP:{$formatted_start_date}Z\r\n";
                    
                    if ( $allday ) {
                        $iCal .= "DTSTART;VALUE=DATE:{$formatted_start_date}\r\n";
    					$iCal .= "DTEND;VALUE=DATE:{$formatted_end_date}\r\n";
                    } else if ( $formatted_end_date ) {
    					$iCal .= "DTSTART;TZID={$timezone}:{$formatted_start_date}\r\n";
    					$iCal .= "DTEND;TZID={$timezone}:{$formatted_end_date}\r\n";
    				} else {
    					$iCal .= "DTSTART;TZID={$timezone}:{$formatted_start_date}\r\n";
    					$iCal .= "DTEND;TZID={$timezone}:{$formatted_start_date}\r\n";
    				}
                    
                    if ( $location ) {
                        $iCal .= "LOCATION:{$location}\r\n";
                    }
    				
    				$iCal .= "SEQUENCE:0\r\n";
    				$iCal .= "STATUS:CONFIRMED\r\n";
    				$iCal .= "SUMMARY:{$summary}\r\n";
    				$iCal .= "TRANSP:OPAQUE\r\n";
    				$iCal .= "X-APPLE-TRAVEL-ADVISORY-BEHAVIOR:AUTOMATIC\r\n";
    				
                    if ( $alarm ) {
        				$iCal .= "BEGIN:VALARM\r\n";
        				$iCal .= "ACTION:DISPLAY\r\n";
        				$iCal .= "DESCRIPTION:{$summary}\r\n";
                        $iCal .= "{$trigger}\r\n";
        				$iCal .= "END:VALARM\r\n"; 
                    }
                    
    				$iCal .= "END:VEVENT\r\n"; 
    			}
    		}
    		$iCal .= "END:VCALENDAR";
	    
    	    return $iCal;
        }
    
    	private function clean_calendar_string( $string = false ) {
    		if ( $string ) :
    			preg_match_all( "/<\!--([\\s\\S]*?)-->/", $string, $matches );
    			if ( isset( $matches[0] ) && !empty( $matches[0] ) ):
    				foreach ( $matches[0] as $match ):
    					$string = str_replace( $match, '', $string );
    				endforeach;
    			endif;

    			if ( function_exists( 'mb_convert_encoding' ) ):
    				$string = mb_convert_encoding( $string, 'UTF-8' );
    			else:
    				$string = htmlspecialchars_decode( utf8_decode( htmlentities( $string, ENT_COMPAT, 'utf-8', false ) ) );
    			endif;

    			return $string;
    		else:
    			return false;
    		endif;
    	}
    
    	private function convert_to_cal( $timestamp, $all_day = false ) {
    		if ( $all_day ):
    			return date( 'Ymd', strtotime( date( 'Y-m-d H:i:s', $timestamp ) ) );
    		else:
    			return date( 'Ymd\THis', strtotime( date( 'Y-m-d H:i:s', $timestamp ) ) );
    		endif;
    	}
	
    }
    
}

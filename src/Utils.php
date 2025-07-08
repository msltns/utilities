<?php

namespace msltns\utilities;

use msltns\utilities\Database_Connector;

/**
 * Class Utils provides several useful helper methods.
 *
 * @category 	Class
 * @package  	Utilities
 * @author 		Daniel Muenter <info@msltns.com>
 * @version  	0.0.1
 * @since       0.0.1
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

if ( ! defined( 'MSLTNS_UTILS_TRANSIENTS_TABLE' ) ) {
	define( 'MSLTNS_UTILS_TRANSIENTS_TABLE', 'msltns_utils_transients' );
}

if ( ! class_exists( '\msltns\utilities\Utils' ) ) {
	
	class Utils {
		
		/**
		 * @var \Database_Connector
		 */
		private $db;
		
		/**
		 * @var \Utils
		 */
		private static $instance;
		
		/**
		 * Main constructor.
		 *
		 * @return void
		 */
		private function __construct() {
			
		}
        
		/**
		 * Singleton instance.
		 * 
         * @since 0.0.1
		 * @return \Utils
		 */
		public static function instance() {
			return self::getInstance();
		}
		
		/**
		 * Singleton instance.
		 * 
		 * @return \Utils
		 */
		public static function get_instance() {
			return self::getInstance();
		}
		
		/**
		 * Singleton instance.
		 * 
		 * @return \Utils
		 */
		public static function getInstance() {
			if ( !isset( self::$instance ) ) {
				self::$instance = new self();
				self::$instance->init();
			}
			return self::$instance;
		}
		
		/**
		 * Initialization method.
		 * 
		 * @return void
		 */
		private function init() {
			
		}
		
		/**
		 * A function to format price expression.
		 *
		 * @param   mixed 	$price
		 * @param   string	$currency
		 * @param   string	$format
		 * @return  string
		 */
		public function get_currency_formatted( $price, $currency = '€', $format = '# @' ) {
			$formatted_price = '';
			$formatted_price = str_replace( '@', $currency, $format );
			
			return str_replace( '#', number_format( $price, 2, ',', '.' ), $formatted_price );
		}
		
		/**
		 * A function to format a date expression.
		 *
		 * @param   string 	$date_string
		 * @param   bool	$use_day_string
		 * @param   bool	$use_time
		 * @return  string
		 */
		public function get_date_formatted( $date_string, $use_day_string = false, $use_time = false ) {
			$result = "";
			if ( $date_string !== null && !empty( $date_string ) ) {
				// format date string
				
				if ( preg_match( '/(\d+)\.(\d+)\.(\d+)/i', $date_string, $matches ) ) {
					$day = $matches[1]; // day
					$day = ( intval( $day ) < 10 ) ? '0' . intval( $day ) : $day;
					$month = $matches[2]; // month
					$month = ( intval( $month ) < 10 ) ? '0' . intval( $month ) : $month;
					$year = $matches[3]; // year
					if ( intval( $year ) < 50 ) {
						$year = '20' . intval( $year );
					} else if ( intval( $year ) < 100 ) {
						$year = '19' . intval( $year );
					}
					$date_string = $day . '.' . $month . '.' . $year;
				}
				
				if ( $use_day_string && $use_time ) {
					$result = gmdate( '%A, %d.%m.%Y - %T', strtotime( $date_string ) ) . " Uhr";
				} else if ( $use_day_string ) {
					$result = gmdate( '%A, %d.%m.%Y', strtotime( $date_string ) );
				} else if ( $use_time ) {
					$result = gmdate( '%d.%m.%Y - %T', strtotime( $date_string ) ) . " Uhr";
				} else {
					$result = gmdate( '%d.%m.%Y', strtotime( $date_string ) );
				}
			}
			
			return $result;
		}
        
        /**
		 * Sets up a directory.
		 *
		 * @param   string 	$directory
		 * @param	int		$permissions
		 * @param	bool	$recursive
		 * @return  bool
		 */
		public function setup_directory( $directory, $permissions = 0775, $recursive = true ) {
			return $this->setup_dir( $directory, $permissions, $recursive );
		}
		
		/**
		 * Sets up a directory.
		 *
		 * @param   string 	$directory
		 * @param	int		$permissions
		 * @param	bool	$recursive
		 * @return  bool
		 */
		public function setup_dir( $directory, $permissions = 0775, $recursive = true ) {
			if ( ! is_dir( $directory ) ) {
				return mkdir( $directory, $permissions, $recursive );
		    }
			
			return false;
		}
		
		/**
		 * Delets a given file.
		 *
		 * @param   string 	$full_path
		 * @return  bool
		 */
		public function delete_file( $full_path ) {
			if ( file_exists( $full_path ) ) {
				return @unlink( $full_path );
			}
			
			return false;
		}
		
		/**
		 * Copy a file.
		 *
		 * @param   string 	$from
		 * @param	string	$to
		 * @return  bool
		 */
		public function copy_file( $from, $to ) {
			if ( !$this->exists( $from ) ) {
				return false;
			}
			if ( $this->delete_file( $to ) ) {
				return copy( $from, $to );
			}
			
			return false;
		}
		
		/**
		 * Checks if a file exists.
		 *
		 * @param   string 	$full_path
		 * @return  bool|string
		 */
		public function exists( $full_path ) {
			if ( ! file_exists( $full_path ) ) {
				return false;
			}
			return $full_path;
		}
		
		/**
		 * Loads the content of a file.
		 *
		 * @param   string 	$full_path
		 * @return  string|bool
		 */
		public function load_file_content( $full_path ) {
			if ( preg_match( '/(https?:\/\/).*?/', $full_path, $matches ) ) {
				$options = [
				    "ssl" => [
				        "verify_peer" 		=> false,
				        "verify_peer_name" 	=> false,
				    ]
				];
				return file_get_contents( $full_path, false, stream_context_create( $options ) );
			}
			
			return file_get_contents( $full_path );
		}
		
		/**
		 * Casts an object to a given class.
		 *
		 * @param   mixed 	$obj
		 * @param	string	$to_class
		 * @return  Object|bool
		 */
		public function cast( $obj, $to_class ) {
			if ( class_exists( $to_class ) ) {
				$obj_in  = $this->maybe_serialize( $obj );
				$obj_out = 'O:' . strlen( $to_class ) . ':"' . $to_class . '":' . substr( $obj_in, $obj_in[2] + 7 );
				return @unserialize( $obj_out );
			}
			
			return false;
		}
		
		/**
		 * Converts cvs file content to a json string.
		 *
		 * @param   string 	$filename
		 * @param	string	$delimiter
		 * @return  string|bool
		 */
		public function convert_csv_to_json( $filename, $delimiter = ';' ) {
		    
			if ( empty( $filename ) ) {
				return false;
			}
			
			$data 		= array_map( 'str_getcsv', file( $filename ) );
			$json_arr 	= array();
			$keys 		= explode( $delimiter, $data[0][0] );
			$cols 		= count( $keys );
			$count 		= count( $data );
			for ( $i = 1; $i < $count; $i++ ) {
				$row = explode( $delimiter, $data[$i][0] );
				$arr = array();
				for ( $j = 0; $j < $cols; $j++ ) {
					$arr[$keys[$j]] = $row[$j];
				}
				$json_arr[] = $arr;							
			}

			return json_encode( $json_arr );
		}
		
		/**
		 * Creates a storable representation of a value.
		 *
		 * @param   mixed	$data
		 * @return  mixed
		 */
		public function maybe_serialize( $data ) {
		    if ( is_array( $data ) || is_object( $data ) ) {
		        return serialize( $data );
		    }
		    /**
			 * @ignore
		     * Double serialization is required for backward compatibility.
		     */
		    if ( is_serialized( $data, false ) ) {
		        return serialize( $data );
		    }
 
		    return $data;
		}
		
		/**
		 * Generates a value in PHP from a stored data format.
		 *
		 * @param   string	$data
		 * @return  mixed
		 */
		public function maybe_unserialize( $original ) {
		    if ( $this->is_serialized( $original ) ) {
		    	/**
				 * @ignore
				 * don't attempt to unserialize data that wasn't serialized going in
				 */
				return @unserialize( $original );
		    }
		    return $original;
		}
		
		/**
		 * Checks whether a value is serialized.
		 *
		 * @param   string	$data
		 * @param 	bool	$strict
		 * @return  mixed
		 */
		public function is_serialized( $data, $strict = true ) {
		    /**
			 * @ignore
			 * If it isn't a string, it's not serialized.
			 */
		    if ( ! is_string( $data ) ) {
		        return false;
		    }
		    $data = trim( $data );
		    if ( 'N;' == $data ) {
		        return true;
		    }
		    if ( strlen( $data ) < 4 ) {
		        return false;
		    }
		    if ( ':' !== $data[1] ) {
		        return false;
		    }
		    if ( $strict ) {
		        $lastc = substr( $data, -1 );
		        if ( ';' !== $lastc && '}' !== $lastc ) {
		            return false;
		        }
		    } else {
		        $semicolon = strpos( $data, ';' );
		        $brace     = strpos( $data, '}' );
		        /**
				 * @ignore
				 * Either ; or } must exist.
				 */
		        if ( false === $semicolon && false === $brace )
		            return false;
		        /**
				 * @ignore
				 * But neither must be in the first X characters.
				 */
		        if ( false !== $semicolon && $semicolon < 3 )
		            return false;
		        if ( false !== $brace && $brace < 4 )
		            return false;
		    }
		    $token = $data[0];
		    switch ( $token ) {
		        case 's' :
		            if ( $strict ) {
		                if ( '"' !== substr( $data, -2, 1 ) ) {
		                    return false;
		                }
		            } elseif ( false === strpos( $data, '"' ) ) {
		                return false;
		            }
		            /**
					 * @ignore
					 * Or else fall through.
					 */
		        case 'a' :
		        case 'O' :
		            return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		        case 'b' :
		        case 'i' :
		        case 'd' :
		            $end = $strict ? '$' : '';
		            return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		    }
			
		    return false;
		}
		
		/**
		 * Determines the IP address of a user.
		 *
		 * @return  string
		 */
		public function get_user_ip() {
			$visitor_ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
			if ( !empty($_SERVER['HTTP_CLIENT_IP'] ) ) {
		        $visitor_ip = $_SERVER['HTTP_CLIENT_IP'];
			} else if ( !empty($_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		        $visitor_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
			} else if ( !empty($_SERVER['HTTP_X_FORWARDED'] ) ) {
		        $visitor_ip = $_SERVER['HTTP_X_FORWARDED'];
			} else if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		        $visitor_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}

		    if ( stristr( $visitor_ip, ',' ) ) {
				//get first address because this will likely be the original connecting IP
		        $visitor_ip = trim( reset( ( explode( ',', $visitor_ip ) ) ) );
		    }
	
		    //Now remove port portion if applicable
		    if ( strpos( $visitor_ip, '.' ) !== FALSE && strpos( $visitor_ip, ':' ) !== FALSE ) {
		        //likely ipv4 address with port
		        $visitor_ip = preg_replace( '/:\d+$/', '', $visitor_ip ); //Strip off port
		    }
	
			return $visitor_ip;
		}
        
		/**
		 * @ignore
		 * 
		 * Determines all transmitted IP addresses of a user.
		 *
		 * @return string
		 */
    	public function get_all_user_ips() {
    		$visitor_ip_data = 'REMOTE_ADDR: ' . $_SERVER['REMOTE_ADDR'];
    		if ( !empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
    	        $visitor_ip_data .= ', HTTP_CF_CONNECTING_IP: ' . $_SERVER['HTTP_CF_CONNECTING_IP'];
    		}
    		if ( !empty( $_SERVER['HTTP_PC_REMOTE_ADDR'] ) ) {
    	        $visitor_ip_data .= ', HTTP_PC_REMOTE_ADDR: ' . $_SERVER['HTTP_PC_REMOTE_ADDR'];
    		}
    		if ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    	        $visitor_ip_data .= ', HTTP_X_FORWARDED_FOR: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];
    		}
    		if ( !empty( $_SERVER['HTTP_X_FORWARDED'] ) ) {
    	        $visitor_ip_data .= ', HTTP_X_FORWARDED: ' . $_SERVER['HTTP_X_FORWARDED'];
    		}
    		if ( !empty( $_SERVER['X_FORWARDED_FOR'] ) ) {
    	        $visitor_ip_data .= ', X_FORWARDED_FOR: ' . $_SERVER['X_FORWARDED_FOR'];
    		}
    		if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
    	        $visitor_ip_data .= ', HTTP_CLIENT_IP: ' . $_SERVER['HTTP_CLIENT_IP'];
    		}
    		if ( !empty( $_SERVER['VIA'] ) ) {
    	        $visitor_ip_data .= ', VIA: ' . $_SERVER['VIA'];
    		}
    		if ( !empty( $_SERVER['USERAGENT_VIA'] ) ) {
    	        $visitor_ip_data .= ', USERAGENT_VIA: ' . $_SERVER['USERAGENT_VIA'];
    		}
    		if ( !empty( $_SERVER['FORWARDED'] ) ) {
    	        $visitor_ip_data .= ', FORWARDED: ' . $_SERVER['FORWARDED'];
    		}
    		if ( !empty( $_SERVER['PROXY_CONNECTION'] ) ) {
    	        $visitor_ip_data .= ', PROXY_CONNECTION: ' . $_SERVER['PROXY_CONNECTION'];
    		}
    		if ( !empty( $_SERVER['XPROXY_CONNECTION'] ) ) {
    	        $visitor_ip_data .= ', XPROXY_CONNECTION: ' . $_SERVER['XPROXY_CONNECTION'];
    		}
    		if ( !empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
    	        $visitor_ip_data .= ', HTTP_X_REAL_IP: ' . $_SERVER['HTTP_X_REAL_IP'];
    		}
    		return $visitor_ip_data;
    	}
        
        /**
		 * Obtains the related country code for a given IP address.
		 *
		 * @param string 	$ip     The IP address to be resolved.
		 * @return string
		 */
    	public function get_country_code_by_ip( $ip ) {
            if ( !in_array( $ip, [ '127.0.0.1', '::1' ], true ) ) {
                $country = file_get_contents( "https://ipinfo.io/{$ip}/country" );
                return !empty( $country ) ? trim( $country ) : '';
            }
            return '';
        }
        
        /**
		 * Obtains the related timezone for a given IP address.
		 * 
         * @since 0.0.1
		 * @param string 	$ip     The IP address to be resolved.
		 * @return string
		 */
    	public function get_timezone_by_ip( $ip ) {
            if ( !in_array( $ip, [ '127.0.0.1', '::1' ], true ) ) {
                $timezone = file_get_contents( "https://ipinfo.io/{$ip}/timezone" );
                return !empty( $timezone ) ? trim( $timezone ) : '';
            }
            return '';
        }
		
		/**
		 * Get request referer.
		 * 
		 * @return string|bool
		 */
		public function get_referer() {
			
			$ref = false;
			
			if ( !empty( $_GET['referer'] ) ) {
				$ref = $_GET['referer'];
			}
			else if ( !empty( $_POST['referer'] ) ) {
				$ref = $_POST['referer'];
			}
			else if ( !empty( $_REQUEST['referer'] ) ) {
				$ref = $_REQUEST['referer'];
			}
			else if ( !empty( $_SERVER['HTTP_REFERER'] ) ) {
				$ref = $_SERVER['HTTP_REFERER'];
			}
			
			return $ref;
		}
		
		/**
		 * Parses a URL and returns its components.
		 *
		 * @param   string	$url
		 * @return  array
		 * @see 	https://www.php.net/manual/de/function.parse-url.php
		 */
		public function parse_url( $url = '' ) {
            $r  = "^(?:(?P<scheme>\w+)://)?";
            $r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
            $r .= "(?P<host>(?:(?P<subdomain>[a-zA-Z0-9\-\_\.]+)\.)?" . "(?P<domain>[a-zA-Z0-9\-\_]+\.(?P<extension>\w+)))";
            $r .= "(?::(?P<port>\d+))?";
            $r .= "(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";
            $r .= "(?:\?(?P<arg>[\w=&]+))?";
            $r .= "(?:#(?P<anchor>\w+))?";
            $r  = "!$r!";   // Delimiters
    
            preg_match( $r, $url, $out );
            
            $params = [];
            if ( !empty( $out['arg'] ) ) {
                $tmp = explode( '&', $out['arg'] );
                foreach( $tmp as $kv ) {
                    $kv = explode( '=', $kv );
                    $params[$kv[0]] = $kv[1];
                }
            }
    
            $result = [
                'input'     => $out['0'],
                'scheme'    => $out['scheme'],
                'login'     => isset( $out['login'] ) ? $out['login'] : '',
                'pass'      => isset( $out['pass'] ) ? $out['pass'] : '',
                'host'      => $out['host'],
                'subdomain' => $out['subdomain'],
                'domain'    => $out['domain'],
                'tld'       => $out['extension'],
                'port'      => isset( $out['port'] ) ? $out['port'] : '',
                'path'      => isset( $out['path'] ) ? $out['path'] : '',
                'file'      => isset( $out['file'] ) ? $out['file'] : '',
                'params'    => $params,
                'anchor'    => isset( $out['anchor'] ) ? $out['anchor'] : '',
            ];
    
            return $result;
        }
        
    	/**
    	 * Sets the scheme of a given URL.
    	 * 
    	 * @param string      $url         The complete URL including scheme and path.
    	 * @param string      $scheme      Scheme applied to the URL. One of 'http', 'https', or 'relative'.
    	 * @param string|null $orig_scheme Scheme requested for the URL. One of 'http', 'https', 'login',
    	 *                                 'login_post', 'admin', 'relative', 'rest', 'rpc', or null.
    	 */
        public function set_url_scheme( $url, $scheme = null ) {
        	$orig_scheme = $scheme;

        	if ( ! $scheme ) {
        		$scheme = $this->is_ssl() ? 'https' : 'http';
        	}

        	$url = trim( $url );
        	if ( $this->starts_with( $url, '//' ) ) {
        		$url = 'http:' . $url;
        	}

        	if ( 'relative' === $scheme ) {
        		$url = ltrim( preg_replace( '#^\w+://[^/]*#', '', $url ) );
        		if ( '' !== $url && '/' === $url[0] ) {
        			$url = '/' . ltrim( $url, "/ \t\n\r\0\x0B" );
        		}
        	} else {
        		$url = preg_replace( '#^\w+://#', $scheme . '://', $url );
        	}

        	return $url;
        }
        
        /**
         * Determines if SSL is used.
         * 
         * @return  bool True if SSL, otherwise false.
         */
        public function is_ssl() {
        	if ( isset( $_SERVER['HTTPS'] ) ) {
        		if ( 'on' === strtolower( $_SERVER['HTTPS'] ) ) {
        			return true;
        		}

        		if ( '1' === (string) $_SERVER['HTTPS'] ) {
        			return true;
        		}
        	} elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' === (string) $_SERVER['SERVER_PORT'] ) ) {
        		return true;
        	}

        	return false;
        }
        
        
		/**
		 * Checks whether a class function exists.
		 *
		 * @param   string	$class
		 * @param 	string	$function
		 * @return  bool
		 * @see 	https://www.php.net/manual/de/function.get-class-methods.php
		 */
		public function class_function_exists( $class = '', $function = '' ) {
			if ( empty( $class ) || empty( $function ) ) {
				return false;
			}
			if ( class_exists( $class ) ) {
				if ( in_array( $function, get_class_methods( $class ) ) ) {
					return true;
				}
			}
			
			return false;
		}
        
        /**
         * Sends a JSON response back to an Ajax request.
         *
         * @since 0.0.1
         *
         * @param mixed $response    Variable (usually an array or object) to encode as JSON,
         *                           then print and die.
         * @param int   $status_code Optional. The HTTP status code to output. Default null.
         * @param int   $flags       Optional. Options to be passed to json_encode(). Default 0.
         */
        public function send_json( $response, $status_code = null, $flags = 0 ) {
            
            $status_codes = array(
                '200' => 'OK',
                '400' => 'Bad Request',
                '401' => 'Unauthorized',
                '403' => 'Forbidden',
                '404' => 'Not Found',
                '500' => 'Internal Server Error',
            );
            
            if ( ! is_null( $status_code ) ) {
                $status_message = $status_codes["{$status_code}"];
                header( "HTTP/1.1 {$status_code} {$status_message}" );
            }
            
            header( 'Content-Type: application/json; charset=utf-8' );
            echo json_encode( $response, $flags );
        	exit;
        }

        /**
         * Sends a JSON response back to an Ajax request, indicating success.
         *
         * @since 0.0.1
         *
         * @param mixed $value       Optional. Data to encode as JSON, then print and die. Default null.
         * @param int   $status_code Optional. The HTTP status code to output. Default null.
         * @param int   $flags       Optional. Options to be passed to json_encode(). Default 0.
         */
        function send_json_success( $value = null, $status_code = 200, $flags = 0 ) {
        	$response = array( 'success' => true );

        	if ( isset( $value ) ) {
        		$response['data'] = $value;
        	}

        	$this->send_json( $response, $status_code, $flags );
        }

        /**
         * Sends a JSON response back to an Ajax request, indicating failure.
         *
         * If the `$value` parameter is a WP_Error object, the errors
         * within the object are processed and output as an array of error
         * codes and corresponding messages. All other types are output
         * without further processing.
         *
         * @since 0.0.1
         *
         * @param mixed $value       Optional. Data to encode as JSON, then print and die. Default null.
         * @param int   $status_code Optional. The HTTP status code to output. Default null.
         * @param int   $flags       Optional. Options to be passed to json_encode(). Default 0.
         */
        function send_json_error( $value = null, $status_code = 500, $flags = 0 ) {
        	$response = array( 'success' => false );
        	if ( isset( $value ) ) {
        		$response['data'] = $value;
        	}

        	$this->send_json( $response, $status_code, $flags );
        }
        
		/**
		 * Sorts an multidimensional array.
		 *
		 * @param   array	$orig
		 * @param   string	$sort_by
		 * @return  array
		 */
        public function array_sort( $orig = [], $sort_by = 'name' ) {
            if ( empty( $orig ) ) {
                return $orig;
            }
            $modif    = [];
            $result   = [];
            $search   = [ "Ä","ä","Ö","ö","Ü","ü","ß","-", "Å" ];
            $replace  = [ "Ae","ae","Oe","oe","Ue","ue","ss"," ", "Ae" ];
            foreach( $orig as $key => $val ) {
                $modif[$key] = str_replace( $search, $replace, $val );
            }
            array_multisort( array_column( $modif, $sort_by ), SORT_ASC , array_column( $modif, $sort_by ), SORT_ASC, $modif );
            foreach( $modif as $key => $val ) {
                $result[$key] = str_replace( $replace, $search, $val );
            }
            
            return $result;
        }
        
		/**
		 * Inserts an array at an arbitrary position of another array.
		 *
		 * @param   array $array
		 * @param   int   $position
		 * @param   array $insert_array
		 * @return  array
		 */
		public function array_insert( $array, $position, $insert_array ) {
			if ( $position < 0 && $position >= count( $array ) ) {
				$this->log( 'Position must be greater than or equal to 0 and less than array length.', 'error' );
				return $array;
			}
			$first_array = array_splice( $array, 0, $position );
			
			return array_merge( $first_array, $insert_array, $array );
		}
        
        /**
         * Imports variables of an array into the current symbol table.
         * 
         * @param array $array  The array to be extracted.
         * @return bool|WP_Error  True if validation succeeded, WP_Error else.
         */
        public function extract_array_variables( $array ) {
            foreach( $array as $key => $value ) {
                ${$key} = $value;
            }
        }
        
        /**
		 * Stringifies a data object.
		 *
		 * @param   mixed $data
		 * @return  string
		 */
		public function stringify( $data ) {
			$result = $data;
			if ( is_object( $data ) ) {
				$result = $this->maybe_serialize( $data );
			} else if ( is_array( $data ) ) {
				$result = json_encode( $data );
			}			
			return $result;
		}
        
		/**
		 * Shortens a given string to a given length.
		 *
		 * @param string $str       The string to shorten.
		 * @param int    $length    The length.
		 * @param string $suffix    The appendix.
		 * @return string
		 */
        public function shorten_string( $str, $length, $suffix = '...' ) {
            if ( strlen( $str ) > $length ) {
                $str = mb_substr( $str, 0, $length, 'utf-8' ) . $suffix;
            }
            return $str;
        }
        
		/**
		 * Checks whether a given string starts with a certain string sequence.
		 *
		 * @param string 	$haystack   The string to search in.
		 * @param string 	$needle     The string to search for.
		 * @return bool
		 */
    	public function starts_with( string $haystack, string $needle ): bool {
            if ( empty( $haystack ) || empty( $needle ) ) {
                return false;
            }
    		$needle = str_replace( [ '/', '(', ')' ], [ '\\/', '\\(', '\\)' ], $needle );
    		$needle = "/^{$needle}/";
    		return preg_match( $needle, $haystack );
    	}
        
		/**
		 * Checks whether a given string contains a certain string sequence.
		 *
		 * @param string 	$haystack   The string to search in.
		 * @param string 	$needle     The string to search for.
		 * @return bool
		 */
    	public function contains( string $haystack, string $needle ): bool {
            if ( empty( $haystack ) || empty( $needle ) ) {
                return false;
            }
            $needle = str_replace( [ '/', '(', ')' ], [ '\\/', '\\(', '\\)' ], $needle );
    		$needle = "/{$needle}/";
            return preg_match( $needle, $haystack );
    	}
		
		/**
		 * Checks whether a given string ends with a certain string sequence.
		 *
		 * @param string 	$haystack   The string to search in.
		 * @param string 	$needle     The string to search for.
		 * @return bool
		 */
    	public function ends_with( string $haystack, string $needle ): bool {
            if ( empty( $haystack ) || empty( $needle ) ) {
                return false;
            }
    		$needle = str_replace( [ '/', '(', ')' ], [ '\\/', '\\(', '\\)' ], $needle );
    		$needle = "/{$needle}$/";
    		return preg_match( $needle, $haystack );
    	}
        
        /**
         * Appends a trailing slash.
         *
         * Will remove trailing forward and backslashes if it exists already before adding
         * a trailing forward slash. This prevents double slashing a string or path.
         *
         * The primary use of this is for paths and thus should be used for paths. It is
         * not restricted to paths and offers no specific path support.
         *
         * @since 0.0.1
         *
         * @param string $value Value to which trailing slash will be added.
         * @return string String with trailing slash added.
         */
        public function trailingslashit( $value ) {
        	return $this->untrailingslashit( $value ) . '/';
        }
        
        /**
         * Removes trailing forward slashes and backslashes if they exist.
         *
         * The primary use of this is for paths and thus should be used for paths. It is
         * not restricted to paths and offers no specific path support.
         *
         * @since 0.0.1
         *
         * @param string $text Value from which trailing slashes will be removed.
         * @return string String without the trailing slashes.
         */
        public function untrailingslashit( $value ) {
        	return rtrim( $value, '/\\' );
        }
        
        /**
         * Escaping for HTML blocks.
         *
         * @since 0.0.1
         *
         * @param string $text
         * @return string
         */
        public function esc_html( $text ) {
        	$safe_text = $this->check_invalid_utf8( $text );
        	$safe_text = htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
            
        	return $safe_text;
        }
        
        /**
         * Generates an image tag.
         *
         * @since 0.0.1
         *
         * @param string $src
         * @param string $width
         * @param string $height
         * @param string $class
         * @param string $title
         * @param string $alt
         * @return string
         */
        public function create_img_tag( $src, $width = '', $height = '', $class = '', $title = '', $alt = '' ) {
            $img = '<img src="' . $src . '"';
            if ( ! empty( $width ) ) {
                $img .= ' width="' . $width . '"';
            }
            if ( ! empty( $height ) ) {
                $img .= ' height="' . $height . '"';
            }
            if ( ! empty( $class ) ) {
                $img .= ' class="' . $class . '"';
            }
            if ( ! empty( $title ) ) {
                $img .= ' title="' . $title . '"';
            }
            if ( ! empty( $alt ) ) {
                $img .= ' alt="' . $alt . '"';
            }
            $img .= '>';
            
            return $img;
        }
        
        /**
         * Checks for invalid UTF8 in a string.
         *
         * @since 0.0.1
         *
         * @param string $text   The text which is to be checked.
         * @param bool   $strip  Optional. Whether to attempt to strip out invalid UTF8. Default false.
         * @return string The checked text.
         */
        private function check_invalid_utf8( $text, $strip = false ) {
            $text = (string) $text;

        	if ( 0 === strlen( $text ) ) {
        		return '';
        	}

        	// Store the site charset as a static to avoid multiple calls to get_option().
        	static $is_utf8 = null;
        	if ( ! isset( $is_utf8 ) ) {
        		$is_utf8 = mb_check_encoding( $text, 'UTF-8' );
        	}
        	if ( ! $is_utf8 ) {
        		return $text;
        	}

        	// Check for support for utf8 in the installed PCRE library once and store the result in a static.
        	static $utf8_pcre = null;
        	if ( ! isset( $utf8_pcre ) ) {
        		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
        		$utf8_pcre = @preg_match( '/^./u', 'a' );
        	}
        	// We can't demand utf8 in the PCRE installation, so just return the string in those cases.
        	if ( ! $utf8_pcre ) {
        		return $text;
        	}

        	if ( 1 === @preg_match( '/^./us', $text ) ) {
        		return $text;
        	}

        	// Attempt to strip the bad chars if requested (not recommended).
        	if ( $strip && function_exists( 'iconv' ) ) {
        		return iconv( 'utf-8', 'utf-8', $text );
        	}

        	return '';
        }
        
        /**
		 * @method add_action
		 *
		 * @param int     $length
		 * @param bool    $special_chars
		 * @param bool    $extra_special_chars
		 * @return string
		 */
		public function generate_password( $length = 24, $special_chars = true, $extra_special_chars = true ) {
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			if ( $special_chars ) {
				$chars .= '!@#$%^&*()';
			}
			if ( $extra_special_chars ) {
				$chars .= '-_[]{}<>~`+=,.;:/?|';
			}

			$password = '';
			for ( $i = 0; $i < $length; $i++ ) {
				$password .= substr( $chars, rand( 0, strlen( $chars ) - 1 ), 1 );
			}

			return $password;
		}
		
		/**
		 * Get a random char.
		 *
		 * @return string
		 */
		public function get_random_char() {
			$chars = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789-_@#*+-_.§$%&^?<>:;,!";
	
			return $chars[ rand( 0, strlen( $chars ) -1 ) ];
		}
		
		/**
		 * Gets the database table prefix.
		 *
		 * @return  string
		 */
		private function get_db_prefix() {
			$prefix = '';
			if ( $this->is_wp_env() ) {
				global $wpdb;
				$prefix = $wpdb->prefix;
			}
			else if ( defined( 'DB_PREFIX' ) ) {
				$prefix = DB_PREFIX;
			}
			return $prefix;
		}
		
		/**
		 * Gets the database object.
		 *
		 * @return \Database_Connector
		 */
		private function get_db() {
            Database_Connector::initializeConnection();
			return Database_Connector::getDb();
		}
        
		/**
		 * Escapes a given string.
		 *
         * @param string $string
		 * @return string
		 */
        public function esc_string( $string ) {
            return str_replace( ["'", '"'], ["\'", '\"'], $string );
        }
        
		/**
		 * Unescapes a given string.
		 *
         * @param string $string
		 * @return string
		 */
        public function unesc_string( $string ) {
            return str_replace( ["\'", '\"'], ["'", '"'], $string );
        }
		
		/**
		 * Writes debug messages to a logfile.
		 *
		 * @param mixed 	$message 	Debug message.
		 * @param string 	$level   	Debug level.
		 * @param array     $context   	Debug context parameters.
		 * @return  void
		 */
	    public function log( $message, string $level = 'info', array $context = [] ): void {
			if ( is_array( $message ) || is_object( $message ) ) {
                $message = print_r( $message, true );
            }
            
			$path = '';
			if ( $this->is_wp_env() && defined( 'WP_CONTENT_DIR' ) ) {
				$path = WP_CONTENT_DIR . '/debug.log';
			}
            
            if ( class_exists( '\msltns\logging\Logger' ) ) {
    			$logger = \msltns\logging\Logger::getInstance( 'Utils', $path );
                $logger->log( $message, $level, $context );
            } else {
                error_log( '[' . strtoupper( $level ) . '] ' . $message );
            }
	    }
		
		/**********************************************
		 * 
		 * Transient functionality
		 * 
		 * Transients provide a convenient way to store 
		 * and use cached objects. Transients live for  
		 * a specified amount of time, or until you need  
		 * them to expire when a resource from the API  
		 * has been updated. Using the transient  
		 * functionality in WordPress may be the easiest  
		 * to use caching system you ever encounter.
		 * 
		 **********************************************/
		
		/**
		 * Adds a new transient or updates it if existing.
		 *
		 * @param   string 	$transient      The transient key.
		 * @param	mixed   $value          The value to store.
		 * @param	int     $expiration     The expiration time in seconds.
		 * @return  bool
		 */
		public function set_transient( string $transient, mixed $value, int $expiration ) {
		    
			$db 		= $this->get_db();
			$prefix 	= $this->get_db_prefix();
			$value      = $this->maybe_serialize( $value );
            $expiration = "TIMESTAMPADD( SECOND, {$expiration}, CURRENT_TIMESTAMP )";
		
			$sql = "INSERT INTO `" . $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE . "` ( 
						`transient`, 
						`value`, 
						`expiration` 
					) 
					VALUES ( 
						'{$transient}', 
						'{$value}', 
						{$expiration} 
					)
					ON DUPLICATE KEY UPDATE 
						`value` = '{$value}', 
						`expiration` = {$expiration}
					;";
		
            return $db->query( $sql );
		}
		
		/**
		 * Gets an existing transient from database.
		 *
		 * @param   string      $transient      The transient key.
		 * @return  mixed|bool
		 */
		public function get_transient( string $transient ) {
		
			$db 	= $this->get_db();
			$prefix = $this->get_db_prefix();
		
			$sql 	= "SELECT `value` 
					   FROM `" . $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE . "` 
					   WHERE `transient` = '{$transient}' 
					   AND `expiration` >= CURRENT_TIMESTAMP;";
		
			$var = $db->getVar( $sql );
			if ( !empty( $var ) ) {
				return $this->maybe_unserialize( $var->value );
			}
			
			return false;
		}
		
		/**
		 * Deletes an existing transient.
		 *
		 * @param   string 		$transient
		 * @return  mixed|bool
		 */
		public function delete_transient( $transient ) {
		
			$db 	= $this->get_db();
			$prefix = $this->get_db_prefix();
		
			$sql 	= "DELETE 
					   FROM `" . $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE . "` 
					   WHERE `transient` = '{$transient}';";
		
			return $db->query( $sql );
		}
		
		/**
		 * Deletes expired transients from database.
		 *
		 * @return  bool
		 */
		public function cleanup_msltns_transients() {
			
			$db 	= $this->get_db();
			$prefix = $this->get_db_prefix();
			
            if ( $db->tableExists( $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE ) === $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE ) {
    			$sql = "DELETE
    					FROM `" . $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE . "` 
    					WHERE `expiration` < CURRENT_TIMESTAMP;";
			
    			return $db->query( $sql );
            }
			
            return true;
		}
		
		/**
		 * Sets up database for using msltns transients.
		 *
		 * @return  bool
		 */
		public function setup_db_for_msltns_transients() {
		
			$db 	= $this->get_db();
			$prefix = $this->get_db_prefix();
			if ( $db->tableExists( $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE ) !== $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE ) {
		
		        $sql = "CREATE TABLE `" . $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE . "` (
						  `transient` varchar(191) NOT NULL DEFAULT '',
						  `value` longtext NOT NULL,
						  `expiration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						  PRIMARY KEY (`transient`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
				return $db->query( $sql );
			}
			
			return true;
		}
		
		/**
		 * Tears down database from transient usage.
		 *
		 * @return  bool
		 */
		public function teardown_db_for_msltns_transients() {
			
			$db 	= $this->get_db();
			$prefix = $this->get_db_prefix();
			
			$sql 	= "DROP TABLE IF EXISTS `" . $prefix . MSLTNS_UTILS_TRANSIENTS_TABLE . "`;";
			return $db->query( $sql );
		}
		
		/**
		 * Checks whether running in a WordPress environment.
		 *
		 * @return  bool
		 */
		public function is_wp_env() {
			return defined( 'ABSPATH' );
		}
		
		/**********************************************
		 * 
		 * Request handling
		 * 
		 **********************************************/
		
		/**
		 * Sends a GET request.
		 *
		 * @return  string
		 */
		public function get( $url, $params = [], $raw_post_fields = false, $headers = [] ) {
			if ( count( $params ) > 0 ) {
				$url = "$url?" . http_build_query( $params );
			}
			return $this->_request( $url, 'GET', [], $raw_post_fields, $headers );
		}
		
		/**
		 * Sends a POST request.
		 *
		 * @return  string
		 */
		public function post( $url, $params = [], $raw_post_fields = false, $headers = [] ) {
			return $this->_request( $url, 'POST', $params, $raw_post_fields, $headers );
		}
		
		/**
		 * Sends a PUT request.
		 *
		 * @return  string
		 */
		public function put( $url, $params = [], $raw_post_fields = false, $headers = [] ) {
			return $this->_request( $url, 'PUT', $params, $raw_post_fields, $headers );			
		}
        
		/**
		 * Sends a PATCH request.
		 *
		 * @return  string
		 */
		public function patch( $url, $params = [], $raw_post_fields = false, $headers = [] ) {
			return $this->_request( $url, 'PATCH', $params, $raw_post_fields, $headers );			
		}
		
		/**
		 * Sends a DELETE request.
		 *
		 * @return  string
		 */
		public function delete( $url, $raw_post_fields = false, $headers = [] ) {
			return $this->_request( $url, 'DELETE', [], $raw_post_fields, $headers );
		}
		
		/**
		 * @ignore
		 * 
		 * Sends a curl request.
		 *
		 * @return  string
		 */
		private function _request( $url, $type = 'GET', $data = [], $raw_post_fields = false, $headers = [] ) {
		    
			$host = parse_url( $url, PHP_URL_HOST );
			$curl = curl_init();
		    
			$opts = array(
				CURLOPT_URL 			=> $url,
				CURLOPT_RETURNTRANSFER 	=> true,
				CURLOPT_ENCODING 		=> "",
				CURLOPT_MAXREDIRS 		=> 10,
				CURLOPT_TIMEOUT 		=> 0,
				CURLOPT_FOLLOWLOCATION 	=> false,
				CURLOPT_HTTP_VERSION 	=> CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST 	=> $type, // GET | POST | PUT | PATCH | DELETE
				CURLOPT_SSL_VERIFYHOST	=> false,
				CURLOPT_SSL_VERIFYPEER	=> false,
			);
		
			if ( in_array( $type, [ 'POST', 'PUT', 'PATCH', 'DELETE' ] ) ) {
                // e.g. $raw_post_fields = json_encode( $any_data_array );
                $opts[CURLOPT_POSTFIELDS] = $raw_post_fields ? $data : http_build_query( $data );
			}
            
    		$default_headers = [
				"User-Agent" 		=> "Utils/0.1",
			    "Accept"	 		=> "application/json, text/plain, */*",
				"Cache-Control" 	=> "no-cache",
				"Referer" 			=> "",
				"Accept-Language" 	=> "de,en-US;q=0.7,en;q=0.3",
			    "Accept-Encoding" 	=> "gzip, deflate",
			    "Connection" 		=> "keep-alive"
    		];
        	$opts[CURLOPT_HTTPHEADER] = $this->get_headers( $default_headers, $headers );
		    
			curl_setopt_array( $curl, $opts );
		    
			$response = curl_exec( $curl );
			$httpcode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
			$error	  = curl_error( $curl );
            
			curl_close( $curl );
		    
			if ( ! empty( $error ) ) {
				$this->log( "{$error} (Code: {$httpcode}, url: {$url})", "error" );
			}
			
			return $response;
	    }
        
    	private function get_headers( $defaults, $atts = [] ) {
		
    		// sets default header values if not given
    		$headers = $addheaders = [];
    	    foreach ( $defaults as $name => $default ) {
    	        if ( array_key_exists( $name, $atts ) ) {
    	            $headers[] = $name . ": " . $atts[ $name ];
    	        } else {
    	            $headers[] = $name . ": " . $default;
    	        }
    	    }
    	    foreach ( $atts as $name => $att ) {
    			$header = $name . ": " . $atts[ $name ];
    			if ( !in_array( $header, $headers ) ) {
    				$headers[] = $header;
    			}
    	    }
            
    		return $headers;
    	}
		
	}

}

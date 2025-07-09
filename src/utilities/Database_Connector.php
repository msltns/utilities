<?php

namespace msltns\utilities;

use Mysqli;

/**
 * This class connects to a database and runs queries on it. It comes
 * with all traditional functions known by databases.
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
if ( ! class_exists( '\msltns\utilities\Database_Connector' ) ) {
	
	class Database_Connector {
	
		private $db = false;
		private $debug = false;
	
		private static $instance;
	    private $connection;
		
		/**
		 * Main constructor.
		 *
		 * @return void
		 */
		private function _construct() {

		}
		
		/**
		 * Main destructor.
		 *
		 * @return void
		 */
		function _destruct() {
			// self::log("terminate Database_Connector...", "info");
			self::closeConnection();
		}
		
		/**
		 * Singleton instance.
		 * 
		 * @return \Database_Connector
		 */
		public static function getInstance() {
			// echo "Database_Connector...";
	        if (self::$instance == null) {
	            $className = __CLASS__;
	            self::$instance = new $className();
	        }
	        return self::$instance;
	    }
		
		/**
		 * Singleton instance.
		 * 
		 * @return \Database_Connector
		 */
		public static function get_instance() {
			return self::getInstance();
	    }

	    public static function initializeConnection($debug = false) {
	        $instance = self::getInstance();
	        // self::log("starting Database_Connector...", "info");
			$instance->debug = $debug;
			if ($instance->connection == null) {
				if ( defined('DB_HOST') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_NAME') ) {
					$instance->db = DB_NAME;
					$instance->connection = new Mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
				}
			}
	    }

	    public static function getDb() {
			return self::getInstance();
	    }
	
		/**
		 * This function returns a connection to the database.
		 */
		protected static function getConnection() {
			$instance = self::getInstance();
	        return $instance->connection;
		}
	
		/**
		 * This function closes the connection to the database.
		 */
		private static function closeConnection() {
			$instance = self::getInstance();
			if ($instance->connection !== null) {
				$instance->connection->close();
			}
		}
	
		/**
		 * This function returns all table names within the connected database
		 */
		public static function getTables() {
			$instance = self::getInstance();
			$tables = [];
			$tabstr = "";
			$stmt = 'SHOW TABLES;';
			$results = self::executeStmt($stmt);
			while ($table = $results->fetch_array()) {
			    $tab = $table["Tables_in_{$instance->db}"];
				array_push($tables, $tab);
				$tabstr .= $tab . ", ";
			}
			$tabstr = rtrim($tabstr, ", ");
			// self::log("found " . count($tables) . " tables (" . $tabstr . ")", "info");
			return $tables;
		}
	
		/**
		 * Checks whether a certain table exists in the connected database
		 */
		public static function tableExists( $table_name ) {
			$instance 	= self::getInstance();
			$tables 	= [];
			$stmt 		= "SHOW TABLES LIKE '{$table_name}';";
			$results 	= self::executeStmt($stmt);
			while ( $table = $results->fetch_array() ) {
				if ( isset( $table["Tables_in_{$instance->db}"] ) ) {
					$tables[] = $table["Tables_in_{$instance->db}"];
				} else if ( isset( $table["Tables_in_{$instance->db} ({$table_name})"] ) ) {
					$tables[] = $table["Tables_in_{$instance->db} ({$table_name})"];
				} else if ( isset( $table["0"] ) && $table["0"] === $table_name ) {
					$tables[] = $table["0"];
				}
			}
			$tabstr = implode( ", ", $tables );
			return count( $tables ) > 0 ? $tables[0] : false;
		}
	
		/**
		 * This function returns all columns and datatypes of a given table.
		 *
		 * @param table the table to be listed
		 *
		 */
		public static function getColumns($table) {
			$columns = array();
			$colstr = "";
			$stmt = 'DESC `' . $table . '`';
			$results = self::executeStmt($stmt);
			while ($column = $results->fetch_array()) {
				$field = $column["Field"];
				$dtype = $column["Type"];
				array_push($columns, array("field" => $field, "dtype" => $dtype));
				$colstr .= $field . ", ";
			}
			$colstr = rtrim($colstr, ", ");
			self::log("found " . count($columns) . " columns in table $table (" . $colstr . ")", "info");
			return $columns;
		}
	
		/**
		 * This function empties a given table.
		 *
		 * @param table the table to empty
		 *
		 */
		public static function emptyTable($table) {
			$stmt = 'TRUNCATE TABLE ' . $table;
			return self::executeStmt($stmt);
		}
	
		/**
		 * This function deletes a given table.
		 *
		 * @param table the table to be dropped
		 *
		 */
		public static function dropTable($table) {
			try {
				$stmt = "DROP TABLE IF EXISTS `" . $table . "`;";
				return self::executeStmt($stmt);
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
		}
	
		/**
		 * This function inserts values into a given table.
		 *
		 * @param table the table to empty
		 * @param row an array of key value pairs to insert
		 *
		 */
		public static function insertRow($table, $row) {
			try {
				$keys =  array();
				$values = array();
				foreach ($row as $key => $val) {
					array_push($keys, $key);
					array_push($values, $val);
				}
				$stmt = 'INSERT INTO `' . $table . '` ('; 
				foreach ($keys as $key) {
					$stmt .= "`" . $key . "`,";
				}
				$stmt = rtrim($stmt, ",");
				$stmt .= ' ) VALUES (';
				foreach ($values as $val) {
					$stmt .= "'" . $val . "',";
				}
				$stmt = rtrim($stmt, ",");
				$stmt .= ');';
				return self::executeStmt($stmt);
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
		}
	
		public static function escapeString($str) {
			try {
				$con 	= self::getConnection();
				$result = $con->real_escape_string($str);
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
			return $result;
		}
	
		public static function query($query) {
			return self::executeQuery($query);
		}
	
		/**
		 * This function runs a given database statment.
		 *
		 * NOTE: this highly critical if you do not know what you are doing here!!!
		 * Wrong statement may cause critical errors and can destroy the database!
		 *
		 * @param query the statement to be executed
		 *
		 */
		public static function executeQuery($query) { 
			try {
				if (self::isValidStmt($query)) {
					return self::executeStmt($query);
				} else {
					throw new \Exception("Query $query is not permitted!");
				}
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
		}
	
		public static function fetchArray($dbres) {
			return $dbres->fetch_array();
		}
	
		/**
		 * This function returns the value of a given database statement.
		 * 
		 * @param stmt the query
		 *
		 */
		public static function getVar($stmt) {
			try {
				$result = self::executeStmt($stmt);
				if ($result) {
					$res = $result->fetch_object();
					// self::log( json_encode( $res ) );
					// Frees the memory associated with a result
					$result->free();
					return $res;
				}
				return false;
			
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
		}
	
		/**
		 * This function returns the values of a given table selected by certain criteria.
		 * 
		 * @param table the table to empty
		 * @param rows an array of key value pairs that specifies the rows to select
		 *
		 */
		public static function selectRow($table, $criteria) {
			try {
				$stmt = 'SELECT * FROM `' . $table . '` WHERE ';
				$i = 0;
				foreach ($criteria as $key => $val) {
					if ($i == 0) {
						$stmt .= "`" . $key . "` = '" . $val . "'";
					} else {
						$stmt .= " AND `" . $key . "` = '" . $val . "'";
					}
					$i++;
				}
				$stmt .= ';';
				$result = self::executeStmt($stmt);
				if ($result) {
					$res = $result->fetch_object();
					// Frees the memory associated with a result
					$result->free();
					return $res;
				}
				return false;
			
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
		}
	
		/**
		 * This function returns the values of a given table selected by certain criteria.
		 * 
		 * @param table the table to empty
		 * @param rows an array of key value pairs that specifies the rows to select
		 *
		 */
		public static function selectRows($table, $rows) {
			try {
				$stmt = 'SELECT * FROM `' . $table . '` WHERE ';
				$count = count($rows);
				$i = 0;
				foreach ($rows as $key => $val) {
					if ($i == 0) {
						$stmt .= "`" . $key . "` = '" . $val . "'";
					} else {
						$stmt .= " AND `" . $key . "` = '" . $val . "'";
					}
					$i++;
				}
				$stmt .= ';';
				$results = self::executeStmt($stmt);
			
				$res = array();
				while($row = $results->fetch_array()) {
				    array_push($res, $row);
				}
				// Frees the memory associated with a result
				$results->free();
				return $res;
			
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
		}
	
		/**
		 * This function updates values in a row of a given table.
		 *
		 * @param table the table to empty
		 * @param row the table row to update (array of col-val pairs)
		 * @param values an array of key value pairs to update
		 *
		 */
		public static function updateRow($table, $row, $values) {
			try {
				$stmt = 'UPDATE `' . $table . '` SET '; 
				foreach ($values as $key => $val) {
					$stmt .= "`" . $key . "` = '" . $val . "',";
				}
				$stmt = rtrim($stmt, ",");
				$stmt .= ' WHERE ';
				$count = count($row);
				$i = 0;
				foreach ($row as $key => $val) {
					if ($i == 0) {
						$stmt .= "`" . $key . "` = '" . $val . "'";
					} else {
						$stmt .= " AND `" . $key . "` = '" . $val . "'";
					}
					$i++;
				}
				$stmt .= ';';
				return self::executeStmt($stmt);
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
		}
	
		/**
		 * This function deletes values from a given table.
		 *
		 * @param table the table to empty
		 * @param row an array of key value pairs that specifies the row to delete
		 *
		 */
		public static function deleteRow($table, $row) {
			try {
				$stmt = 'DELETE FROM `' . $table . '` WHERE ';
				$count = count($row);
				$i = 0;
				foreach ($row as $key => $val) {
					if ($i == 0) {
						$stmt .= "`" . $key . "` = '" . $val . "'";
					} else {
						$stmt .= " AND `" . $key . "` = '" . $val . "'";
					}
					$i++;
				}
				$stmt .= ';';
				return self::executeStmt($stmt);
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
		}
	
		/**
		 * This function deletes values from a given table.
		 *
		 * @param table the table to empty
		 * @param rows an array of rows to delete
		 *
		 */
		public static function deleteRows($table, $rows) {
			try {
				foreach ($rows as $row) {
					$success = self::deleteRow();
				}
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
			return $success;
		}
	
		/**
		 * This function executes a given statement.
		 *
		 * @param stmt the statement to be executed
		 *
		 */
		private static function executeStmt( $stmt ) {
			try {
				$con = self::getConnection();
				// self::log("executing statement '" . $stmt . "'", "info");
				$result = $con->query($stmt, MYSQLI_STORE_RESULT);
				if (!$result){
				    self::log($con->error . " (".$con->errno.") in " . $stmt, "error");
					return false;
				}
				return $result;
			} catch (\Exception $e) {
				self::log($e->getMessage(), "error");
			}
			return false;
		}
	
		private static function isValidStmt($stmt) {
			if (self::startsWith($stmt, 'CREATE TABLE') 
				|| self::startsWith($stmt, 'INSERT')
				|| self::startsWith($stmt, 'SELECT')
				|| self::startsWith($stmt, 'ALTER')
				|| self::startsWith($stmt, 'DELETE')
				|| self::startsWith($stmt, 'UPDATE')) {
				return true;
			}
			return false;
		}
	
		private static function startsWith( $haystack, $needle ) {
		     return trim( $needle ) === '' || strpos( trim( $haystack ), trim( $needle ) ) === 0;
		}

		private static function endsWith( $haystack, $needle ) {
		    $length = strlen( $needle );
		    if ( $length == 0 ) {
		        return true;
		    }
		    return ( substr( $haystack, -$length ) === $needle );
		}
	
		private static function log( $msg, $level = 'info' ) {
			$instance = self::getInstance();
			if ( $instance->debug && class_exists( '\msltns\logging\Logger' ) )  {
				\msltns\logging\Logger::getInstance()->log( $msg, $level );
			}
		}
	}
}

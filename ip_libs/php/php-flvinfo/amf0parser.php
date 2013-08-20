<?php  
/**
 * AMF0 Parser
 * 
 * Based (pretty far) on the AMFPHP serializer. I actually started using the AMFPHP serializer and
 * mostly rewrote it.
 *
 * @author		Tommy Lacroix <lacroix.tommy@gmail.com>
 * @version 	1.3.20090611 $Id: amf0parser.php 4 2009-06-11 12:36:15Z lacroix.tommy $
 * @copyright   Copyright (c) 2006-2009 Tommy Lacroix
 * @license		LGPL version 3, http://www.gnu.org/licenses/lgpl.html
 * @package 	php-flvinfo
 * @link 		$HeadURL: https://php-flvinfo.googlecode.com/svn/trunk/amf0parser.php $
 */

// ---

/**
 * AMF0 Parser class
 * 
 * @author		Tommy Lacroix <lacroix.tommy@gmail.com>
 * @todo 		Finish Date parsing
 */
class AMF0Parser {
	// Type constants
	const TYPE_NUMBER = 0x00;
	const TYPE_BOOLEAN = 0x01;
	const TYPE_STRING = 0x02;
	const TYPE_OBJECT = 0x03;
	const TYPE_MOVIECLIP = 0x04;
	const TYPE_NULL = 0x05;
	const TYPE_UNDEFINED = 0x06;
	const TYPE_REFERENCE = 0x07;
	const TYPE_MIXEDARRAY = 0x08;
	const TYPE_OBJECT_TERM = 0x09;
	const TYPE_ARRAY = 0x0a;
	const TYPE_DATE = 0x0b;
	const TYPE_LONGSTRING = 0x0c;
	const TYPE_RECORDSET = 0x0e;
	const TYPE_XML = 0x0f;
	const TYPE_TYPED_OBJECT = 0x10;
	const TYPE_AMF3 = 0x11;
	// }}} Type constants
	
	/**
	 * Endianess, 0x00 for big, 0x01 for little
	 *
	 * @access private
	 * @var int
	 */
	private $endian;
	
	/**
	 * AMF0 Data
	 *
	 * @access private
	 * @var string
	 */
	private $data;
	
	/**
	 * Index in data
	 *
	 * @access private
	 * @var int
	 */
	private $index;
	
	/**
	 * Data length
	 *
	 * @access private
	 * @var int
	 */
	private $dataLength;
	
	/**
	 * Constructor
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	string		$str		Optional initialization string
	 * @return 	AMF0Parser
	 */
	public function AMF0Parser($str = false) {	
		/**
		 * Proceed to endianess detection. This will be needed by
		 * double decoding because unpack doesn't allow the selection
		 * of endianess when decoding doubles.
		 */
		
		// Pack 1 in machine order
		$tmp = pack("L", 1);
		
		// Unpack it in big endian order
		$tmp2 = unpack("None",$tmp);
		
		// Check if it's still one
		if ($tmp2['one'] == 1) $this->endian = 0x00; // Yes, big endian
			else $this->endian = 0x01;	// No, little endian
			
		// Initialize if needed
		if ($str !== false) {
			$this->initialize($str);
		}
	} // Constructor
	
	/**
	 * Initialize data
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	string		$str	AMF0 Data
	 */
	public function initialize($str) {
		$this->data = $str;
		$this->dataLength = strlen($str);
		$this->index = 0;
	} // initialize function
	
	
	/**
	 * Read all packets
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	string		$str	AMF0 data (optional, uses the initialized one if not given)
	 * @return 	array
	 */
	public function readAllPackets($str = false) {
		// Initialize if needed
		if ($str !== false) $this->initialize($str);
		
		// Parse each packet
		$ret = array();
		while ($this->index < $this->dataLength)
			$ret[] = $this->readPacket();
			
		// Return it
		return $ret;
	} // readAllPackets function
	
	/**
	 * Read a packet at current index
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	mixed
	 */
	public function readPacket() {	
		// Get data code
		$dataType = ord($this->data[$this->index++]);
		// Interpret
		switch($dataType) {	
			case self::TYPE_NUMBER:		// Number 0x00
				return $this->readNumber();
			case self::TYPE_BOOLEAN: 	// Boolean 0x01
				return $this->readBoolean();
			case self::TYPE_STRING:		// String 0x02
				return $this->readString();
			case self::TYPE_OBJECT:		// Object 0x03
				return $this->readObject();
			case self::TYPE_MOVIECLIP:		// MovieClip
				throw new Exception("Unhandled AMF type: MovieClip (04)");
				break;
			case self::TYPE_NULL:		// NULL 0x05
				return NULL;
			case self::TYPE_UNDEFINED:		// Undefined 0x06
				return 'undefined';
			case self::TYPE_REFERENCE:		// Reference
				throw new Exception("Unhandled AMF type: Reference (07)");
				break;
			case self::TYPE_MIXEDARRAY : 	// Mixed array 0x08
				return $this->readMixedArray();
			case self::TYPE_OBJECT_TERM: 	// ObjectTerm
				throw new Exception("Unhandled AMF type: ObjectTerm (09) -- should only happen in the getObject function");
				break;
			case self::TYPE_ARRAY:	// Array 0x0a
				return $this->readArray();
			case self::TYPE_DATE: 	// Date
				return $this->readDate();
			case self::TYPE_LONGSTRING: 	// LongString
				return $this->readLongString();
			case TYPE_RECORDSET: 	// Recordset
				throw new Exception("Unhandled AMF type: Unsupported (0E)");
				break;
			case self::TYPE_XML: 	// XML
				return $this->readLongString();
			case self::TYPE_TYPED_OBJECT: 	// Typed Object
				return $this->readTypedObject();
			case TYPE_AMF3: 	// AMF3
				throw new Exception("Unhandled AMF type: AMF3 (11)");
				break;
			default:
				throw new Exception("Unhandled AMF type: unknown (0x".dechex($dataType).") at offset ".$this->index-1);
		}
	} // readPacket function
	
	/**
	 * Read a string at current index
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	string
	 */
	public function readString() {
		// Check if we have enough data
		if (strlen($this->data) < $this->index+2) {
			return null;
		}
		
		// Get length
		$len = unpack('nlen', substr($this->data,$this->index,2));
		$this->index+=2;
		
		// Get string
		$val = substr($this->data, $this->index, $len['len']);
		$this->index += $len['len'];
		
		// Return it
		return $val;
	} // readString function
	
	/**
	 * Read a long string at current index
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	string
	 */
	public function readLongString() {
		// Check if we have enough data
		if (strlen($this->data) < $this->index+4) {
			return null;
		}

		// Get length
		$len = unpack('Nlen', substr($this->data,$this->index,4));
		$this->index+=4;
		
		// Get string
		$val = substr($this->data, $this->index, $len['len']);
		$this->index += $len['len'];
		
		// Return it
		return $val;
	} // readLongString function
	
	/**
	 * Read a number (double) at current index
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	double
	 */
	public function readNumber() {	
		// Check if we have enough data
		if (strlen($this->data) < $this->index+8) {
			return null;
		}

		// Get the packet, big endian (8 bytes long)
		$packed = substr($this->data, $this->index, 8);
		$this->index += 8;
		
		// Reverse it if we're little endian
		if ($this->endian == 0x01) $packed = strrev($packed);

		// Unpack it
		$tmp = unpack("dnumber", $packed);
		
		// Return it
		return $tmp['number'];
	} // readNumber function
	
	/**
	 * Read a boolean at current index
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	boolean
	 */
	public function readBoolean() {
		return ord($this->data[$this->index++]) == 1;
	} // readBoolean function
	
	/**
	 * Read an object at current index
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	stdClass
	 */
	public function readObject() {	
		// Create return object we will add data to
		$ret = new stdClass();
		
		do {
			// Get key
			$key = $this->readString();
			
			// Stop if no key was read (end of AMF0 stream)
			if ($key === null) break;
			
			// Check if we reached ObjectTerm (09)
			$dataType = ord($this->data[$this->index]);
			
			// If it's not an Object Term, read the packet
			if ($dataType != self::TYPE_OBJECT_TERM) {
				// Get data
				$val = $this->readPacket();
				
				// Store it
				$ret->key = $val;
			}
		} while ($dataType != 0x09);

		// Skip the Object Term
		$this->index += 1; 
		
		// Return object
		return $ret;
	} // readObject function
	
	/**
	 * Read a typed object at current index
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	stdClass
	 */
	public function readTypedObject() {
		// Get class name
		$className = $this->readString();
		
		// Get object
		$object = $this->readObject();
		
		// Save class name inside object
		$object->__className = $className;
		
		// Return object
		return $object;
	} // readTypedObject function

	/**
	 * Read a mixed array at current position
	 * 
	 * Note: A mixed array is basically an object, but with a long integer describing its highest index at first.
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	array
	 */
	public function readMixedArray() {	
		// Skip the index
		$this->index += 4;
		
		// Parse the object, but return it as an array
		return get_object_vars($this->readObject());
	} // readMixedArray function
	
	/**
	 * Get an indexed array ([0,1,2,3,4,...])
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	array
	 */
	public function readArray() {	
		// Check if we have enough data
		if (strlen($this->data) < $this->index+4) {
			return null;
		}

		// Get item count
		$len = unpack('Nlen',substr($this->data,$this->index,4));
		$this->index+=4;
		
		// Get each packet
		$ret = array();
		for($i=0;$i<$len['len'];$i++) $ret[] = $this->readPacket();
		
		// Return the array
		return $ret;
	} // readArray function

	/**
	 * Read a date at current index
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	string
	 */
	public function readDate() {	
		// Check if we have enough data
		if (strlen($this->data) < $this->index+10) {
			return null;
		}

		// Get the packet, big endian (8 bytes long)
		$packed = substr($this->data, $this->index, 8);
		$this->index += 8;
		
		// Reverse it if we're little endian
		if ($this->endian == 0x01) $packed = strrev($packed);

		// Unpack it
		$tmp = unpack("dnumber", $packed);
		$epoch = $tmp['number']/1000;

		// Get timezone
		$tmp = unpack('nnumber', substr($this->data, $this->index, 2));
		$this->index += 2;
		$timezone = $tmp['number'];
		if ($timezone > 32767) {
			$timezone = $timezone-65536;
		}

		// Make epoch GMT, and then convert to local time
		$time = $epoch;
		$time += $timezone*60;	// Timezone is in seconds
		$time += date('Z',$time);
		
		// Return it
		return date('r',$time);
	} // readDate function
	
	
	/**
	 * Write a packet
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	mixed	$value
	 * @param 	int		$type		autodetected if none given
	 * @return 	string
	 */
	public function writePacket($value, $type=false) {	
		if ($type === false) {
			if (($value === true) || ($value === false)) $type = self::TYPE_BOOLEAN;
			if (is_numeric($value)) {
				$type = self::TYPE_NUMBER;
			} else if (is_array($value)) {
				$type = self::TYPE_ARRAY;
				foreach (array_keys($value) as $k) {
					if (preg_match(',[^0-9],',$k)) {
						$type = self::TYPE_MIXEDARRAY;
						break;
					}
				}
				// Test for mixed/indexed
			} else if (is_object($value)) {
				$type = self::TYPE_OBJECT;
			} else if (is_string($value)) {
				if (strlen($value) < 65535) {
					$type = self::TYPE_STRING;
				} else {
					$type = self::TYPE_LONGSTRING;
				}
			} else if (is_null($value)) {
				$type = self::TYPE_NULL;
			}
		} // if($type===false)
		
		switch ($type) {
			case self::TYPE_NUMBER:
				return $this->writeNumber($value);
			case self::TYPE_STRING:
				return $this->writeString($value);
			case self::TYPE_LONGSTRING:
				return $this->writeLongString($value);
			case self::TYPE_NULL:
				return $this->writeNull();
			case self::TYPE_BOOLEAN:
				return $this->writeBoolean($value);
			case self::TYPE_ARRAY:
				return $this->writeArray($value);
			case self::TYPE_MIXEDARRAY:
				return $this->writeMixedArray($value);
			case self::TYPE_OBJECT:
				return $this->writeObject($value);
			default:
				throw new Exception('Unhandled AMF0 type: 0x'.dechex($type));
		} //switch($type)
	} // writePacket function
	
	/**
	 * Write a string
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	string $str
	 * @return 	string
	 */
	public function writeString($str) {
		// Write type
		$value = chr(self::TYPE_STRING);
		
		// Write length
		$value .= pack('n', strlen($str));
		
		// Write string
		$value .= $str;
		
		// Return it
		return $value;
	}		
	
	/**
	 * Write a long string
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	string $str
	 * @return 	string
	 */
	public function writeLongString($str) {
		// Write type
		$value = chr(self::TYPE_LONGSTRING);
		
		// Write length
		$value .= pack('N', strlen($str));
		
		// Write string
		$value .= $str;
		
		// Return it
		return $value;
	} // writeLongString function
	
	/**
	 * Write a XML
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	string $str
	 * @return 	string
	 */
	public function writeXML($str) {
		// Write type
		$value = chr(self::TYPE_XML);
		
		// Write length
		$value .= pack('N', strlen($str));
		
		// Write string
		$value .= $str;
		
		// Return it
		return $value;
	} // writeXML function
	
	/**
	 * Write a number
	 *
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	integer $number
	 * @return 	string
	 */
	public function writeNumber($number) {
		// Write type
		$value = chr(self::TYPE_NUMBER);
		
		// Build packed
		$packed = pack('d', $number);
		
		// Reverse it if we're little endian
		if ($this->endian == 0x01) $packed = strrev($packed);
		
		// Write packed
		$value .= $packed;
		
		// Return it
		return $value;
	} // writeNumber function
	
	
	/**
	 * Write a null
	 * 
	 * @author 	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	string
	 */
	public function writeNull() {
		// Write type
		$value = chr(self::TYPE_NULL);

		// Return it
		return $value;
	} // writeNull function
	
	/**
	 * Write a boolean
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	bool	$boolean
	 * @return 	string
	 */
	public function writeBoolean($boolean) {
		// Write type
		$value = chr(self::TYPE_BOOLEAN);
		
		// Write value
		$value .= ($boolean ? chr(1) : chr(0));
		
		// Return it
		return $value;
	} // writeBoolean function
	
	/**
	 * Write a mixed array
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	array	$array
	 * @return 	string
	 */
	public function writeMixedArray($array) {
		// Write type
		$value = chr(self::TYPE_MIXEDARRAY);
		
		// Write index
		$value .= pack('N',count($array));
		
		// Write as object
		$value .= $this->writeObjectSub($array);
		
		// Return it
		return $value;
	} // writeMixedArray

	/**
	 * Write an object
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	stdClass|array	$object
	 * @return 	string
	 */
	public function writeObject($object) {
		// Write type
		$value = chr(self::TYPE_OBJECT);
		
		// Write as object
		$value .= $this->writeObjectSub(get_object_vars($object));
		
		// Return it
		return $value;
	} // writeObject function
	
	/**
	 * Write a typed object
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	stdClass|array	$object
	 * @return 	string
	 */
	public function writeTypedObject($object, $className = false) {
		// Write type
		$value = chr(self::TYPE_TYPED_OBJECT);
		
		// Write class
		if ($className === false) {
			if (isset($object->__className)) $className = $object->__className;
				else $className = get_class($object);
		}
		$value .= $this->writeString($className);
		
		// Write as object
		$value .= $this->writeObjectSub(get_object_vars($object));
		
		// Return it
		return $value;
	} // writeTypedObject function
	
	/**
	 * Write an object, without the leading type
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	private
	 * @internal
	 * @param 	stdClass|array	$object
	 * @return 	string
	 */
	private function writeObjectSub($object) {
		$output = '';
		
		// Write each element
		foreach ($object as $key=>$value) {
			// Write key
			$output .= pack('n',strlen($key)).$key;
			
			// Write value
			$output .= $this->writePacket($value);
		}
		
		// Write object term
		$output .= pack('n',0);
		$output .= chr(0x09);
		
		// Return it
		return $output;
	} // writeObjectSub function
	
	/**
	 * Write an index array
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	array	$array
	 * @return 	string
	 */
	public function writeArray($array) {
		// Write type
		$value = chr(self::TYPE_ARRAY);
		
		// Write length
		$value .= pack('N', count($array));
		
		// Write elements
		foreach ($array as $value) {
			$value .= $this->writePacket($value);
		}
		
		// Return it
		return $value;
	} // writeArray function
	
} // AMF0Parser class

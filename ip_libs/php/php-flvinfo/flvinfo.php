<?php 
/**
 * PHP-FLVInfo
 * 
 * Reads and writes FLV meta data from a Flash Video File.
 * 
 * @author		Tommy Lacroix <lacroix.tommy@gmail.com>
 * @version 	1.3.20090611
 * @copyright   Copyright (c) 2006-2009 Tommy Lacroix
 * @license		LGPL version 3, http://www.gnu.org/licenses/lgpl.html
 * @package 	php-flvinfo
 * @uses 		AMF0Parser
 * @link 		$HeadURL: https://php-flvinfo.googlecode.com/svn/trunk/flvinfo.php $
 */

// ---

// Dependencies
require_once 'amf0parser.php';
//

// ---

/**
 * FLVInfo2 class
 * 
 * @author		Tommy Lacroix <lacroix.tommy@gmail.com>
 */
class Flvinfo {
	//  Audio codec types
	const FLV_AUDIO_CODEC_UNCOMPRESSED = 0x00;
	const FLV_AUDIO_CODEC_ADPCM = 0x01;
	const FLV_AUDIO_CODEC_MP3 = 0x02;
	const FLV_AUDIO_CODEC_NELLYMOSER_8K = 0x05;
	const FLV_AUDIO_CODEC_NELLYMOSER = 0x06;
	//
	
	//  Video codec types
	const FLV_VIDEO_CODEC_SORENSON_H263 = 0x02;
	const FLV_VIDEO_CODEC_SORENSON = 0x03;
	const FLV_VIDEO_CODEC_ON2_VP6 = 0x04;
	const FLV_VIDEO_CODEC_ON2_VP6ALPHA = 0x05;
	const FLV_VIDEO_CODEC_SCREENVIDEO_2 = 0x06;
	//
	
	/**
	 * Constructor
	 * 
	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @return 	FLVInfo2
	 */
	public function __construct() {
	} // Constructor
	
	/**
	 * Get information about the FLV file
	 * 
 	 * @author	Tommy Lacroix <lacroix.tommy@gmail.com>
 	 * @access 	public
	 * 
	 * <code>
	 * $flvinfo = new FLVInfo2();
	 * $info = $flvinfo->getInfo('demo.flv',true);
	 * var_export($info);
	 * </code>
	 * 
	 * Demo output:
	 * 
	 * <code>
	 * 	(
	 * 		[signature] => 1
	 *	    [hasVideo] => 1
	 *	    [hasAudio] => 1
	 *	    [minimalFlashVersion] => 8
	 *	    [duration] => 32.4
	 *	    [video] => stdClass Object (
	 *	            [codec] => 4
	 *	            [width] => 480
	 *	            [height] => 360
	 *	            [bitrate] => 896
	 *	            [fps] => 15
	 *	            [codecStr] => On2 VP6
	 *	        )
	 *	
	 *	    [audio] => stdClass Object (
	 *	            [codec] => 2
	 *	            [frequency] => 44
	 *	            [depth] => 16
	 *	            [channels] => 2
	 *	            [bitrate] => 128
	 *	            [codecStr] => MP3
	 *	        )
	 *
	 *	)
	 * </code>
	 * 
	 * @param	string      $filename
	 * @param   bool		$extended	Get extended information about the FLV file
	 * @return  stdClass
	 */
	public function getInfo($filename, $extended=false, $useMeta=true) {
		// Initialize info class where we'll collect preliminary data
		$info = new stdClass();
		
		// No tag found yet
		$gotVideo = $gotAudio = false;
		$tagParsed = 0;
		
		// Frames types
		$vFrames = array();
		$aFrames = array();
		
		// Max tags
		$maxTags = $extended ? false : 20;
		// Open file
		$f = fopen($filename,'rb');

		// Read header
		$buf = fread($f, 9);
		$header = unpack('C3signature/Cversion/Cflags/Noffset', $buf);
		
		// Check signature
		$info->signature = (($header['signature1'] == 70) && ($header['signature2'] == 76) && ($header['signature3'] == 86));
		
		// If signature is valid, go on
		if ($info->signature) {
			// Version
			$info->version = $header['version'];
			
			// Content
			$info->hasVideo = ($header['flags'] & 1) != 0;
			$info->hasAudio = ($header['flags'] & 4) != 0;
			$hasMeta = false;
			
			// Read tags
			fseek($f, $header['offset']);
			//$prevTagSize = 0;
			do {
				// Read tag header and check length
				$buf = fread($f, 15);
				if (strlen($buf) < 15) break;
				
				// Interpret header
				$tagInfo = unpack('Nprevsize/C1type/C3size/C4timestamp/C3stream', $buf);
				$tagInfo['size'] = ($tagInfo['size1'] << 16) + ($tagInfo['size2'] << 8) + ($tagInfo['size3']);
				$tagInfo['stream'] = ($tagInfo['stream1'] << 16) + ($tagInfo['stream2'] << 8) + $tagInfo['stream3'];
				$tagInfo['timestamp'] = ($tagInfo['timestamp1'] << 16) + ($tagInfo['timestamp2'] << 8) + $tagInfo['timestamp3'] + ($tagInfo['timestamp4'] << 24);
				
				// Validate previous offset
				//if ($tagInfo['prevsize'] != $prevTagSize) {
					// Do nothing
				//}
				
				// Read tag body
				$nextOffset = ftell($f) + $tagInfo['size'];
				if ($tagInfo['size'] > 0) {
					$body = fread($f, min($tagInfo['size'],16384));
				} else {
					$body = '';
				}
				
				// Seek
				fseek($f, $nextOffset);
				if ($body == '') continue;
				
				// Intepret body
				switch ($tagInfo['type']) {
					case 0x09: 	// Video tag
						// Mark video frame as found
						$gotVideo = true;
						
						// Unpack flags
						$bodyInfo = unpack('Cflags', $body);
						
						// Get codec
						$info->videoCodec = $bodyInfo['flags'] & 15;
						
						// Get frame type and store it
						$frameType = ($bodyInfo['flags'] >> 4) & 15;
						$vFrames[] = array('type'=>$frameType, 'timestamp'=>$tagInfo['timestamp'], 'size'=>$tagInfo['size']);
						
						if (((!isset($frameWidth)) && (!isset($frameHeight))) && ($frameType == 0x01)) {
							switch ($bodyInfo['flags'] & 15) {
								case self::FLV_VIDEO_CODEC_ON2_VP6:
								case self::FLV_VIDEO_CODEC_ON2_VP6ALPHA:
									// This probably isn't right
									// in all cases (VP60, VP61, VP62)
									// http://wiki.multimedia.cx/index.php?title=On2_VP6
									$frameWidth = ord($body[5])*16;
									$frameHeight = ord($body[6])*16;
									break;
								case self::FLV_VIDEO_CODEC_SORENSON_H263:
									$bin = '';
									for ($i=0;$i<16;$i++) {
										$sbin = decbin(ord($body[$i+1]));
										$bin .= str_pad($sbin, 8, '0', STR_PAD_LEFT);
									}
									
									// Start code
									//$startCode = substr($bin,0,17);
									
									// Size type
									$size = bindec(substr($bin,30,3));
									
									// Get width/height
									switch ($size) {
										case 0:		// Custom, 8 bit
											$frameWidth = bindec(substr($bin,33,8));
											$frameHeight = bindec(substr($bin,41,8));
											break;
										case 1:		// Custom, 16 bit
											$frameWidth = bindec(substr($bin,33,16));
											$frameHeight = bindec(substr($bin,49,16));
											break;
										case 2:
											$frameWidth = 352;
											$frameHeight = 288;
											break;
										case 3:
											$frameWidth = 176;
											$frameHeight = 144;
											break;										
										case 4:
											$frameWidth = 128;
											$frameHeight = 96;
											break;
										case 5:
											$frameWidth = 320;
											$frameHeight = 240;
											break;
										case 6:
											$frameWidth = 160;
											$frameHeight = 120;
											break;
									}
									break;
							}
						}
						break;
					case 0x08:		// Audio tag
						// Mark audio frame as found
						$gotAudio = true;
						
						// Unpack flag
						$bodyInfo = unpack('Cflags', $body);
						
						// Get codec
						$info->audioCodec = ($bodyInfo['flags'] >> 4) & 15;
						
						// Get frequency
						$freq = ($bodyInfo['flags'] >> 2) & 3;
						switch ($freq) {
							case 0:	$info->audioFreq =  5; break;
							case 1: $info->audioFreq = 11; break;
							case 2: $info->audioFreq = 22; break;
							case 3: $info->audioFreq = 44; break;
						};
						
						// Get depth (8 or 16 bits)
						$info->audioDepth = (($bodyInfo['flags'] >> 1) & 1) == 1 ? 16 : 8;
						
						// Get channels count
						$info->audioChannels = (($bodyInfo['flags'] & 1) == 1) ? 2 : 1;

						// Get frame type and store it
						$aFrames[] = array('timestamp'=>$tagInfo['timestamp'], 'size'=>$tagInfo['size']);
						break;
					case 0x12:	// Meta tag
						// Skip if already found
						if (((isset($hasMeta)) && ($hasMeta == true)) || ($useMeta == false)) continue;
						
						// Mark meta as found
						$hasMeta = true;
						
						// Initialize parser
						$parser = new AMF0Parser();
						
						// Parse data
						$meta = $parser->readAllPackets($body);
						
						// Save it
						if ((is_array($meta)) && (isset($meta[1])))	$info->meta = $meta;
						break;
				}
				
				// Increase parsed tag count
				$tagParsed++;
			} while (
			/*	(feof($f) == false) && 
				(($gotVideo == false) || ($gotAudio == false) || ($gotMeta == false) && (count($vFrames) < 1000)) && 
				($tagParsed <= $maxTags)*/
				(!feof($f)) && (($maxTags === false) || ($maxTags < $tagParsed))
			);
		} 
		
		// Close file
		fclose($f);
		
		// Return final object
		$ret = new stdClass();

		// Copy root properties
		$ret->signature = $info->signature;
		if(!isset($info->hasVideo))
		  $info->hasVideo = false;
    if(!isset($info->hasAudio))
      $info->hasAudio = false;
		$ret->hasVideo = $info->hasVideo;
		$ret->hasAudio = $info->hasAudio;
		$ret->minimalFlashVersion = 7; 	// Default value, changed later by video codec
		if ((isset($info->meta[1])) && (isset($info->meta[1]['duration']))) $ret->duration = $info->meta[1]['duration'];
		
		// Copy video properties
		if ($ret->hasVideo) {
			$ret->video = new stdClass();
			
			// Trust the parsed video tag for codec
			if ($gotVideo) {
				// Got one, use it
				$ret->video->codec = $info->videoCodec;
			}
			
			// Width
			if (isset($frameWidth)) {
				$ret->video->width = $frameWidth;
			} else if ((isset($info->meta)) && (isset($info->meta[1])) && (isset($info->meta[1]['width']))) { 
				$ret->video->width = $info->meta[1]['width'];
			}
				
			// Height
			if (isset($frameHeight)) {
				$ret->video->height = $frameHeight;
			} else if ((isset($info->meta)) && (isset($info->meta[1])) && (isset($info->meta[1]['height']))) {
				$ret->video->height = $info->meta[1]['height'];
			}
				
			// Set information from meta
			if ((isset($info->meta)) && (isset($info->meta[1]))) {
				// Codec (if not found from video frame)
				if (!isset($ret->video->codec)) $ret->video->codec = $info->meta[1]['videocodecid'];

				
				// Bitrate
				if (isset($info->meta[1]['videodatarate'])) $ret->video->bitrate = $info->meta[1]['videodatarate'];
				
				// FPS
				if (isset($info->meta[1]['framerate'])) {
					$ret->video->fps = $info->meta[1]['framerate'];
				}

				// Cue points
				if (isset($info->meta[1]['cuePoints'])) $ret->video->cuepoints = $info->meta[1]['cuePoints'];
			}
				
			// Key frame ratio, FPS and bit rate
			$lastKeyFrame = $lastTimestamp = false;
			$keyFrameSum = $keyFrameCount = 0;
			$frameSum = $frameCount = $frameSizeSum = 0;
			foreach ($vFrames as $idx=>$frame) {
				if ($frame['type'] == 0x01) {
					if ($lastKeyFrame !== false) {
						$keyFrameSum += $idx-$lastKeyFrame;
						$keyFrameCount++;
					}
				
					$lastKeyFrame = $idx;
				}

				if ($lastTimestamp !== false) {
					$frameSum += $frame['timestamp']-$lastTimestamp;
					$frameCount++;
				}
				$lastTimestamp = $frame['timestamp'];
				$frameSizeSum += $frame['size'];
			}
			if (($keyFrameSum > 0) && ($keyFrameCount > 0)) {
				$ret->video->keyframeRatio = 1/($keyFrameSum/$keyFrameCount);
				$ret->video->keyframeEvery = ($keyFrameSum/$keyFrameCount);
			}
			if (($frameSum > 0) && ($frameCount > 0) && (!isset($ret->video->fps))) {
				$ret->video->fps = round(1000/($frameSum/$frameCount));
			}
			if (($frameSizeSum > 0) && ($frameCount > 0) && (!isset($ret->video->bitrate))) {
				$ret->video->bitrate = round($frameSizeSum/$frameSum)*8;
			}
		}
		
		// Copy audio properties
		if ($ret->hasAudio) {
			$ret->audio = new stdClass();
			
			// Trust the parsed audio tag 
			if ($gotAudio) {
				// Got one, use it
				$ret->audio->codec = $info->audioCodec;
				$ret->audio->frequency = $info->audioFreq;
				$ret->audio->depth = $info->audioDepth;
				$ret->audio->channels = $info->audioChannels;
			}
			
			// Set information from meta
			if (isset($info->meta[1])) {
				// Codec (if not found from video frame)
				if (!isset($ret->audio->codec)) $ret->audio->codec = $info->meta[1]['audiocodecid'];
				
				// Bitrate
				if (isset($info->meta[1]['audiodatarate'])) $ret->audio->bitrate = $info->meta[1]['audiodatarate'];
			}
			
			// Get bitrate if not specified
			if (!isset($ret->audio->bitrate)) {
				$lastTimestamp = false;
				$frameSum = $frameCount = $frameSizeSum = 0;
				foreach ($aFrames as $idx=>$frame) {
					if ($lastTimestamp !== false) {
						$frameSum += $frame['timestamp']-$lastTimestamp;
						$frameCount++;
					}
					$lastTimestamp = $frame['timestamp'];
					$frameSizeSum += $frame['size'];
				}
				if (($frameSizeSum > 0) && ($frameCount > 0)) {
					$ret->audio->bitrate = round($frameSizeSum/$frameSum)*8;
				}				
			}
		}

		// Get strings for audio/video codecs
		if ((isset($ret->video)) && (isset($ret->video->codec))) {
			switch ($ret->video->codec) {
				case self::FLV_VIDEO_CODEC_SORENSON_H263:
					$ret->video->codecStr = 'Sorenson H263';
					$ret->minimalFlashVersion = 7;
					break;
				case self::FLV_VIDEO_CODEC_SORENSON:
					$ret->video->codecStr = 'Sorenson';
					$ret->minimalFlashVersion = 7;
					break;
				case self::FLV_VIDEO_CODEC_ON2_VP6:
					$ret->video->codecStr = 'On2 VP6';
					$ret->minimalFlashVersion = 8;
					break;
				case self::FLV_VIDEO_CODEC_ON2_VP6ALPHA:
					$ret->video->codecStr = 'On2 VP6 Alpha';
					$ret->minimalFlashVersion = 8;
					break;
				case self::FLV_VIDEO_CODEC_SCREENVIDEO_2:
					$ret->video->codecStr = 'Screen Video 2';
					$ret->minimalFlashVersion = 7;
					break;
			}
		}

		if ((isset($ret->audio)) && (isset($ret->audio->codec))) {
			switch ($ret->audio->codec) {
				case self::FLV_AUDIO_CODEC_UNCOMPRESSED:
					$ret->audio->codecStr = 'Uncompressed';
					break;
				case self::FLV_AUDIO_CODEC_ADPCM:
					$ret->audio->codecStr = 'ADPCM';
					break;
				case self::FLV_AUDIO_CODEC_MP3:
					$ret->audio->codecStr = 'MP3';
					break;
				case self::FLV_AUDIO_CODEC_NELLYMOSER_8K:
				case self::FLV_AUDIO_CODEC_NELLYMOSER:
					$ret->audio->codecStr = 'Nellymoser';
					break;
			}
		}
		
		$ret->rawMeta = isset($info->meta) ? $info->meta : null;
		
		// Return the object
		return $ret;
	} // getInfo method

	/**
	 * Get a files meta data and cuepoints
	 * 
	 * @author 	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param	string       $filename
	 * @return	array(metas=>array(...),cuepoints=>array(...))
	 */
	public function getMeta($filename) {
		// Open file
		$f = fopen($filename,'rb');
		
		// Read header
		$buf = fread($f, 9);
		$header = unpack('C3signature/Cversion/Cflags/Noffset', $buf);
		
		// Check signature
		$signature = (($header['signature1'] == 70) && ($header['signature2'] == 76) && ($header['signature3'] == 86));
		
		// If signature is valid, go on
		$cuepoints = $metas = array();
		if ($signature) {
			// Read tags
			fseek($f, $header['offset']);
			//$prevTagSize = 0;
			do {
				// Read tag header and check length
				$buf = fread($f, 15);
				if (strlen($buf) < 15) break;
				
				// Interpret header
				$tagInfo = unpack('Nprevsize/C1type/C3size/C4timestamp/C3stream', $buf);
				$tagInfo['size'] = ($tagInfo['size1'] << 16) + ($tagInfo['size2'] << 8) + ($tagInfo['size3']);
				$tagInfo['stream'] = ($tagInfo['stream1'] << 16) + ($tagInfo['stream2'] << 8) + $tagInfo['stream3'];
				$tagInfo['timestamp'] = ($tagInfo['timestamp1'] << 16) + ($tagInfo['timestamp2'] << 8) + $tagInfo['timestamp3'] + ($tagInfo['timestamp4'] << 24);
				
				// Validate previous offset
				//if ($tagInfo['prevsize'] != $prevTagSize) {
					// Do nothing
				//}
				
				// Read tag body (max 16k)
				$nextOffset = ftell($f) + $tagInfo['size'];
				$body = fread($f, min($tagInfo['size'],16384));
				
				// Seek
				fseek($f, $nextOffset);
				
				// Intepret body
				switch ($tagInfo['type']) {
					case 0x09: 	// Video tag
						break;
					case 0x08:	// Audio tag
						break;
					case 0x12:	// Meta tag
						// Initialize parser
						$parser = new AMF0Parser();
						
						// Parse data
						$meta = $parser->readAllPackets($body);

						switch ($meta[0]) {
							case 'onMetaData':
								$metas[] = $meta;
								break;
							case 'onCuePoint':
								$cuepoints[] = $meta;
								break;
						}
						
						// Save it
						break;
				}
			} while (
				(feof($f) == false)
			);
		} 
		
		// Close file
		fclose($f);
		
		return array('metas'=>$metas,'cuepoints'=>$cuepoints);
	} // getMeta method
	
	/**
	 * Rewrite (or strip) meta data and cuepoints from FLV file
	 *
	 * @author 	Tommy Lacroix <lacroix.tommy@gmail.com>
	 * @access 	public
	 * @param 	string		$in		Input file
	 * @param 	string		$out	Output file (must be different!)
	 * @param 	string[]	$meta	Meta data (ie. array('width'=>320,'height'=>240,...), optional
	 * @param 	array		$cuepoints	Cuepoints (ie. array(array('name'=>'cue1','time'=>'4.4',type=>'event'),...), optional
	 */
	public function rewriteMeta($in, $out, $meta = false, $cuepoints = false) {
		// No tag found yet
		$tagParsed = 0;
		
		// Open input file
		$f = fopen($in,'rb');
		
		// Read header
		$buf = fread($f, 9);
		$header = unpack('C3signature/Cversion/Cflags/Noffset', $buf);
		
		// Check signature
		$signature = (($header['signature1'] == 70) && ($header['signature2'] == 76) && ($header['signature3'] == 86));
		
		// If signature is valid, go on
		if ($signature) {
			// Open output file, and write header
			$o = fopen($out,'w');
			if (!$o) {
				throw new Exception('Cannot open output file!');
			}
			fwrite($o, $buf, 9);
			
			// Version
			//$version = $header['version'];
			
			// Write meta data
			if ($meta !== false) {
				$parser = new AMF0Parser();
				$metadata = $parser->writeString('onMetaData');
				$metadata .= $parser->writeMixedArray($meta);
				$metadataSize = substr(pack('N',strlen($metadata)),1);
				
				// Write 
				$metadataHead = pack('N',0).
					chr(18).
					$metadataSize.
					pack('N',0).
					chr(0).chr(0).chr(0);
				fwrite($o, $metadataHead);
				fwrite($o, $metadata);
			}
			
			// Read tags
			//$prevTagSize = 0;
			$lastTimeStamp = 0;
			do {
				// Read tag header and check length
				$buf = fread($f, 15);
				if (strlen($buf) < 15) break;
				
				// Interpret header
				$tagInfo = unpack('Nprevsize/C1type/C3size/C4timestamp/C3stream', $buf);
				$tagInfo['size'] = ($tagInfo['size1'] << 16) + ($tagInfo['size2'] << 8) + ($tagInfo['size3']);
				$tagInfo['stream'] = ($tagInfo['stream1'] << 16) + ($tagInfo['stream2'] << 8) + $tagInfo['stream3'];
				$tagInfo['timestamp'] = ($tagInfo['timestamp1'] << 16) + ($tagInfo['timestamp2'] << 8) + $tagInfo['timestamp3'] + ($tagInfo['timestamp4'] << 24);
				
				// Need we insert a cuepoint here?
				if ($cuepoints !== false) {
					foreach ($cuepoints as $index=>$cuepoint) {
						if (($lastTimeStamp < $cuepoint['time']*1000) && ($tagInfo['timestamp'] >= $cuepoint['time']*1000)) {
							// Write cuepoint
							$parser = new AMF0Parser();
							$metadata = $parser->writeString('onCuePoint');
							$metadata .= $parser->writeMixedArray($cuepoint);
							$metadataSize = substr(pack('N',strlen($metadata)),1);
							
							// Write first
							$metadataHead = pack('N',$tagInfo['prevsize']).
								chr(18).
								$metadataSize.
								pack('N',0).
								chr(0).chr(0).chr(0);
							fwrite($o, $metadataHead);
							fwrite($o, $metadata);
							
							// Update prevsize in $buf
							// @todo
							
							// Unset cuepoint
							unset($cuepoints[$index]);
						}
					}
				}
				
				// Validate previous offset
				//if ($tagInfo['prevsize'] != $prevTagSize) {
					// Do nothing
				//}
				
				// Read tag body
				$body = fread($f, $tagInfo['size']);
				
				// Intepret body
				switch ($tagInfo['type']) {
					case 0x09: 	// Video tag
						// Write to output file
						fwrite($o,$buf,15);
						fwrite($o,$body,$tagInfo['size']);
						break;
					case 0x08:		// Audio tag
						// Write to output file
						fwrite($o,$buf,15);
						fwrite($o,$body,$tagInfo['size']);
						break;
					case 0x12:	// Meta tag
						// Skip
						break;
				}
				
				// Increase parsed tag count
				$tagParsed++;
			} while (
				(feof($f) == false)
			);
		} 
		
		// Close file
		fclose($f);
		fclose($o);
	} // rewriteMeta method
} // FLVInfo2 class

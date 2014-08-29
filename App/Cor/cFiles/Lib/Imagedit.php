<?php
	/*  
	*	Image Library to edit and modify Images
    *	Copyright (C) 2011  Jakob Riedle <ijake@palmato.de>.
	*	
	*	This program is free software: you can redistribute it and/or modify
	*	it under the terms of the GNU General Public License as published by
	*	the Free Software Foundation, either version 3 of the License, or
	*	(at your option) any later version.
	*
	*	This program is distributed in the hope that it will be useful,
	*	but WITHOUT ANY WARRANTY; without even the implied warranty of
	*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	*	GNU General Public License for more details.
	*
	*	You should have received a copy of the GNU General Public License
	*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*
	* 
	* 	@Jakob Riedle <ijake@palmato.de> 
	* 	@version 1.0 
	* 	@since 1.0 
	* 	@access public 
	* 	@copyright iJake@Palmato 
	* 
	*/ 
	
	class cFiles_Lib_Imagedit {
	
		/** 
		* Image Resource to be edited
		* 
		* @var image Resource 
		* @access private 
		* @see loadFile() 
		*/ 
		private $image;
		
		/** 
		* Image Height
		*
		* @access private 
		*/ 
		private $height;
		/** 
		* Image Width
		*
		* @access private 
		*/ 
		private $width;
		
		/** 
		* Preferences
		*
		* @access private 
		*/ 
		private $prefs;
		
		// Only check for a face once because very slow
		private $face;
		
		
		/** 
		* Constructor
		* 
		* @param String $url File to load
		*/ 
		public function __construct( $url = "" )
		{
			$this->image = null;
			
			// Set The Image if it was passed
			$this->setImage( $url );
			
			$this->face = null;
			$this->prefs = Array();
		}
		
			
		/** 
		* Set The Image
		*
		* @param String $url File to load
		*/ 
		public function setImage( $url )
		{
			$this->image = null;
			
			if( $url != "" && is_file($url) ){
			    				
				$path_info = pathinfo($url);
				
				switch($path_info['extension']){
					case "jpg":
					case "jpeg":
						$this->image = imagecreatefromjpeg($url);
						break;
					case "png":
						$this->image = imagecreatefrompng($url);
						break;
					case "gif":
						$this->image = imagecreatefromgif($url);
						break;
					default:
						return false;
				}
								
				$this->preserveAlpha();
				$this->setSize();
			}
			/* elseif( $url != "" && preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url) ){ */
			elseif( Zend_Uri_Http::check($url) ){
				if( !in_array( 'curl' , get_loaded_extensions() ) ) {
				    //throw new Exception( "cUrl Library has to be installed to include cross-domain Images!" );
				    $data = file_get_contents($url);
				} 
				else {
				    $curl = curl_init();
				    curl_setopt( $curl, CURLOPT_URL, $url );
				    ob_start();
				    curl_exec( $curl );
				    $data = ob_get_contents();
				    ob_end_clean();
				}
				
				$this->image = imagecreatefromstring( $data );
			}
			else {
				return false;
			}
		}
		
		/** 
		* Check if an Image is loaded
		*
		* @return Boolean Is an Image Loaded?
		*/ 
		public function isLoaded()
		{
			return ( $this->image != null );
		}
		
		/** 
		* Makes the image preserve it's Alpha Channel
		*
		* @access private 
		*/ 
		private function preserveAlpha( $image = null)
		{
			if( ! $this->isLoaded() )
				return false;
				
			if( $image != null ){
				// Preserve Alpha-Channel
				imagealphablending($image, true); 
				imagesavealpha($image, true); 
			}
			else{
				// Preserve Alpha-Channel
				imagealphablending($this->image, true); 
				imagesavealpha($this->image, true); 
			}
		}
		
		/** 
		* Save the Image-dimensions to $height and $width
		* 
		* @see $height
		* @see $width
		* @access private 
		*/ 
		private function setSize()
		{
			if( ! $this->isLoaded() )
				return false;
				
			$this->width = imagesx( $this->image );
			$this->height = imagesy( $this->image );
		}
		
		public function getWidth()
		{
		    return imagesx( $this->image );
		}
		
		public function getHeight()
		{
		    return imagesy( $this->image );
		}
		
		/** 
		* Check if the image that is loaded has a Face on it
		* 
		* @return Boolean
		*/ 
		public function hasFace()
		{
			if( ! $this->isLoaded() )
				return false;
				
			if( $this->face != null)
				return true;
			$detector = new cFiles_Lib_Facedetect('detection.dat');
			if( $detector->face_detect($this->image) ){
				$this->face = $detector->getFace();
				return true;
			}
			else
				return false;
		}
		
		/** 
		* Rotates the image by a specific angle
		* 
		* @param Float $angle The Angle the Image is rotated by
		*/ 
		public function rotate( $angle , $crop = false )
		{
			if( ! $this->isLoaded() )
				return false;
			
			$this->image = imagerotate($this->image, $angle, -1);
			$this->setSize();
			$this->preserveAlpha();
			
			if( $crop ){
				$h = $this->getHeight();
				$w = $this->getWidth();
				$dimens = $this->rverdrehung( $w , $h , $angle );
				$y_crop = ( $h - $dimens['height'] ) / 2;
				$x_crop = ( $w - $dimens['width'] ) / 2;
				$this->crop( $x_crop , $y_crop , $dimens['width'] , $dimens['height'] );
			}
			
			return $this;
		}
		
		/** 
		* Rotation of a Rectangle inside another
		* 
		* @param Integer $width Width of the original Rect
		* @param Integer $height Height of the original Rect
		* @param Integer $angle Angle to rotate the rect
		* @copyright Jakob Riedle
		*/ 
		/*private function rverdrehung( $width , $height , $angle ){
			$angle = deg2rad( $angle%90 );
			$n = $height;
			$m = $width;
			$n0 = $n - $n / ( tan( $angle ) * ( $n / $m ) + 1 );
			$m0 = $n0 * $ratio;
			$n_new = sqrt( $m0*$m0 + ( $n - $n0 )*( $n - $n0 ) );
			return array( "width" => intval( $n_new * ( $m / $n ) ) , "height" => intval( $n_new ) );
		}*/
		private function rverdrehung( $width , $height , $angle )
		{
			$angle = deg2rad( $angle%90 );
			$equi0 = $height / tan($angle + atan( $height / $width ));
			$r = ( $equi0 * $equi0 + $height * $height ) / ( $width * $width  +  $height * $height );
			return array( "width" => intval( $width * $r ) , "height" => intval( $height * $r ) );
		}
		
		/** 
		* Convert the Image to grayscale
		*/ 
		public function grayscale()
		{
			if( ! $this->isLoaded() )
				return false;
			
			imagefilter( $this->image , IMG_FILTER_GRAYSCALE );
			
			return $this;
		}
		
		/**
		* Extract Either the Image out of Imagedit-Class or return an Image
		*/
		private function extractImage( $image )
		{
			if( is_object( $image ) && get_class( $image ) == "imagedit" )
				return $image->getImage();
			else
				return $image;
		}
		
		/** 
		* Multiply Brightness of two pictures
		*
		* @param $image Gray (128,128,128) effects nothing White makes bright, Black makes dark
		*/ 
		public function multiply( $image , $multiplyAlpha = false )
		{
			$image = $this->extractImage( $image );
			
			if( ! @imagesx( $image ) )
				throw new Exception("No valid image!");
				
			imageAlphaBlending( $this->image, false);
			$noAlpha = !$multiplyAlpha;
			
			for( $x = 0; $x < $this->getWidth() ; $x++ ){
				for( $y = 0; $y < $this->getHeight() ; $y++ ){
					$color1 = imagecolorsforindex($this->image, imagecolorat( $this->image , $x , $y ) );
					$color2 = imagecolorsforindex($image, imagecolorat( $image , $x , $y ) );
					$alpha = ( $color1["alpha"] + $color2["alpha"]/255) * $multiplyAlpha + $color1["alpha"] * $noAlpha;
					$color1 = imagecolorallocatealpha( $this->image , $color1["red"]*($color2["red"]/255) , $color1["green"]*($color2["green"]/255) , $color1["blue"]*($color2["blue"]/255) , $alpha );
					imagesetpixel( $this->image , $x , $y , $color1 );
				}
			}
			imageAlphaBlending( $this->image, true);
			
			return $this;
		}
		
		/** 
		* Mask The Picture width B/W
		*
		* @param $image The Mask to be applied ( Black means full opaque, White means full transparent )
		*/ 
		public function mask( $image )
		{
			$image = $this->extractImage( $image );
			
			if( ! @imagesx( $image ) )
				throw new Exception("No valid image!");
			
			imageAlphaBlending( $this->image, false);
			
			for( $x = 0; $x < $this->getWidth() ; $x++ ){
				for( $y = 0; $y < $this->getHeight() ; $y++ ){
					$color1 = imagecolorsforindex($this->image, imagecolorat( $this->image , $x , $y ) );
					$color2 = imagecolorsforindex($image, imagecolorat( $image , $x , $y ) );
					$alpha = $color1["alpha"] + ( $color2["red"] + $color2["green"] + $color2["blue"] ) / 6;
					$alpha = intval( $alpha );
					if( $alpha > 127 )
						$alpha = 127;
					//var_dump( $alpha );
					$color = imagecolorallocatealpha( $this->image , $color1["red"] , $color1["green"] , $color1["blue"] , $alpha );
					imagesetpixel( $this->image , $x , $y , $color );
				}
			}
			
			imageAlphaBlending( $this->image, true);
			
			return $this;
		}
		
		/**
		* Put some Overlay/Image over this Image
		*
		* @param Image Resource $image The Image as a Resource or an Imagedit Class
		* @param Integer $x X-Position to put the Overlay (Optional, defaults 0)
		* @param Integer $y Y-Position to put the Overlay (Optional, defaults 0)
		* @param String $align Alignment of the Overlay (Optional, defaults "topleft")
		*/
		public function overlay( $image , $x = 0 , $y = 0 , $align = "topleft" )
		{
			$image = $this->extractImage( $image );
			
			if( ! @imagesx( $image ) )
				throw new Exception("No valid image!");
			
			$width = imagesx( $image );
			$height = imagesy( $image );
			$myWidth = $this->getWidth();
			$myHeight = $this->getHeight();
			$x = ( $align == "topleft" || $align == "lefttop" || $align == "bottomleft" || $align == "leftbottom" ) ? $x : $myWidth - $width - $x ;
			$y = ( $align == "topleft" || $align == "lefttop" || $align == "topright" || $align == "righttop" ) ? $y : $myHeight - $height - $y ;
			
			imagecopy( 
				$this->image /* Destination */
				, $image /* Source */
				, $x /* Dest X */ 
				, $y /* Dest Y */
				, 0 /* Source X */
				, 0 /* Source Y */
				, $width /* Source Width */
				, $height /* Source Height */
			);
			
			return $this;
		}
		
		/**
		* Set A Specific Color(-Range) To be Transparent
		*
		* @param Array $color The Main Color of Transparency
		* @param Integer $tolerance Tolerance of Main Color (Optional, defaults 0 )
		* @param String $fix Whether the Transparency should be fixed ( See examples )
		*/
		public function setTransparent( $color , $tolerance = 0 , $fix = false )
		{
			if( $tolerance == -1 )
				imagecolortransparent( $this->image , imagecolorallocate( $this->image , $color[0] , $color[1] , $color[2] ));
			else{
				$tolerance += 1;
				$r = $color[0];
				$g = $color[1];
				$b = $color[2];
						
				imageAlphaBlending( $this->image, false);
				
				for( $x = 0; $x < $this->getWidth() ; $x++ ){
					for( $y = 0; $y < $this->getHeight() ; $y++ ){
						$color2 = @imagecolorsforindex($this->image, imagecolorat( $this->image , $x , $y ) );
						$r2 = $color2["red"];
						$g2 = $color2["green"];
						$b2 = $color2["blue"];
						if( !$fix ){
							if( $b < ( $b2 + $tolerance ) && $g < ( $g2 + $tolerance ) && $r < ( $r2 + $tolerance ) && $b > ( $b2 - $tolerance ) && $g > ( $g2 - $tolerance ) && $r > ( $r2 - $tolerance ) )
								imagesetpixel( $this->image , $x , $y , IMG_COLOR_TRANSPARENT );
						}
						else{
							// Color-distance to Alpha
							$alpha = intval( 127 - sqrt( abs( ($r2 - $r) * ($g2 - $g) * ($b2 - $b) ) ) );
							$alpha += $tolerance;
							imagesetpixel( $this->image , $x , $y , imagecolorallocatealpha( $this->image , $r2 , $g2 , $b2 , min( 127 , max( 0 , $alpha ) ) ) );
						}
					}
				}
				imageAlphaBlending( $this->image, true);
			}
		}
		/** 
		* Set the Image Brightness
		*
		* @param $brighness Brightness to apply ( -100 To 100 )
		*/ 
		public function brightness( $brightness )
		{
			if( ! $this->isLoaded() )
				return false;
			
			$brightness = $brightness / 100 * 255;
			imagefilter( $this->image , IMG_FILTER_BRIGHTNESS , $brightness);
			
			return $this;
		}
		
		/** 
		* Set the Image Contrast
		*
		* @param $bcontrast Contrast to apply ( -100 To 100 )
		*/ 
		public function contrast( $contrast )
		{
			if( ! $this->isLoaded() )
				return false;
			
			imagefilter( $this->image , IMG_FILTER_CONTRAST , $contrast);
			
			return $this;
		}
		
		/** 
		* Crop the Image by Position-Dimension-Pair
		* 
		* @param Integer $x X-Position of Left-Upper Corner
		* @param Integer $y Y-Position of Left-Upper Corner
		* @param Integer $width Width of the cropped Image
		* @param Integer $height Height of the cropped Image
		*/ 
		public function crop( $x , $y , $width , $height )
		{
			if( ! $this->isLoaded() )
				return false;
				
			$new_image = imagecreatetruecolor( $width , $height );
			imagefill($new_image , 0 , 0 , IMG_COLOR_TRANSPARENT);
			
			imagecopy( 
				$new_image /* Destination */
				, $this->image /* Source */
				, 0 /* Dest X */ 
				, 0 /* Dest Y */
				, $x /* Source X */
				, $y /* Source Y */
				, $width /* Source/Dest Width */
				, $height /* Source/Dest Height */
			);
			unset($this->image);
			
			$this->image = $new_image;
			$this->setSize();
			$this->preserveAlpha();
			
			return $this;
		}
		
		/** 
		* Crop the Image by Borders
		* 
		* @param Integer $left Left-Crop
		* @param Integer $top Top-Crop
		* @param Integer $right Right-Crop
		* @param Integer $bottom Bottom-Crop
		*/ 
		public function cropBorder( $left , $top , $right , $bottom )
		{
			if( ! $this->isLoaded() )
				return false;
				
			$new_image = imagecreatetruecolor( $this->getWidth()-$left-$right , $this->getHeight()-$top-$bottom );
			imagefill($new_image , 0 , 0 , IMG_COLOR_TRANSPARENT);
			
			imagecopy( 
				$new_image /* Destination */
				, $this->image /* Source */
				, 0 /* Dest X */ 
				, 0 /* Dest Y */
				, $left /* Source X */
				, $top /* Source Y */
				, $this->getWidth()-$left-$right /* Source/Dest Width */
				, $this->getHeight()-$top-$bottom /* Source/Dest Height */
			);
			unset($this->image);
			
			$this->image = $new_image;
			$this->setSize();
			$this->preserveAlpha();
			
			return $this;
		}
		
		/** 
		* Crop the Image to a Face
		* 
		* @param Boolean $preserveRatio Should the Output-Image be same-dimensioned as Input?
		*/ 
		public function cropFace( $preserveRatio = false )
		{
			if( ! $this->isLoaded() )
				return false;
				
			if( $this->hasFace() ){
				$faceData = $this->face;
				$Fx = intval($faceData['x']);
				$Fy = intval($faceData['y']);
				$FWidth = intval($faceData['w']);
				$FHeight = intval($faceData['w']);
				$width = $FWidth;
				$height = $FHeight;
				$x = $Fx;
				$y = $Fy;
				if( $preserveRatio )
				{
					$ratio = $this->getWidth() / $this->getHeight();
					if( $ratio > 1 ){ // wider than high
						$width = min( $this->getWidth() , $FHeight * $ratio );
						$height = $width / $ratio;
					}
					elseif( $ratio < 1 ){ // higher than wide
						$height = min( $this->getHeight() , $FWidth * $ratio );
						$width = $height * $ratio;
					}
					
					// When Size changes: Adjust X and Y Position to center face
					$y = $Fy + ( $FHeight - $height ) / 2;
					$x = $Fx + ( $FWidth - $width ) / 2;
					
					if( $x < 0 ) $x = 0;
					if( $y < 0 ) $y = 0;
					if( $x + $width > $this->getWidth() ) $x = $this->getWidth() - $width;
					if( $y + $height > $this->getHeight() ) $y = $this->getHeight() - $height;
				}
				// Convert them all to Int
				$width = intval($width);
				$height = intval($height);
				$x = intval($x);
				$y = intval($y);
				
				// Create the new Image	
				$new_image = imagecreatetruecolor( $width , $height );
				imagefill($new_image , 0 , 0 , IMG_COLOR_TRANSPARENT);
				
				imagecopy(
					$new_image /* Destination */
					, $this->image /* Source */
					, 0 /* Dest X */ 
					, 0 /* Dest Y */
					, $x /* Source X */
					, $y /* Source Y */
					, $width /* Source/Dest Width */
					, $height /* Source/Dest Height */
				);
				unset($this->image);
				
				$this->image = $new_image;
				$this->setSize();
				$this->preserveAlpha();
				
				return $this;
			}
			else
				return false;
		}
		
		/** 
		* Return the Image
		*
		* @return Image Resource
		*/ 
		public function getImage()
		{
			if( ! $this->isLoaded() )
				return false;
				
			return $this->image;
		}
		
		/** 
		* Resize the Image by setting it's Height
		* 
		* @param Integer $height The Height of the Output-Image
		* @param Boolean $preserveRatio Should the Image Preserve its Ratio?
		*/ 
		public function setHeight( $height , $preserveRatio = false )
		{
			if( ! $this->isLoaded() )
				return false;
				
			if( $preserveRatio )
				$width = $this->getWidth() / $this->getHeight() * $height;
			else
				$width = $this->getWidth();
				
			$new_image = imagecreatetruecolor( $width , $height );
			imagefill($new_image , 0 , 0 , IMG_COLOR_TRANSPARENT);
			
			imagecopyresampled( 
				$new_image /* Destination */
				, $this->image /* Source */
				, 0 /* Dest X */ 
				, 0 /* Dest Y */
				, 0 /* Source X */
				, 0 /* Source Y */
				, $width /* Dest Width */
				, $height /* Dest Height */
				, $this->getWidth() /* Source Width */
				, $this->getHeight() /* Source Height */
			);
			unset($this->image);
			
			$this->image = $new_image;
			$this->setSize();
			$this->preserveAlpha();
			
			return $this;
		}
		
		/** 
		* Resize the Image by setting it's Width
		* 
		* @param Integer $width The Width of the Output-Image
		* @param Boolean $preserveRatio Should the Image Preserve its Ratio?
		*/ 
		public function setWidth( $width , $preserveRatio = false )
		{
			if( ! $this->isLoaded() )
				return false;
				
			if( $preserveRatio )
				$height = $this->getHeight() / $this->getWidth() * $width;
			else
				$height = $this->getHeight();
						
			$new_image = imagecreatetruecolor( $width , $height );
			imagefill($new_image , 0 , 0 , IMG_COLOR_TRANSPARENT);
			
			imagecopyresampled( 
				$new_image /* Destination */
				, $this->image /* Source */
				, 0 /* Dest X */ 
				, 0 /* Dest Y */
				, 0 /* Source X */
				, 0 /* Source Y */
				, $width /* Dest Width */
				, $height /* Dest Height */
				, $this->getWidth() /* Source Width */
				, $this->getHeight() /* Source Height */
			);
			unset($this->image);
			
			$this->image = $new_image;
			$this->setSize();
			$this->preserveAlpha();
			
			return $this;
		}
		
		/** 
		* Output the image
		*/ 
		public function getPNG()
		{
			if( ! $this->isLoaded() )
				return false;
				
			ob_start();
			imagepng( $this->image );
			$data = ob_get_contents();
			ob_end_clean();
			return $data;
		}
		/*
		** Output the Image
		*/
		public function getJPG()
		{
			if( !$this->isLoaded() )
				return false;
				
			ob_start();
			imagejpeg( $this->image );
			$data = ob_get_contents();
			ob_end_clean();
			return $data;
		}
	}
?>
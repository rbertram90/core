<?php

namespace rbwebdesigns\core;

/**
 * Provides an easy wrapper to upload images
 */
class ImageUpload
{
    public $data;
    public $maxUploadSize;
    public $fileTypes;
	
    public function __construct($fileData)
    {
        $this->data = $fileData;
        $this->maxUploadSize = 1000000; // 1 MB
        $this->fileTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    }

    /**
     * @return string
     */
    public function getFileExtention()
    {
        return strtolower(end(explode('.', $this->data['name'])));
    }
    
    // Create thumbnail (current image, new image folder)
    // createThumb($user_image_folder."/".$_FILES["file"]["name"], $user_image_folder."/square/", 300, 300);
    public function createThumbnail($psImagePath, $psDestinationPath, $piWidth = 300, $piHeight = 300) {
        
        // Actual Image Size
        list($imageHeight, $imageWidth) = getimagesize($psImagePath);
        
        // Thumbnail Size
        list($thumbHeight, $thumbWidth) = array($piHeight, $piWidth);
        
        // Create full size Image from file path
        $srcImage = imagecreatefromjpeg($psImagePath);
        
        $lsTargetDir = pathinfo($psDestinationPath, PATHINFO_DIRNAME);
                
        // Create the directory structure if not already in place
        if(!is_dir($lsTargetDir) && strlen($lsTargetDir) > 0) mkdir($lsTargetDir);
        
        // Find the smallest of the two
        $min = min(array($imageHeight, $imageWidth));
        
        // Calculate how to resize the image to a (smaller?) one
        if($min == $imageWidth) {
            $startY = 0;
            $startX = floor(($imageHeight - $imageWidth) / 2);
        }
        else {
            $startX = 0;
            $startY = floor(($imageWidth - $imageHeight) / 2);
        }
        
        // Create thumbnail
        $destImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        //imagecopy($destImage, $srcImage, 0, 0, $startX, $startY, $min, $min);
        imagecopyresampled($destImage, $srcImage, 0, 0, $startX, $startY, $thumbWidth, $thumbHeight, $min, $min);
        
        // Output to file
        imagejpeg($destImage, $psDestinationPath);
        
        // Create return information
        $larrUpload = array();
        $larrUpload['filename'] = $psDestinationPath;
        $larrUpload['filepath'] = $psDestinationPath;
        $larrUpload['filesize'] = getimagesize($psDestinationPath);
        $larrUpload['fileerror'] = '';
        
        // Output to screen?
        // echo "<p><img src='$destLoc' alt='thumbnail' /></p>";
        return $larrUpload;
    }
    
	/*
	 *  Example Input for multiple file upload
	 *  Array (
     *      [upload] => Array ( 
     *          [name] => Array ( [0] => map_v3-Optimized.png [1] => ttd_small.png ) 
	 *	        [type] => Array ( [0] => image/png [1] => image/png ) 
	 *	        [tmp_name] => Array ( [0] => C:\xampp\tmp\php3AC2.tmp [1] => C:\xampp\tmp\php3AC3.tmp ) 
	 *	        [error] => Array ( [0] => 0 [1] => 0 ) 
	 *	        [size] => Array ( [0] => 179949 [1] => 23277 ) 
	 *      )
	 *  )
	 */
    public function multiUpload($files) {
        // To do...
		// <input name="upload[]" type="file" multiple="multiple" />		
		echo "<pre>".print_r($_FILES)."</pre>";
    }
    
    public function createFileName($psPrefix = "", $psSuffix = "") {
        $lsFileName = $psPrefix.rand(10000,32000).rand(10000,32000).$psSuffix.'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        return strtolower($lsFileName);
    }
    
    /**
     * Save the file to more permanent location
     * 
     * @throws Exception With any error with upload
     */
    public function upload($uploadFolder, $fileName='') {
        
        // Validate
        if (!in_array($this->data['type'], $this->fileTypes)) {
            throw new \Exception("Unable to upload - {$this->data['type']} not in allowed file types list");
        }
        if ($this->data['size'] > $this->maxUploadSize) {
            throw new \Exception('Unable to upload - Maximum file size is 200KB');
        }
        if ($this->data['error'] > 0) {
            throw new \Exception('Unable to upload - Error has Occurred Code: '. $this->data['error']);
        }
        if (!is_dir($uploadFolder)) {
            throw new \Exception('Unable to upload - target upload directory not found');
        }

        // Ensure the folder has trailing slash
        if (substr($uploadFolder, strlen($uploadFolder) - 1) != '/') {
            $uploadFolder .= '/';
        }
        
        // Make a new file name
        if (strlen($fileName) == 0) {
            $this->data['name'] = $this->createFileName();

            // loop until we find a unique name
            while (file_exists($uploadFolder . $this->data['name'])) {
                $this->data['name'] = $this->createFileName();
            }
        }
        else {
            $this->data['name'] = $fileName;
        }

        $this->data['new_path'] = $uploadFolder . $this->data['name'];
        
        // Move to final resting place & return result
        return move_uploaded_file($this->data['tmp_name'], $this->data['new_path']);
    }

}
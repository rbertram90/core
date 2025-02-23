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
	
    public function __construct(array $fileData)
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
    
    /**
     * Create a resized image from original upload
     * 
     * createThumbnail($user_image_folder."/square/", 300, 300);
     * 
     * @return boolean If image was created successfully.
     */
    public function createThumbnail($destinationPath, $newFilename='', $thumbWidth=300, $thumbHeight=300)
    {
        if (!function_exists('imagecreatefromjpeg')) {
            trigger_error('Unable to create thumbnail, is the GD image library enabled in PHP config?', E_USER_ERROR);
        }

        if (!array_key_exists('new_path', $this->data)) {
            die('Image has not been uploaded yet');
        }

        $imagePath = $this->data['new_path'];

        list($imageWidth, $imageHeight) = getimagesize($imagePath);
        $widthRatio = $imageWidth / $thumbWidth;
        $heightRatio = $imageHeight / $thumbHeight;
        
        // Create full size Image from file path
        if ($this->data['type'] == 'image/jpg' || $this->data['type'] == 'image/jpeg') {
            $srcImage = imagecreatefromjpeg($imagePath);
        }
        elseif ($this->data['type'] == 'image/png') {
            $srcImage = imagecreatefrompng($imagePath);
        }
        else {
            die('Cannot create thumbnail from image type: '. $this->data['type']);
        }
        
        // Create the directory structure if not already in place
        if (!is_dir($destinationPath) && strlen($destinationPath) > 0) {
            if (!mkdir($destinationPath, 0777, true)) {
                trigger_error('Unable to create folder ' . $destinationPath, E_USER_ERROR);
            }
        }
        if (is_null($newFilename) || strlen($newFilename) == 0) {
            $newFilename = pathinfo($this->data['new_path'])['basename'];
        }
        
        // Find the smallest of the two
        $min = min(array($widthRatio, $heightRatio));
        
        // Calculate how to resize the image to a (smaller?) one
        if($min == $widthRatio) {
            $startX = 0;
            $startY = floor(($imageHeight - $thumbHeight * $widthRatio) / 2);
            $sourceWidth = $imageWidth;
            $sourceHeight = $thumbHeight * $widthRatio;
        }
        else {
            $startY = 0;
            $startX = floor(($imageWidth - $thumbWidth * $heightRatio) / 2);
            $sourceWidth = $thumbWidth * $heightRatio;
            $sourceHeight = $imageHeight;
        }
        
        // Create thumbnail
        $destImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        // imagecopy($destImage, $srcImage, 0, 0, $startX, $startY, $min, $min);
        imagecopyresampled($destImage, $srcImage, 0, 0, $startX, $startY, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);
        
        // Output to file
        if ($this->data['type'] == 'image/jpg' || $this->data['type'] == 'image/jpeg') {
            $image = imagejpeg($destImage, $destinationPath .'/'. $newFilename);
        }
        elseif ($this->data['type'] == 'image/png') {
            $image = imagepng($destImage, $destinationPath .'/'. $newFilename);
        }

        return $image;
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
    
    public function createFileName($prefix = "", $suffix = "") {
        $fileName = $prefix.rand(10000,32000).rand(10000,32000).$suffix.'.'.pathinfo($this->data['name'], PATHINFO_EXTENSION);
        return strtolower($fileName);
    }
    
    /**
     * Save the file to more permanent location
     * 
     * @throws Exception With any error with upload
     */
    public function upload($uploadFolder, $fileName='') {
        $this->prepareUploadFolder($uploadFolder);

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

    public function prepareUploadFolder($uploadFolder, $permissions = 0777) {
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, $permissions, true);
        }
    }
}
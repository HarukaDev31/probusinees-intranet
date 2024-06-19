<?php
trait FileTrait
{
    /**
     * This function is used to validate the extension and content type of the file
     * and prevent the upload of dangerous files
     * @param $file
     * @param $allowedExtensions
     */
    private $maxFileSize = 3072;
    private $allowedExtensions = array('png', 'jpg', 'jpeg', 'webp', 'PNG', 'JPG', 'JPEG', 'WEBP');
    private $allowedContentTypes = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/jpg', 'image/webp');
    public function __construct($maxFileSize = null, $allowedExtensions = null, $allowedContentTypes = null)
    {
        if ($maxFileSize) {
            $this->maxFileSize = $maxFileSize;
        }
        if ($allowedExtensions) {
            $this->allowedExtensions = $allowedExtensions;
        }if ($allowedContentTypes) {
            $this->allowedContentTypes = $allowedContentTypes;
        }
    }
    

    public function validateExtensionAndContentTypes($name, $type, $allowedExtensions, $allowedContentTypes)
    {
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $contentType = $type;
        if (!in_array($extension, $allowedExtensions) || !in_array($contentType, $allowedContentTypes)) {
            return false;
        }
        return true;
    }
    public function validateSize($size, $maxSize)
    {
        if ($size > $maxSize) {
            return false;
        }
        return true;
    }

    public function uploadSingleFile($file, $path)
    {
        $fileSize= $file['size'] / 1024; // size in K}B
        $fileTname = $file['tmp_name'];
        $fileType = $file['type'];
        $fileName = $file['name'];
        $validateExtensionAndContentTypes = $this->validateExtensionAndContentTypes($fileName, $fileType, $this->allowedExtensions, $this->allowedContentTypes);
        if (!$validateExtensionAndContentTypes) {
            return 'Invalid extension or content type';
        }
        if (!$this->validateSize($fileSize, $this->maxFileSize)) {
            return 'File size exceeds limit';
        }
        try {
            $uploadedFilePath = $this->uploadFile($fileTname, $fileName, $path);
            return base_url() . $uploadedFilePath;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function uploadFile($file, $name, $path)
    {
        try {
            $path = rtrim(str_replace('\\', '/', $path), '/') . '/';

            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true)) {
                    throw new Exception("Failed to create directory: $path");
                }
            }
            $name = time() . '_' . $name;
            $destination = $path . $name;
            //concat current time in format _Y_m_d_H_i_s

            // Attempt to move the uploaded file
            if (move_uploaded_file($file, $destination)) {
                return $destination;
            } else {
                // Handle the error appropriately
                throw new Exception("Failed to move uploaded file to $destination");
                return "Failed to move uploaded file to $destination";
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * This function is used to process the files uploaded by the user in pedidos garantizados
     * @param $data_files - The files data
     * @param $path - The path where the files will be stored
     * @param $filesKey - The keys of the files in the data_files array
     * @param $data - The data of the pedidos garantizados for set directory names
     */
    public function processFiles($data_files, $path, $filesKey, $data)
    {

        $results = [];
        $paths = [];

        $filesLength = count($data_files['file']['tmp_name']);
        for ($filesIndex = 1; $filesIndex <= $filesLength; $filesIndex++) {
            $path = "assets/images/agentecompra/garantizados/" . $data['addProducto'][$filesIndex]['pedido-cabecera'] . "/" . $data['addProducto'][$filesIndex]['id_detalle'];

            foreach ($filesKey as $keyFile) {
                if (empty($data_files['file']['tmp_name'][$filesIndex][$keyFile])) {
                    // Skip if no file is uploaded for this key
                    if ($data) {

                        $product = $data['addProducto'][$filesIndex];
                        if (!array_key_exists($keyFile, $product)) {
                            $results[$filesIndex][$keyFile] = "No file uploaded for this key";
                            $paths[$filesIndex][$keyFile] = null;
                            continue;
                        }
                        if ($product[$keyFile] != "" && $product[$keyFile] != null) {
                            $paths[$filesIndex][$keyFile] = $product[$keyFile];
                            $results[$filesIndex][$keyFile] = "No file uploaded for this key, using existing file";
                            continue;
                        }
                    }
                    $results[$filesIndex][$keyFile] = "No file uploaded for this key";
                    $paths[$filesIndex][$keyFile] = null;
                    continue;
                }

                $rowFile = $data_files['file']['tmp_name'][$filesIndex][$keyFile];
                $type = $data_files['file']['type'][$filesIndex][$keyFile];
                $name = $data_files['file']['name'][$filesIndex][$keyFile];
                $size = $data_files['file']['size'][$filesIndex][$keyFile] / 1024; // size in KB
                $allowedExtensions = array('png', 'jpg', 'jpeg', 'webp', 'PNG', 'JPG', 'JPEG', 'WEBP');
                $allowedContentTypes = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/jpg', 'image/webp');
                $maxSize = 3072; // 1024 KB = 3 MB

                $validateExtensionAndContentTypes = $this->validateExtensionAndContentTypes($name, $type, $allowedExtensions, $allowedContentTypes);
                $validateSize = $this->validateSize($size, $maxSize);

                if (!$validateExtensionAndContentTypes) {
                    $results[$filesIndex][$keyFile] = 'Invalid extension or content type';
                    $paths[$filesIndex][$keyFile] = null;
                    continue;
                }

                if (!$validateSize) {
                    $results[$filesIndex][$keyFile] = 'File size exceeds limit';
                    $paths[$filesIndex][$keyFile] = null;
                    continue;
                }

                try {
                    $uploadedFilePath = $this->uploadFile($rowFile, $name, $path);
                    $results[$filesIndex][$keyFile] = "File uploaded successfully";
                    $paths[$filesIndex][$keyFile] = base_url() . $uploadedFilePath;
                } catch (Exception $e) {
                    $results[$filesIndex][$keyFile] = $e->getMessage();
                }

            }
        }
        return ["results" => $results, "paths" => $paths];
    }
}

<?php
trait FileTrait
{
    /**
     * This function is used to validate the extension and content type of the file
     * and prevent the upload of dangerous files
     * @param $file
     * @param $allowedExtensions
     */
    private $maxFileSize = 50072;
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
        log_message('error', 'Extension: ' . $extension . ' Content Type: ' . $contentType);
        if (!in_array($extension, $allowedExtensions) || !in_array($contentType, $allowedContentTypes)) {
            return false;
        }
        return true;
    }
    public function validateSize($size, $maxSize)
    {
        if ($size > $this->maxFileSize) {
            return false;
        }
        return true;
    }
    public function setAllowedExtensionsImagesOfficeFiles()
    {
        $this->allowedExtensions = array('png', 'jpg', 'jpeg', 'webp', 'PNG', 'JPG', 'JPEG', 'WEBP','doc','docx','xls','xlsx','pdf','xlsm');
        $this->allowedContentTypes = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/jpg', 'image/webp','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/pdf','application/vnd.ms-excel.sheet.macroEnabled.12','multipart/form-data',);
    }
    public function setAllowedExtensionsImagesOfficeFilesVideos(){
        $this->allowedExtensions = array('png', 'jpg', 'jpeg', 'webp', 'PNG', 'JPG', 'JPEG', 'WEBP','doc','docx','xls','xlsx','pdf','mp4','MP4','avi','AVI','mov','MOV','flv','FLV','wmv','WMV','3gp','3GP','mkv','MKV','webm','WEBM','avif','AVIF','heif','HEIF','heic','HEIC','mov','MOV','mpg','MPG','mpeg','MPEG','m4v','M4V','3g2','3GP2','3gpp','3GPP');
        $this->allowedContentTypes = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/jpg', 'image/webp','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/pdf','video/mp4','video/avi','video/mov','video/flv','video/wmv','video/3gp','video/mkv','video/webm','image/avif',
        'video/ogg','video/quicktime','image/heif','image/heic');
    }
    /*
    * This function is used to upload a single file
    * @param $file - The file data
    * @param $path - The path where the file will be stored
    */
    public function uploadSingleFile($file, $path) {
        $fileSize = $file['size'] / 1024;
        $fileTname = $file['tmp_name'];
        $fileType = $file['type'];
        
        // 1. Convertir a UTF-8 y normalizar el nombre
        $fileName = mb_convert_encoding($file['name'], 'UTF-8', 'auto');
        if (function_exists('normalizer_normalize')) {
            $fileName = normalizer_normalize($fileName, Normalizer::FORM_C);
        }
       
        log_message("error","upload_max_filesize: " . ini_get('upload_max_filesize'));
        log_message("error","post_max_size: " . ini_get('post_max_size'));
        
       
        // Validaciones existentes
        $validateExtensionAndContentTypes = $this->validateExtensionAndContentTypes($fileName, $fileType, $this->allowedExtensions, $this->allowedContentTypes);
        if (!$validateExtensionAndContentTypes) {
            return null;
        }
    
        if (!$this->validateSize($fileSize, $this->maxFileSize)) {
            return null;
        }
    
        try {
            // 2. Sanitizar caracteres problemáticos pero mantener caracteres especiales
            $fileName = preg_replace('/[\x00-\x1F\x7F<>:"\/\\|?*]/', '', $fileName);

            $uploadedFilePath = $this->uploadFile($fileTname, $fileName, $path);
            $encodedPath=str_replace("%2F","/",rawurlencode($uploadedFilePath));
            // 3. Codificar la ruta para la URL

            return base_url() . $encodedPath;
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

        // Concatenar el tiempo actual al nombre del archivo
        $name = time() . '_' . $name;
        $destination = $path . $name;


        // Crear una copia del archivo en la ubicación de destino
        if (copy($file, $destination)) {
            return $destination; // Retorna la ruta del archivo copiado
        } else {
            throw new Exception("Failed to copy uploaded file to $destination");
        }
    } catch (Exception $e) {
        log_message('error', 'Failed to copy uploaded file: ' . $e->getMessage());
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
                        if ($product[$keyFile] != "" && $product[$keyFile]!="null" && $product[$keyFile] != null) {
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
                $allowedExtensions = array('png', 'jpg', 'jpeg', 'webp', 'PNG', 'JPG', 'JPEG', 'WEBP','mp4','MP4','avi','AVI','mov','MOV','flv','FLV','wmv','WMV','3gp','3GP','mkv','MKV','webm','WEBM');
                $allowedContentTypes = array('image/png', 'image/jpeg', 'image/pjpeg', 'image/jpg', 'image/webp','video/mp4','video/avi','video/mov','video/flv','video/wmv','video/3gp','video/mkv','video/webm');
                $maxSize = 100240; // 1024 KB = 3 MB

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

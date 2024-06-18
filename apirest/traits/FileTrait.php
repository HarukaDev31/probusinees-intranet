<?php
trait FileTrait {
    /**
     * This function is used to validate the extension and content type of the file
     * and prevent the upload of dangerous files
     * @param $file
     * @param $allowedExtensions
     */
    public function validateExtensionAndContentTypes($file, $allowedExtensions, $allowedContentTypes) {
        $extension = $file->getClientOriginalExtension();
        $contentType = $file->getClientMimeType();
        if (!in_array($extension, $allowedExtensions) || !in_array($contentType, $allowedContentTypes)) {
            return false;
        }
        return true;
    }
    public function validateSize($file, $maxSize) {
        $size = $file->getClientSize();
        if ($size > $maxSize) {
            return false;
        }
        return true;
    }
    public function uploadFile($file, $path) {
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);
        return $fileName;
    }
}
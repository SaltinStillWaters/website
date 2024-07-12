<?php

class File
{
    public static function copyFolder($source, $destination)
    {
        array_map('unlink', glob("$source/*.*"));

        // Check if source is a directory
        if (!is_dir($source)) {
            return false;
        }

        // Create destination directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true); // Recursive directory creation
        }

        // Iterate through files in source directory
        $files = glob($source . '/*');
        foreach ($files as $file) {
            $destFile = $destination . '/' . basename($file);

            if (is_file($file)) {
                copy($file, $destFile); // Copy file
            } elseif (is_dir($file)) {
                self::copyFolder($file, $destFile); // Recursive call for subdirectory
            }
        }

        return true;
    }
}

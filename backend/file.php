<?php

class Files
{
    public static function copyFolder($source, $destination)
    {
        array_map('unlink', glob("$destination/*.*"));


        if (!is_dir($source)) {
            return false;
        }


        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }


        $files = glob($source . '/*');
        foreach ($files as $file) {
            $destFile = $destination . '/' . basename($file);

            if (is_file($file)) {
                copy($file, $destFile);
            } elseif (is_dir($file)) {
                self::copyFolder($file, $destFile);
            }
        }

        return true;
    }
}

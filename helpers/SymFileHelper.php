<?php

/**
 * ###############################################################################
 * @author Pawel Brzozowski <pb@human-device.com>
 * @copyright Copyright (c) 2016, Human Device Sp. z o.o. https://human-device.com
 * ###############################################################################
 */
 
namespace helpers;

use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;

/**
 * File system helper with additional symlinks handling
 */
class SymFileHelper extends BaseFileHelper
{
    
    /**
     * Creates a new directory with path symlinks.
     * See BaseFileHelper::createDirectory() for details.
     *
     * @param array $symlinks list of symlinks to be traveresed or created.
     * List of key => value pairs where key is directory basename and value is symlink target.
     * Symlink target path will be created using createDirectory() if necessary.
     * @param string $path path of the directory to be created.
     * @param integer $mode the permission to be set for the created directory.
     * @param boolean $recursive whether to create parent directories if they do not exist.
     * @return boolean whether the directory is created successfully
     * @throws \yii\base\Exception if the directory could not be created (i.e. php error due to parallel changes)
     */
    public static function createSymDirectory($symlinks, $path, $mode = 0775, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        // recurse if parent dir does not exist and we are not at the root of the file system.
        if ($recursive && !is_dir($parentDir) && $parentDir !== $path) {
            static::createSymDirectory($symlinks, $parentDir, $mode, true);
        }
        try {
            $symlink = ArrayHelper::remove($symlinks, basename($path));
            if (!empty($symlink)) {
                if (!is_dir($symlink)) {
                    if (!static::createDirectory($symlink, $mode, $recursive)) {
                        return false;
                    }
                }
                if (!symlink($symlink, $path)) {
                    return false;
                }
            } elseif (!mkdir($path, $mode)) {
                return false;
            }
        } catch (\Exception $e) {
            if (!is_dir($path)) {// https://github.com/yiisoft/yii2/issues/9288
                throw new \yii\base\Exception("Failed to create directory \"$path\": " . $e->getMessage(), $e->getCode(), $e);
            }
        }
        try {
            return chmod($path, $mode);
        } catch (\Exception $e) {
            throw new \yii\base\Exception("Failed to change permissions for directory \"$path\": " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}

<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class FileUtil
{
    public static function tempUrlOf($file) {
        return $file?->temporaryUrl();
    }

    public static function storageUrl($file): string
    {
        if(str($file)->startsWith('http')) return $file;
        return Storage::url($file);
    }

    public static function nameOf($file) {
        return gettype($file) == 'string' ? $file : $file?->getClientOriginalName();
    }

    public static function originalNameOf($file) {
        return $file?->getFilename();
    }

    public static function typeOf($file) {
        return $file?->getMimeType();
    }

    public static function sizeOf($file):string {
        $size = $file?->getSize();
        return isset($size) ? self::convertToReadableSize($size) : 'unknown';
    }

    public static function convertToReadableSize($size): string
    {
        $base = log($size) / log(1024);
        $suffix = array("B", "KB", "MB", "GB", "TB");
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }
}
/*
__construct"
  1 => "getPath"
  2 => "isValid"
  3 => "getSize"
  4 => "getMimeType"
  5 => "getFilename"
  6 => "getRealPath"
  7 => "getClientOriginalName"
  8 => "temporaryUrl"
  9 => "isPreviewable"
  10 => "readStream"
  11 => "exists"
  12 => "get"
  13 => "delete"
  14 => "storeAs"
  15 => "generateHashNameWithOriginalNameEmbedded"
  16 => "extractOriginalNameFromFilePath"
  17 => "createFromLivewire"
  18 => "canUnserialize"
  19 => "unserializeFromLivewireRequest"
  20 => "serializeForLivewireResponse"
  21 => "serializeMultipleForLivewireResponse"
  22 => "fake"
  23 => "store"
  24 => "storePublicly"
  25 => "storePubliclyAs"
  26 => "clientExtension"
  27 => "createFromBase"
  28 => "getClientOriginalExtension"
  29 => "getClientMimeType"
  30 => "guessClientExtension"
  31 => "getError"
  32 => "move"
  33 => "getMaxFilesize"
  34 => "getErrorMessage"
  35 => "guessExtension"
  36 => "getContent"
  37 => "getExtension"
  38 => "getBasename"
  39 => "getPathname"
  40 => "getPerms"
  41 => "getInode"
  42 => "getOwner"
  43 => "getGroup"
  44 => "getATime"
  45 => "getMTime"
  46 => "getCTime"
  47 => "getType"
  48 => "isWritable"
  49 => "isReadable"
  50 => "isExecutable"
  51 => "isFile"
  52 => "isDir"
  53 => "isLink"
  54 => "getLinkTarget"
  55 => "getFileInfo"
  56 => "getPathInfo"
  57 => "openFile"
  58 => "setFileClass"
  59 => "setInfoClass"
  60 => "__toString"
  61 => "__debugInfo"
  62 => "_bad_state_ex"
  63 => "path"
  64 => "extension"
  65 => "hashName"
  66 => "macro"
  67 => "mixin"
  68 => "hasMacro"
  69 => "flushMacros"
  70 => "__callStatic"
  71 => "__call"
 */

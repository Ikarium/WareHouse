<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple optimized function that detect the mime type of given file.
 *
 * @version   0.1.2
 * @since     0.1.2
 * @author    Benyamna Karim <eva.axis@gmail.com>
 * @copyright CC-BY-NC-SA 3.0
 */
namespace Hestia\Tools;

function scanDirRecurse($path, $recursive = true)
{
    if($path === null) throw new \InvalidArgumentException('Path empty.');
    $lists = scandir($path);

    $name = array();
    if(!empty($lists))
    {
        foreach($lists as $file)
        {
            if($file != '..' && $file != '.')
            {
                $file = $path . $file;

                if(is_dir($file))
                {
                    if($recursive === true) $name = array_merge($name, scanDirRecurse($file . '/', true));
                    elseif(is_numeric($recursive) && $recursive > 0 )
                        $name = array_merge($name, scanDirRecurse($file . '/', $recursive - 1));
                }
                else
                {
                    array_push($name, $file);
                }
            }
        }
    }
    return $name;
}
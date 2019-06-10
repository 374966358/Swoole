<?php

namespace Anthony\Helper;

class Dir
{
    public static function tree($dir, $filter = '')
    {
        try {
            $dir = rtrim(str_replace('//', DS, $dir), DS) . DS;

            $list = [];

            $dirs = [$dir];

            while(count($dirs) > 0)
            {
                $file = array_pop($dirs);

                $children = scandir($file);

                foreach ($children as $child) {
                    if ($child === '.' || $child === '..') {
                        continue;
                    }

                    $path = $file . $child;

                    if (is_dir($path)) {
                        array_push($dirs, $path . DS);
                    } elseif (is_file($path)) {
                        if (!empty($filter)) {
                            if (!preg_match($filter, $path)) {
                                continue;
                            } else {
                                array_push($list, $path);
                            }
                        } else {
                            array_push($list, $path);
                        }
                    }
                }
            }

            return $list;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function scanfiles($dir) {
    if (! is_dir ( $dir ))
     return array ();
       
       // 兼容各操作系统
        $dir = rtrim ( str_replace ( '\\', '/', $dir ), '/' ) . '/';
          
          // 栈，默认值为传入的目录
           $dirs = array ( $dir );
             
             // 放置所有文件的容器
              $rt = array ();
                
                do {
                // 弹栈
                 $dir = array_pop ( $dirs );
                   
                   // 扫描该目录
                    $tmp = scandir ( $dir );
                      var_dump($tmp);die;
                      foreach ( $tmp as $f ) {
                        // 过滤. ..
                          if ($f == '.' || $f == '..')
                            continue;
                               
                                 // 组合当前绝对路径
                                   $path = $dir . $f;
                                      
                                         
                                           // 如果是目录，压栈。
                                             if (is_dir ( $path )) {
                                               array_push ( $dirs, $path . '/' );
                                                 } else if (is_file ( $path )) { // 如果是文件，放入容器中
                                                   $rt [] = $path;
                                                     }
                                                      }
                                                        
                                                         } while ( $dirs ); // 直到栈中没有目录
                                                           
                                                            return $rt;
                                                            }
}


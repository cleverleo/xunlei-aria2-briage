<?php

/**
 * Created by PhpStorm.
 * User: leoliang
 * Date: 16/4/2
 * Time: 下午11:46
 */
class XunleiLixian
{
    public static $cli_path = './lx';

    public static function getTasks($path = '')
    {
        $rs = [];

        exec(sprintf('%s list %s --completed --download-url', static::$cli_path, $path ? escapeshellarg($path) : ''), $rs);

        return array_map(function ($line) use ($path) {
            $line_arr = explode(' ', $line);

            $count = count($line_arr);

            $link = $line_arr[$count - 1];

            return [
                'task_id' => endsWith($path, '/') ? $path . $line_arr[0] : $line_arr[0],
                'file_name' => join('_', array_slice($line_arr, 1, $count - 3)),
                'status' => $line_arr[$count - 2],
                'link' => $link === 'None' ? null : $link
            ];
        }, $rs);
    }

    public static function addTasks($url)
    {
        $rs = [];

        exec(sprintf('%s add %s', static::$cli_path, escapeshellarg($url)), $output, $rs);

        $arr = explode(' ', $output[1]);

        if ($arr[1] !== 'completed') {
            throw new Exception('The Xunlei Not Cache');
        }

        return $arr[0];
    }

    public static function login()
    {
        exec(sprintf('%s login', static::$cli_path), $output, $rs);

        if ($rs !== 0) {
            throw new Exception('The Xunlei Login Fail');
        }
    }

    public static function getInfo()
    {
        exec(sprintf('%s info', static::$cli_path), $output, $rs);

        $out = [];
        foreach ($output as $line) {
            $arr = explode(':', $line);
            $out[trim($arr[0])] = trim($arr[1]);
        }

        return $out;
    }

    public static function downloadById($id, $path = '')
    {
        if (!$tasks = static::getTasks($id)) {
            throw new Exception('The Task Id Not Exist ' . $id);
        }


        $out = [];
        foreach ($tasks as $task) {
            $file_path = $path . '/' . $task['file_name'];

            if ($task['link'] === null) {
                $out = array_merge($out, static::downloadById($task['task_id'] . '/', $file_path));
            } else {
                $out[] = ['download_link' => $task['link'], 'file_path' => $file_path];
            }
        }

        return $out;
    }

    public static function downloadByUrl($url)
    {
        return static::downloadById(static::addTasks($url));
    }
}

function endsWith($haystack, $needle)
{
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}
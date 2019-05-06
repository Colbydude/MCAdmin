<?php

/**
 * Read n lines from the end of a file
 * @param  string $file
 * @param  int    $lines
 * @param  int    $fsize
 * @return string
 */
function file_backread($file, $lines, &$fsize = 0)
{
    $f = fopen($file, 'r');

    if (!$f) {
        return Array();
    }

    $splits = $lines * 50;

    if ($splits > 10000) {
        $splits = 10000;
    }

    $fsize = filesize($file);
    $pos = $fsize;
    $buff1 = Array();
    $cnt = 0;

    while ($pos) {
        $pos = $pos - $splits;

        if ($pos<0) {
            $splits += $pos;
            $pos = 0;
        }

        fseek($f, $pos);
        $buff = fread($f, $splits);

        if (!$buff) {
            break;
        }

        $lines -= substr_count($buff, "\n");

        if ($lines <= 0) {
            $buff1[] = __file_backread_helper($buff, "\n", abs($lines) + 1);
            break;
        }

        $buff1[] = $buff;
    }

    return str_replace("\r", '', implode('', array_reverse($buff1)));
}

/**
 * Helper for file_backread function
 * @param  string $haystack
 * @param  string $needle
 * @param  int    $x
 * @return string|bool
 */
function __file_backread_helper(&$haystack, $needle, $x)
{
    $pos = 0;
    $cnt = 0;

    while ($cnt < $x && ($pos = strpos($haystack, $needle, $pos)) !== false) {
        $pos++;
        $cnt++;
    }

    return $pos == false ? false : substr($haystack, $pos, strlen($haystack));
}

/**
 * Parses text with control codes and returns what may be usable HTML.
 * Original author: http://theperfectbeast.blogspot.com.es/2013/10/minecraft-server-log-web-interface.html
 * Modified heavily to work better
 *
 * @param  string  $str
 * @return string
 */
function mclogparse2($str) {
    $lines = explode("\n", $str);

    foreach ($lines as &$line) {
        // Remap problem characters
        $line = preg_replace("/</", "&lt;", $line);
        $line = preg_replace("/>/", "</span>&gt;", $line);

        // Remove unrequired formatting codes
        $line = str_replace(array("[m", "[21m", "[3m"), "", $line);

        // Split log line into sections to using first formatting code style
        $segarray = preg_split('/(\[0(?![0-9])|\[m)/', $line); // such lookaround :D - Alanaktion

        for ($i = 1; $i < count($segarray); ++$i) {
            // Do replace to add styled spans
            if (preg_match('/;\d{2};\d+m/', $segarray[$i])) {
                $segarray[$i] = preg_replace("/;30/", "<span class='mc-black", $segarray[$i]);
                $segarray[$i] = preg_replace("/;31/", "<span class='mc-red", $segarray[$i]);
                $segarray[$i] = preg_replace("/;32/", "<span class='mc-green", $segarray[$i]);
                $segarray[$i] = preg_replace("/;33/", "<span class='mc-gold", $segarray[$i]);
                $segarray[$i] = preg_replace("/;34/", "<span class='mc-blue", $segarray[$i]);
                $segarray[$i] = preg_replace("/;35/", "<span class='mc-purple", $segarray[$i]);
                $segarray[$i] = preg_replace("/;36/", "<span class='mc-aqua", $segarray[$i]);
                $segarray[$i] = preg_replace("/;37/", "<span class='mc-gray", $segarray[$i]);
                $segarray[$i] = preg_replace("/;22m/", "'>", $segarray[$i]);
                $segarray[$i] = preg_replace("/;1m/", " mc-bold'>", $segarray[$i]);
                $segarray[$i] .= "</span>";
            }
        }

        // Rejoin then split log line using second formatting code style
        $line = implode("", $segarray);
        $segarray = preg_split('/§/', $line);

        for ($i = 1; $i < count($segarray); ++$i) {
            // Do replace to add styled spans
            $segarray[$i] = preg_replace("/^0/", "<span class='mc-black'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^1/", "<span class='mc-dark-blue'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^2/", "<span class='mc-dark-green'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^3/", "<span class='mc-dark-aqua'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^4/", "<span class='mc-dark-red'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^5/", "<span class='mc-dark-purple'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^6/", "<span class='mc-gold'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^7/", "<span class='mc-gray'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^8/", "<span class='mc-dark-gray'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^9/", "<span class='mc-blue'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^a/", "<span class='mc-green'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^b/", "<span class='mc-aqua'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^c/", "<span class='mc-red'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^d/", "<span class='mc-purple'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^e/", "<span class='mc-yellow'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^f/", "<span class='mc-white'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^l/", "<span class='mc-bold'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^m/", "<span class='mc-strikethrough'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^n/", "<span class='mc-underline'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^o/", "<span class='mc-italic'>", $segarray[$i]);
            $segarray[$i] = preg_replace("/^r/", "<span class='mc-reset'>", $segarray[$i]);
            $segarray[$i] .= "</span>";
        }

        $line = implode("", $segarray);

        // Format prefixes nicely
        $line = preg_replace("/^(\[[0-9\:]+\] \[(Server thread|[A-Za-z0-9# -])\/(ERROR|WARN|INFO)\]:)/", "<span class='log-info mc-gray'>$1</span>", $line);
        $line = preg_replace(array("/(\[(Server thread|[A-Za-z0-9# -])\/WARN\])/", "/(\[(Server thread|[A-Za-z0-9# -])\/ERROR\])/"), array("<span class='mc-gold'>$1</span>", "<span class='mc-red'>$1</span>"), $line);

        // Remove excessive "Server thread" prefix
        $line = str_replace("[Server thread/", "[", $line);

        // Remove remaining control bytes
        $line = str_replace("\x1B", "", $line);

        // Wrap line in a div.
        $line = '<div class="console-line">' . $line . '</div>';
    }

    return implode("\n", $lines);
}

<?php

namespace Core\Utils;

use Exception;

class Vite
{

    static function vite(string $entry): string
    {
        return "\n" . self::jsTag($entry)
            . "\n" . self::jsPreloadImports($entry)
            . "\n" . self::cssTag($entry);
    }

    static function URL(){
        if(!file_exists(APP_PATH.'config/vite.php')){
            throw new Exception('Vite file config not found ');
        }
        $config = require APP_PATH.'config/vite.php';
        $host = isset($config ['host'] ) ? $config ['host'] : "localhost";
        $port = isset($config ['port'] ) ? $config ['port'] : 5133;

        return "http://{$host}:{$port}";
    }

    static function isDev(string $entry): bool
    {
        static $exists = null;
        if ($exists !== null) {
            return $exists;
        }
        $handle = curl_init(self::URL() . '/' . $entry);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);

        curl_exec($handle);
        $error = curl_errno($handle);
        curl_close($handle);

        // return $exists = !$error;
        return true;
    }


    // Helpers to print tags

    static function jsTag(string $entry): string
    {

        if(self::isDev($entry)){
            $refresh = '
            <script>
                window.$RefreshReg$ = () => {}
                window.$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__= true
            </script>
            <script type="module">
                import RefreshRuntime from "'.self::URL().'/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
            </script>';
            // $vite_client =  $refresh.'<script type="module" src="'.self::URL().'/@vite/client "></script>';
            return  $refresh.'
            <script type="module" crossorigin src="'
                . self::URL() . '/' . $entry
            . '"></script>';
        }else{
            return  '<script type="module" crossorigin src="'
            . self::assetUrl($entry)  
            . '"></script>';
        }
        
    }

    static function jsPreloadImports(string $entry): string
    {
        if (self::isDev($entry)) {
            return '';
        }

        $res = '';
        foreach (self::importsUrls($entry) as $url) {
            $res .= '<link rel="modulepreload" href="'
                . $url
                . '">';
        }
        return $res;
    }

    static function cssTag(string $entry): string
    {
        // not needed on dev, it's inject by Vite
        if (self::isDev($entry)) {
            return '';
        }

        $tags = '';
        foreach (self::cssUrls($entry) as $url) {
            $tags .= '<link rel="stylesheet" href="'
                . $url
                . '">';
        }
        return $tags;
    }


    // Helpers to locate files

    static function getManifest(): array
    {
        $content = file_get_contents(DIR . 'public/js/bundle/manifest.json');
        return json_decode($content, true);
    }

    static function assetUrl(string $entry): string
    {
        $manifest = self::getManifest();

        return isset($manifest[$entry])
            ? '/js/bundle/' . $manifest[$entry]['file']
            : '';
    }

    static function importsUrls(string $entry): array
    {
        $urls = [];
        $manifest = self::getManifest();

        if (!empty($manifest[$entry]['imports'])) {
            foreach ($manifest[$entry]['imports'] as $imports) {
                $urls[] = '/js/bundle/' . $manifest[$imports]['file'];
            }
        }
        return $urls;
    }

    static function cssUrls(): array
    {
        $urls = [];
        $manifest = self::getManifest();

        if (!empty($manifest["style.css"])) {
            $urls[] = '/js/bundle/' . $manifest["style.css"]['file'];
        }
        return $urls;
    }
}

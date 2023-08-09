<?php

namespace Core\Renderer;


class Template2
{

    protected array $blocks = array();
    protected string $cache_path = DIR . 'storage/cache/';
    protected $cache_enabled;
    protected $ENV;
    private static array $functions = [];
    protected $extensions = [];
    protected $customDirectives = [];

    public function __construct($template, $cache_enabled = !DEBUG)
    {
        $this->ENV = $template;
        $this->cache_enabled = $cache_enabled;
    }

    public function render($file, $data = array())
    {
        $cached_file = $this->cache($file);
        $data = array_merge($data, self::$functions);
        extract($data, EXTR_SKIP);
        require $cached_file;
        if (!$this->cache_enabled) {
            register_shutdown_function('unlink', $cached_file);
        }
    }

    public static function addFunction($name, $function)
    {
        self::$functions[$name] = $function;
    }

    protected function cache($file)
    {
        if (!file_exists($this->cache_path)) {
            mkdir($this->cache_path, 0744);
        }
        $cached_file = $this->cache_path . str_replace('/', '_', uniqid() . uniqid()) . '.php';
        if (!$this->cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($file)) {
            $filepath = str_replace('.', DIRECTORY_SEPARATOR, $file) . '.php';
            $code = $this->includeFiles($filepath);
            $code = $this->compileCode($code);
            file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
        }
        return $cached_file;
    }

    protected function clearCache()
    {
        foreach (glob($this->cache_path . '*') as $file) {
            unlink($file);
        }
    }

    protected function compileCode($code)
    {
        $code = $this->compileBlock($code);
        $code = $this->compileYield($code);
        $code = $this->compileEscapedEcho($code);
        $code = $this->compilePhpBlocks($code);
        $code = $this->compileFilter($code);
        $code = $this->compileEcho($code);
        $code = $this->compilePhp($code);
        return $code;
    }

    protected function includeFiles($file)
    {
        $code = file_get_contents($this->ENV . $file);
        preg_match_all('/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $code, $matches, PREG_SET_ORDER);
        // preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', $code, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            if($value[1] === "extends"){
                $file = rtrim($value[4], "'");
            }
            $code = str_replace($value[0], $this->includeFiles($value[2]), $code);
        }
        $code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
        return $code;
    }

    protected function compilePhp($code)
    {
        return preg_replace('~\{%\s*(.+?)\s*\%}~is', '<?php $1 ?>', $code);
    }

    protected function compileFilter($code)
    {
        $data =  preg_replace_callback('~\{{\s*(.+?)\s*\}}~is', function ($match) {
            $array_variables = explode('|', $match[1]);
            if (count($array_variables) == 2) {
                $var = trim($array_variables[0]);
                $params = array();
                if (strpos($array_variables[1], '(') !== false) {
                    $funcstring = substr(trim($array_variables[1]), 0, -1);
                    $parts = array_map("trim", explode("(", $funcstring, 2));
                    $func = $parts[0];
                    if (count($parts) > 1) {
                        $params = eval("return [$parts[1]];");
                    }
                } else {
                    $func = trim($array_variables[1]);
                }
                if (count($params) != 0) {
                    $args = "$" . $var . ",'" . implode('\',\'', $params) . "'";
                } else {
                    $args = "$" . $var;
                }
                return "<?php echo $$func($args)?>";
            } else {
                return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?php echo $$1 ?>', $match[0]);
            }
        }, $code);

        return $data;
    }

    protected function compileEcho($code)
    {
        return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?php echo $1 ?>', $code);
    }

    protected function compileEscapedEcho($code)
    {
        return preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo htmlentities($$1, ENT_QUOTES, \'UTF-8\') ?>', $code);
    }

    protected function compileBlock($code)
    {
        preg_match_all('/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            if (!array_key_exists($value[1], $this->blocks)) {
                $this->blocks[$value[1]] = '';
            }
            if (strpos($value[2], '@parent') === false) {
                $this->blocks[$value[1]] = $value[2];
            } else {
                $this->blocks[$value[1]] = str_replace('@parent', $this->blocks[$value[1]], $value[2]);
            }
            $code = str_replace($value[0], '', $code);
        }
        return $code;
    }

    protected function compilePhpBlocks($value)
    {
        return preg_replace_callback('/(?<!@)@php(.*?)@endphp/s', function ($matches) {
            return "<?php{$matches[1]}?>";
        }, $value);
    }



    protected function compileStatements($value)
    {
        /*return preg_replace_callback(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            function ($match) {
                return $this->compileStatement($match);
            },
            $value
        );*/
        return preg_replace_callback('/(?<!@)@(.*?)(.*?)@end(.*?)/s', function ($matches) {
            return $matches[1];
        }, $value);
    }
    protected function callCustomDirective($name, $value)
    {
        if (str_starts_with($value, '(') && str_ends_with($value, ')')) {
            $value = substr($value, 1, -1);
        }
        return call_user_func($this->customDirectives[$name], trim($value));
    }

    protected function compileStatement($match)
    {
        if (method_exists($this, $method = 'compile' . ucfirst($match[1]))) {
            $match[0] = $this->$method($match[3]);
        } elseif (strpos($match[0], '@') > -1) {
            $match[0] = isset($match[3]) ? $match[1] . $match[3] : $match[1];
        } elseif (isset($this->customDirectives[$match[1]])) {
            $match[0] = $this->callCustomDirective($match[1], $match[3]);
        }
        return isset($match[3]) ? $match[0] : $match[0] . $match[2];
    }

    protected function compileYield($code)
    {
        foreach ($this->blocks as $block => $value) {
            $code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
        }
        $code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
        return $code;
    }
}

<?php

    namespace Core\Renderer;

    use TemplateFunctionInterface;

    class Template {

        protected $blocks = array();
        protected $cache_path = DIR.'storage/cache/';
        protected $cache_enabled;
        protected $ENV;
        protected  $functions = [];
        
        public function __construct($template,$cache_enabled=FALSE){
            $this->ENV = $template; 
            $this->cache_enabled = $cache_enabled; 
        }

        public function view($file, $data = array()) {
            $cached_file = $this->cache($file);
            $data = array_merge($data,$this->functions);
            extract($data, EXTR_SKIP);
            require $cached_file;
        }

        public function addFunction($name,$function){
            $this->functions[$name] = $function;
        }
        

        protected function cache($file) {
            if (!file_exists($this->cache_path)) {
                mkdir($this->cache_path, 0744);
            }
            $cached_file = $this->cache_path .str_replace('/','_',$file).'.php';
            if (!$this->cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($file)) {
                $filepath = str_replace('.',DIRECTORY_SEPARATOR,$file).'.php';
                $code = $this->includeFiles($filepath);
                $code = $this->compileCode($code);
                file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
            }
            return $cached_file;
        }

        protected function clearCache() {
            foreach(glob($this->cache_path . '*') as $file) {
                unlink($file);
            }
        }

        protected function compileCode($code) {
            $code = $this->compileBlock($code);
            $code = $this->compileYield($code);
            $code = $this->compileEscapedEcho($code);
            $code = $this->compileFilter($code);
            // $code = $this->compileEcho($code);
            $code = $this->compilePHP($code);
            return $code;
        }

        protected function includeFiles($file) {
            $code = file_get_contents($this->ENV.$file);
            preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', $code, $matches, PREG_SET_ORDER);
            foreach ($matches as $value) {
                $code = str_replace($value[0], $this->includeFiles($value[2]), $code);
            }
            $code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
            return $code;
        }

        protected function compilePHP($code) {
            return preg_replace('~\{%\s*(.+?)\s*\%}~is', '<?php $1 ?>', $code);
        }

        protected function compileFilter($code) {
            $varibale = preg_replace_callback('~\{{\s*(.+?)\s*\}}~is', function($match){
                $array_varibales = explode('|',$match[1]);
                if(count($array_varibales) ==2){
                    $var = trim($array_varibales[0]);
                    $params = array();
                    if(strpos($array_varibales[1], '(') !== false){
                        $funcstring = substr(trim($array_varibales[1]), 0, -1);
                        $parts = array_map("trim", explode("(", $funcstring, 2));
                        $func = $parts[0];
                        if(count($parts) > 1){
                            $params = eval("return [$parts[1]];");
                        }
                    }
                    else{
                        $func = trim($array_varibales[1]);
                    }
                    if(count($params) != 0){       
                        $args = "$".$var.",'".implode('\',\'',$params)."'";
                    }
                    else{
                        $args = "$".$var;
                    }
                    return "<?php echo $$func($args)?>";
                }
                else{
                    return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?php echo $$1 ?>', $match[0]);
                }
            }, $code);

            return $varibale;
        }

        protected function compileEcho($code) {
            return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?php echo $$1 ?>', $code);
        }

        protected function compileEscapedEcho($code) {
            return preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo htmlentities($$1, ENT_QUOTES, \'UTF-8\') ?>', $code);
        }

        protected function compileBlock($code) {
            preg_match_all('/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER);
            foreach ($matches as $value) {
                if (!array_key_exists($value[1], $this->blocks)) $this->blocks[$value[1]] = '';
                if (strpos($value[2], '@parent') === false) {
                    $this->blocks[$value[1]] = $value[2];
                } else {
                    $this->blocks[$value[1]] = str_replace('@parent', $this->blocks[$value[1]], $value[2]);
                }
                $code = str_replace($value[0], '', $code);
            }
            return $code;
        }

        protected function compileYield($code) {
            foreach($this->blocks as $block => $value) {
                $code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
            }
            $code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
            return $code;
        }

    }

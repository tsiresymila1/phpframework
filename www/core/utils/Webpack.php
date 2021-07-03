<?php
    namespace Core\Utils;
    use Twig\Extension\AbstractExtension;
    use Twig\Markup;
    use Twig\TwigFunction;

    class Webpack extends AbstractExtension {

        public function getFunctions()
        {
            return [
                new TwigFunction('webpack_build_js', [$this, 'loadjs']),
                new TwigFunction('webpack_build_css', [$this, 'loadcss']),
            ];
        }

        public function loadjs(String $name)
        {
            return new Markup('<script src="/build/js/'.$name.'.bundle.js" type="text/javascript"></script>','UTF-8') ;
        }
        public function loadcss(String $name)
        {
            return new Markup('<link href="/build/css/'.$name.'.css" type="text/css" rel="stylesheet" ></script>','UTF-8') ;
        }
    }

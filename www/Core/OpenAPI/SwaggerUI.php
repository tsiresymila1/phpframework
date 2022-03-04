<?php

namespace Core\OpenAPI;

class SwaggerUI
{
    public string $resource = DIRECTORY_SEPARATOR . 'swagger-ui/dist/';

    public function __construct()
    {
        $this->resource = dirname(__FILE__) . $this->resource;
    }

    public function loadFavicon($size = "16x16")
    {
        $content = file_get_contents($this->resource . 'favicon-' . $size . '.png');
        $type = pathinfo($this->resource, PATHINFO_EXTENSION);
        return  'data:image/' . $type . ';base64,' .base64_encode($content);
    }

    public function loadCss()
    {
        return file_get_contents($this->resource . 'swagger-ui.css');
    }

    public function loadJs()
    {
        $bundle = file_get_contents($this->resource . 'swagger-ui-bundle.js');
        $preset = file_get_contents($this->resource . 'swagger-ui-standalone-preset.js');
        return array('bundle' => $bundle, 'preset' => $preset);
    }

    public static function renderer($url)
    {
        $ins = new SwaggerUI();
        $css = $ins->loadCss();
        $js = $ins->loadJs();
        $ic16 = $ins->loadFavicon();
        $ic32 = $ins->loadFavicon('32x32');
        $spec = json_encode(OpenApi::getSPec(),JSON_PRETTY_PRINT);
        return <<<HTML
            <!DOCTYPE html>
            <html lang="en">
              <head>
                <meta charset="UTF-8">
                <title>PHP FRAMEWORK API DOCS</title>
                <link rel="icon" type="image/png" href="${ic32}" sizes="32x32" />
                <link rel="icon" type="image/png" href="${ic16}" sizes="16x16" />
                <style>
                 ${css}
                </style>
                <style>
                  html
                  {
                    box-sizing: border-box;
                    overflow: -moz-scrollbars-vertical;
                    overflow-y: scroll;
                  }
            
                  *,
                  *:before,
                  *:after
                  {
                    box-sizing: inherit;
                  }
            
                  body
                  {
                    margin:0;
                    background: #fafafa;
                  }
                </style>
              </head>
            
              <body>
                <div id="swagger-ui"></div>
                <script>${js['bundle']}</script>
                <script>${js['preset']}</script>
                <script>
                window.onload = function() {
                  const ui = SwaggerUIBundle({
                    dom_id: '#swagger-ui',
                    deepLinking: true,
                    presets: [
                      SwaggerUIBundle.presets.apis,
                      SwaggerUIStandalonePreset
                    ],
                    spec: ${spec},
                  })                
                  window.ui = ui;
                };
              </script>
              </body>
            </html>
        HTML;
    }
}

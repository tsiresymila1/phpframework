<?php

namespace Core\Http\Exception;


class ErrorRender
{
    public static array $code = array(
        "500" => 'Internal Server Error',
        "501" => 'Not Implemented',
        "502" => 'Bad Gateway',
        "508" => 'Loop Detected',
        "401" => "Unauthorized",
        "403" => "Forbidden",
        "404" => "Route Not Found",
        "405" => "Method Not Allowed"
    );

    /**
     * @param int $code
     * @param string $message
     * @param bool $iscontent
     * @return false|string
     */
    public static function showError($code=500, $message="Internal server error", $iscontent=false)
    {

        $content = <<<HTML
            <!DOCTYPE>
            <html>
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
            
                <title>PHP FRAMEWORK</title>
            
                <style id="" media="all">
            
                    * {
                        -webkit-box-sizing: border-box;
                        box-sizing: border-box
                    }
            
                    body {
                        padding: 0;
                        margin: 0
                    }
            
                    #notfound {
                        position: relative;
                        height: 100vh;
                        background: #030005
                    }
            
                    #notfound .notfound {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        -webkit-transform: translate(-50%, -50%);
                        -ms-transform: translate(-50%, -50%);
                        transform: translate(-50%, -50%)
                    }
            
                    .notfound {
                        max-width: 767px;
                        width: 100%;
                        line-height: 1.4;
                        text-align: center
                    }
            
                    .notfound .notfound-404 {
                        position: relative;
                        height: 180px;
                        margin-bottom: 20px;
                        z-index: -1
                    }
            
                    .notfound .notfound-404 h1 {
                        font-family: montserrat, sans-serif;
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        -webkit-transform: translate(-50%, -50%);
                        -ms-transform: translate(-50%, -50%);
                        transform: translate(-50%, -50%);
                        font-size: 224px;
                        font-weight: 900;
                        margin-top: 0;
                        margin-bottom: 0;
                        margin-left: -12px;
                        color: #030005;
                        text-transform: uppercase;
                        text-shadow: -1px -1px 0 #8400ff, 1px 1px 0 #ff005a;
                        letter-spacing: -20px
                    }
            
                    .notfound .notfound-404 h2 {
                        font-family: montserrat, sans-serif;
                        position: absolute;
                        left: 0;
                        right: 0;
                        top: 110px;
                        font-size: 42px;
                        font-weight: 700;
                        color: #fff;
                        text-transform: uppercase;
                        text-shadow: 0 2px 0 #8400ff;
                        letter-spacing: 13px;
                        margin: 0
                    }
            
                    .notfound a {
                        font-family: montserrat, sans-serif;
                        display: inline-block;
                        text-transform: uppercase;
                        color: #ff005a;
                        text-decoration: none;
                        border: 2px solid;
                        background: 0 0;
                        padding: 10px 40px;
                        font-size: 14px;
                        font-weight: 700;
                        -webkit-transition: .2s all;
                        transition: .2s all
                    }
            
                    .notfound a:hover {
                        color: #8400ff
                    }
            
                    @media only screen and (max-width: 767px) {
                        .notfound .notfound-404 h2 {
                            font-size: 24px
                        }
                    }
            
                    @media only screen and (max-width: 480px) {
                        .notfound .notfound-404 h1 {
                            font-size: 182px
                        }
                    }
                </style>
            </head>
            <body>
            <div id="notfound">
                <div class="notfound">
                    <div class="notfound-404">
                        <h1>${code}</h1>
                        <h2>${message}</h2>
                    </div>
                </div>
            </div>
            </body>
            </html>
        HTML;
        if($iscontent){
            return $content;
        }
        else{
            ob_start();
            header('Content-Type: text/html');
            echo $content;
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

    }

    /**
     * @param string $title
     * @param string $message
     * @param int $code
     * @param bool $iscontent
     * @return false|string
     */
    public static function showErrorDetails($title="", $traces=[], $code=500, $iscontent=false)
    {
        $messages_content = '';
        foreach($traces as $k => $t){
            $st = isset($t['class'] ) ? $t['class'] : '';
            $st.= isset($t['class']) ? $t['type']: '';
            $file = isset($t['file']) ? $t['file'].' on line '.$t['line'] . ': ' : '';
            $messages_content.='<div style="padding:4px 2px;"><code>#'.$k.' '.$file. $st.$t['function'].'()</code></div>';
        }
        $content = <<<HTML
            <!DOCTYPE>
            <html>
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
            
                <title>PHP FRAMEWORK</title>
            
                <style id="" media="all">
            
                    * {
                        -webkit-box-sizing: border-box;
                        box-sizing: border-box
                    }
            
                    body {
                        padding: 0;
                        margin: 0
                    }
            
                    #error {
                        position: relative;
                        height: 100vh;
                        background: #030005
                    }
            
                    #error .error {
                        left: 50%;
                        top: 50%;
                    }
            
                    .error {
                        width: 100%;
                        line-height: 1.4;
                    }
            
                    .error .error-404 {
                        position: relative;
                        margin-bottom: 20px;
                        color: white;
                        padding-left: 6vw;
                         padding-right: 6vw;
                         padding-top: 20px;
                    }
                    .error .error-header {
                        background-color: #d62748;
                        color: white;
                        padding:20px 60px;
                        display: flex
                    }
            
                    .error .error-header h1 {
                        font-family: montserrat, sans-serif;                
                        font-size: 22px;
                        font-weight: 600;
                        margin-top: 0;
                        margin-bottom: 0;
                        color: white;
                    }
                    .error .error-header h2 {
                        font-family: montserrat, sans-serif;                
                        font-size: 16px;
                        font-weight: 600;
                        margin-top: 0;
                        margin-bottom: 0;
                        color: white;
                    }
            
                    @media only screen and (max-width: 767px) {
                        .error .error-404 h2 {
                            font-size: 24px
                        }
                    }
            
                    @media only screen and (max-width: 480px) {
                        .error .error-404 h1 {
                            font-size: 16px
                        }
                    }
                </style>
            </head>
            <body>
            <div id="error">
                <div class="error">
                    <div class="error-header">
                        <div style="height:60px;width:8vw">
                        <svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" width="100" height="64" viewBox="0 0 640.00001 480.00001" id="svg2" version="1.1" inkscape:version="0.91 r13725" sodipodi:docname="bug.svg">
                              <sodipodi:namedview id="base" pagecolor="#ffffff" bordercolor="#666666" borderopacity="1.0" inkscape:pageopacity="0.0" inkscape:pageshadow="2" inkscape:zoom="1.04375" inkscape:cx="320" inkscape:cy="240" inkscape:document-units="px" inkscape:current-layer="layer1" showgrid="false" units="px" inkscape:window-width="1280" inkscape:window-height="968" inkscape:window-x="0" inkscape:window-y="30" inkscape:window-maximized="1"/>
                                  <defs id="defs4"/>
                                  <metadata id="metadata7">
                                    <rdf:RDF>
                                      <cc:Work rdf:about="">
                                        <dc:format>image/svg+xml</dc:format>
                                        <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>
                                        <dc:title/>
                                      </cc:Work>
                                    </rdf:RDF>
                                  </metadata>
                                  <g inkscape:groupmode="layer" id="layer2" inkscape:label="Layer 2" style="display:inline">
                                    <path style="stroke-width:4;stroke-miterlimit:4;stroke-dasharray:none" inkscape:transform-center-x="21.466042" inkscape:transform-center-y="0.39577698" d="m 465.83586,87.77965 c -4.34266,3.619711 -19.63151,76.36345 -65.46965,114.62744 -53.70468,44.83068 -138.54943,56.21014 -134.96952,61.77756 4.777,7.42912 228.26792,-95.06938 354.46232,-148.63536 49.09452,-20.83924 -119.28558,29.17268 -118.25015,32.49938 3.69695,11.87775 -231.66882,91.39 -230.08039,103.44592 1.01222,7.68256 105.81682,12.26692 181.63482,17.73681 43.16012,3.11379 79.42423,7.37048 76.80642,10.93712 -40.33027,54.94813 -249.7642,-41.92225 -253.13507,-30.36957 -2.17548,7.45582 95.21538,50.89048 163.20002,83.74351 37.35655,18.05226 67.95788,35.01502 64.03581,36.84716 -92.09588,43.02117 -233.21009,-109.97725 -241.21133,-100.54565 -5.59387,6.59387 92.55251,90.71863 149.11545,143.56682 24.34238,22.7437 -8.66961,36.93621 -11.31371,39.42186 -8.78799,8.26138 -129.09524,-176.50439 -139.45896,-169.97752 -6.36182,4.00655 31.09738,85.57001 58.21835,148.81795 17.06041,39.78603 -15.48505,22.67182 -20.08747,24.09868 -11.91888,3.69513 -14.93912,-180.29404 -27.12809,-177.84857 -12.18897,2.44547 -15.86752,173.57375 -28.19722,173.7843 -4.65814,0.0795 8.70515,33.12901 11.05243,-11.74688 3.86579,-73.9069 11.52122,-172.12504 4.15554,-173.91592 -11.83809,-2.87829 -90.46007,205.97484 -94.30093,194.57802 -45.19222,-134.097 -0.60262,-8.073 20.66937,-39.80694 38.24037,-57.04765 77.04004,-140.15652 71.20431,-144.71961 -9.39681,-7.34758 19.33286,28.53674 11.18158,19.36965 -8.15128,-9.1671 2.40309,-11.55643 -4.07784,-21.85326 -6.48092,-10.29684 -70.12896,32.48602 -73.46957,20.47584 -3.34062,-12.0102 53.31678,-19.35405 51.26138,-31.31579 C 239.62835,250.81086 2.647932,249.47414 3.546715,237.35771 4.445501,225.24129 242.56108,262.93409 244.93005,251.27273 247.29903,239.61137 16.066058,173.99472 21.255889,163.01103 26.44572,152.02735 260.29934,270.81371 267.72527,261.02857 272.83513,254.29531 188.08125,170.1035 137.52738,115.46476 114.61359,90.6995 109.75979,227.86004 101.41658,69.34768 100.77918,57.23793 257.4541,263.36178 268.55385,257.74147 276.36563,253.78599 224.36152,140.3934 196.75151,71.95092 185.13047,43.14354 137.52738,115.46476 181.1322,20.930224 186.18554,9.974674 255.96912,243.01046 268.31805,241.72277 280.66699,240.43508 225.68323,34.760123 237.86991,36.149303 c 4.17972,0.47645 41.43948,-0.87413 38.31792,35.327227 -5.97988,69.34978 -18.42792,170.21511 -10.79711,172.19355 7.91937,2.05327 49.12484,-97.49455 77.87273,-159.26608 13.41239,-28.81964 -40.71128,1.61551 27.45673,-47.636087 9.65175,-6.97341 -99.9777,192.089777 -108.64793,201.054877 -22.15603,22.90953 63.64243,21.47458 135.5414,-35.77871 C 531.32955,95.565833 330.21589,-4.3116933 545.58418,21.251514 573.8257,24.603651 497.35512,61.50757 465.83586,87.77965 Z" id="path4172" inkscape:connector-curvature="0" sodipodi:nodetypes="sssssssssssssssssssssssssssssssssssssssssssssss"/>
                                  </g>
                                  <g inkscape:label="Layer 1" inkscape:groupmode="layer" id="layer1" transform="translate(0,-572.36216)" style="display:inline">
                                    <ellipse style="fill:#ea0a35;fill-rule:evenodd;stroke:#000000;stroke-width:4;stroke-linecap:butt;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1;fill-opacity:1" id="path4148" cx="269.64294" cy="823.40491" rx="122.7526" ry="101.37434"/>
                                    <ellipse style="fill:#ea0a35;fill-rule:evenodd;stroke:#000000;stroke-width:4;stroke-linecap:butt;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" id="path4150" cx="213.9234" cy="835.71643" rx="71.720619" ry="77.237595" transform="matrix(0.95120318,-0.30856524,0.30856524,0.95120318,0,0)"/>
                                    <path style="display:inline;fill:#000000;stroke:none" d="m 185.30638,793.66172 0,1.73675 -2.99471,0 0,-11.87287 26.95236,0 0,5.93644 -19.46559,0 c -2.5106,-0.008 -4.46463,2.05527 -4.49206,4.19968 z m 12.77051,42.87597 c -1.12261,6.07354 -0.79186,12.09554 -0.79169,18.22513 l -14.97353,0 0,-15.17088 c -0.0151,-10.3056 13.77227,-20.41655 26.95236,-20.44773 l 0,5.93644 c -6.15338,0 -9.86291,5.95043 -11.18714,11.45704 z m -18.75992,21.26175 c 1.72454,-0.0276 3.02454,-1.45805 2.9947,-3.03662 l 14.97353,0 c 0.051,1.66349 1.37895,2.92911 2.99471,2.96822 l 0,2.96822 -20.96294,0 0,-2.89982 z m 29.94706,-38.65523 c 7.87852,-0.0838 11.73658,-8.04542 11.88627,-14.84108 0.14969,-6.79567 -4.29285,-14.84109 -11.88627,-14.84109 0,-1.93998 0,-3.99646 0,-5.93644 11.96931,0.028 26.84557,7.10731 26.84141,20.85998 -0.004,13.75267 -14.82329,20.68885 -26.84141,20.69507 0,-1.99495 0,-3.94149 0,-5.93644 z m 32.82856,-29.68217 14.97353,0 0,65.30078 -14.97353,0 0,-65.30078 z m 14.97353,29.68217 14.97353,0 0,5.93644 -14.97353,0 0,-5.93644 z m -17.96824,-32.65039 0,-2.96822 20.96294,0 0,2.96822 c -1.65524,0.0276 -2.97835,1.32097 -2.9947,2.96822 l -14.97353,0 c -0.051,-1.69784 -1.42516,-2.94056 -2.99471,-2.96822 z m 0,71.30562 c 1.72454,-0.0276 3.02455,-1.45805 2.99471,-3.03662 l 14.97353,0 c 0.051,1.66349 1.37895,2.92911 2.9947,2.96822 l 0,2.96822 -20.96294,0 0,-2.89982 z m 62.88883,-68.3374 0,65.30078 -14.97353,0 0,-65.30078 14.97353,0 z m -14.97353,29.68217 0,5.93644 -14.97353,0 0,-5.93644 14.97353,0 z m 17.96823,-32.65039 c -1.56955,0.0277 -2.94373,1.27038 -2.9947,2.96822 l -14.97353,0 c -0.0164,-1.64725 -1.33947,-2.9406 -2.99471,-2.96822 l 0,-2.96822 20.96294,0 0,2.96822 z m 0,71.30562 0,2.89982 -20.96294,0 0,-2.96822 c 1.61576,-0.0391 2.94374,-1.30473 2.99471,-2.96822 l 14.97353,0 c -0.0299,1.57857 1.27016,3.009 2.9947,3.03662 z m 5.9157,-68.3374 14.97353,0 0,65.30078 -14.97353,0 0,-65.30078 z m -2.99471,-3.03662 0,-2.89982 32.94177,0 0,5.93644 -29.94706,0 c -0.007,-1.99608 -0.94031,-3.01414 -2.99471,-3.03662 z m 0,71.37402 c 1.72454,-0.0276 3.02455,-1.45805 2.99471,-3.03662 l 14.97353,0 c 0.051,1.66349 1.37896,2.92911 2.9947,2.96822 l 0,2.96822 -20.96294,0 0,-2.89982 z m 31.15326,-32.71517 c -2.23459,0.1196 -3.86114,0.60186 -4.26992,2.96459 l -2.92569,0 0,-11.87286 2.95089,0 c 0.301,2.22425 1.58962,3.11229 3.88191,2.94009 l 2.15132,0.0543 0,5.91022 -1.78851,0.004 z m 1.78851,-5.94006 c 7.87852,-0.0838 11.73659,-8.04542 11.88628,-14.84108 0.14969,-6.79567 -4.29286,-14.84109 -11.88628,-14.84109 0,-1.93998 0,-3.99646 0,-5.93644 11.96932,0.028 26.84558,7.10731 26.84141,20.85998 -0.004,13.75267 -14.82329,20.68885 -26.84141,20.69507 0,-1.99495 0,-3.94149 0,-5.93644 z" id="path4248" inkscape:connector-curvature="0"/>
                                    <path id="path4306" d="m 418.58925,741.06154 8.32302,-3.01727 6.03455,16.64604 -8.32303,3.01727 -6.03454,-16.64604 z m -3.01726,-8.32303 8.32302,-3.01727 0.49875,1.37578 c 1.51725,-1.80184 3.60786,-2.78194 5.73119,-3.67275 l 0.75163,2.07333 c -3.38745,1.67591 -4.72766,4.94954 -4.00575,8.56169 l -8.28158,3.00225 -3.01726,-8.32303 z m 29.00758,27.00144 c -0.51664,0.25079 -1.26199,0.58067 -1.65629,0.73318 l -0.71692,0.27723 -0.73578,-2.02958 c 1.12088,-0.6031 2.26263,-1.19398 2.91786,-2.28378 0.84638,-1.40775 1.36035,-2.98612 1.18837,-4.68772 -0.0477,-1.74211 -0.76175,-3.59133 -1.80375,-4.97556 -1.60089,-2.02828 -4.11713,-3.45121 -6.75246,-2.88882 l -0.86734,0.16824 -0.73576,-2.02958 c 0.12677,-0.046 0.25353,-0.0919 0.3803,-0.13787 3.17204,-1.36274 4.81936,-5.13759 3.59901,-8.36736 -1.1704,-3.22852 -4.79203,-5.13948 -8.12492,-4.11717 l -0.38031,0.13786 -0.7543,-2.08077 0.49163,-0.17715 c 0.82578,-0.29723 2.59723,-0.68679 3.7856,-0.83184 4.04544,-0.23643 9.70143,0.41899 11.15517,4.83128 0.96483,2.66141 -0.0817,5.39766 -1.77565,7.56341 -0.49709,0.60884 -1.06872,1.16488 -1.56876,1.75502 0.19104,0.0155 0.38175,0.0594 0.57304,0.0476 0.82697,-0.052 2.90445,0.16695 4.01626,0.42235 2.78096,0.6406 5.56687,2.23433 6.49696,5.06142 1.00634,2.77592 0.0614,5.7211 -1.71496,8.0082 -1.67241,2.19884 -4.25617,4.26126 -7.01699,5.60115 z m -32.02485,-35.32447 8.32303,-3.01726 3.01726,8.32302 -8.32302,3.01727 -3.01727,-8.32303 z M 401.78655,714.1939 c 7.12554,-2.5606 16.38917,-0.17058 19.0912,7.20432 l -8.32303,3.01726 c -1.49589,-4.11168 -5.28418,-9.8618 -10.01386,-8.14082 l -0.75431,-2.08076 z m 0.74276,2.04886 c -6.19922,2.75998 -8.13776,7.84984 -6.65642,14.22023 l -2.04498,0.74134 -3.77159,-10.40377 2.05451,-0.7448 c 0.33951,0.65779 0.74073,1.38301 1.5439,1.47854 0.33808,-0.0346 0.32837,-0.0217 1.32973,-1.74196 1.54501,-2.67954 4.04031,-4.38446 6.78972,-5.63255 l 0.75513,2.08297 z m 23.55239,45.4864 -1.45791,-4.02158 8.28157,-3.00223 c 1.76111,3.23666 4.886,4.88949 8.56172,4.00574 l 0.75161,2.07333 c -2.20091,0.67681 -4.4339,1.2641 -6.75334,0.85314 l 0.49874,1.37578 -10.40378,3.77159 -0.73692,-2.03281 c 1.044,-0.76127 1.72682,-1.72001 1.25831,-3.02296 z m 64.83784,-28.05515 -8.32302,3.01727 -6.03455,-16.64605 8.32303,-3.01727 6.03454,16.64605 z m 1.4579,4.02157 c 0.47566,1.30034 1.61386,1.59894 2.90318,1.51433 l 0.73693,2.0328 -10.40377,3.7716 -0.49875,-1.37579 c -1.51726,1.80185 -3.60784,2.78195 -5.73119,3.67276 l -0.75162,-2.07333 c 3.38815,-1.67703 4.72791,-4.9484 4.00574,-8.56171 l 8.28158,-3.00223 1.4579,4.02157 z m -23.67532,-0.64815 c 1.44411,3.96007 5.36459,9.81407 9.92646,8.1805 l 0.75407,2.08006 c -7.89673,2.85188 -15.56433,2.32758 -18.34783,-5.35055 l -5.29021,-14.3655 8.31694,-3.01506 4.6406,12.47053 z m -4.62471,-12.4763 -8.32302,3.01728 c -0.52755,-1.45628 -2.64578,-3.67752 -4.76976,-2.95334 -1.24387,0.50699 -1.18999,1.78374 -1.66255,2.80019 l -2.29803,-0.83934 c 0.033,-3.26989 2.63083,-6.10027 5.44165,-7.23722 5.13469,-2.07691 10.0275,0.8904 11.61171,5.21243 z m 21.37893,-12.48607 0.74492,2.05484 c -1.11047,0.47128 -1.70797,1.8227 -1.31629,2.88807 l -8.32302,3.01727 -3.01529,-8.31756 11.90968,0.35738 z m 33.49212,-4.8554 5.21736,14.39188 -8.32303,3.01728 -5.50831,-15.1945 c -1.15661,-3.19045 -5.15595,-6.89144 -9.03143,-5.43087 l -0.75432,-2.08077 c 7.32489,-2.61214 15.31154,-3.10047 18.39973,5.29698 z m 8.23462,22.71491 -8.32303,3.01728 -0.49874,-1.37579 c -1.51726,1.80184 -3.60783,2.78193 -5.73119,3.67275 l -0.75161,-2.07332 c 3.38745,-1.6759 4.72768,-4.94954 4.00574,-8.56171 l 8.28157,-3.00224 3.01726,8.32303 z m 3.01728,8.32302 -8.32303,3.01727 -3.01728,-8.32301 8.32303,-3.01728 3.01728,8.32302 z m -10.0394,17.76477 -0.75429,-2.08076 c 4.73376,-1.7097 3.95724,-8.55166 2.47066,-12.66674 l 8.32303,-3.01727 c 2.65166,7.39316 -2.92831,15.16423 -10.0394,17.76477 z m -0.74274,-2.04887 0.75512,2.08297 c -2.91051,0.80375 -5.91866,1.09394 -8.82187,0.027 -1.87103,-0.67884 -1.8554,-0.6753 -2.13709,-0.48517 -0.55537,0.58803 -0.39866,1.4019 -0.23772,2.12444 l -2.05452,0.74479 -3.77158,-10.40377 2.04497,-0.74136 c 2.94511,5.83977 7.695,8.50494 14.22272,6.65113 z m -27.39559,-30.25757 c -3.03964,-8.10045 -0.0276,-18.69236 8.5261,-21.79324 l 0.73449,2.02606 c -4.34459,1.63095 -2.59848,12.16878 -0.88443,16.86199 1.71404,4.69323 7.20175,13.97393 11.48452,12.37801 l 0.73449,2.02604 c -8.8709,3.13187 -17.55551,-3.39841 -20.59517,-11.49886 z" style="fill:#000000;stroke:none" inkscape:connector-curvature="0" inkscape:transform-center-x="12.054617" inkscape:transform-center-y="-5.1048972"/>
                                    <ellipse style="fill:#000000;fill-opacity:1;stroke-width:4;stroke-miterlimit:4;stroke-dasharray:none" id="path4317" cx="480.47903" cy="695.95496" rx="15.808383" ry="11.497006"/>
                                    <ellipse style="display:inline;fill:#000000;fill-opacity:1;stroke-width:4;stroke-miterlimit:4;stroke-dasharray:none" id="path4317-4" cx="439.7605" cy="684.45795" rx="15.808383" ry="11.497006"/>
                                    <ellipse style="display:inline;fill:#000000;fill-opacity:1;stroke-width:4;stroke-miterlimit:4;stroke-dasharray:none" id="path4317-5" cx="269.22159" cy="755.35614" rx="15.808383" ry="11.497006"/>
                                    <ellipse style="display:inline;fill:#000000;fill-opacity:1;stroke-width:4;stroke-miterlimit:4;stroke-dasharray:none" id="path4317-1" cx="215.56888" cy="883.73944" rx="15.808383" ry="11.497006"/>
                                  </g>
                            </svg>
                        </div>
                        <div>
                        <h1>Error ${code}</h1>
                        <h2>${title}</h2>
                        </div>
                    </div>
                    <div class="error-404">
                        ${messages_content}
                    </div>
                </div>
            </div>
            </body>
            </html>
        HTML;
        if($iscontent){
            return $content;
        }
        else{
            ob_start();
            header('Content-Type: text/html');
            echo $content;
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

    }
}
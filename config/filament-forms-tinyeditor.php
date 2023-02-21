<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profiles
    |--------------------------------------------------------------------------
    |
    | You can add as many as you want of profiles to use it in your application.
    |
    */

    'profiles' => [

        'default' => [
            'plugins' => 'advlist autoresize codesample directionality emoticons fullscreen hr image imagetools link lists media table toc wordcount',
            'toolbar' => 'undo redo removeformat | formatselect fontsizeselect | bold italic | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist | forecolor backcolor | blockquote table toc hr | image link media codesample emoticons | wordcount fullscreen',
            'upload_directory' => null,

        ],

        'custom' => [
            'plugins' => 'code advlist autoresize codesample directionality emoticons fullscreen hr link lists media table toc wordcount',
            'toolbar' => 'code undo redo removeformat | formatselect fontsizeselect | bold italic | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist | forecolor backcolor | blockquote table toc hr | link codesample emoticons | wordcount fullscreen',
            'upload_directory' => null,
            'custom_configs' => [
                'entity_encoding' => 'raw',
                'extended_valid_elements' => 'a[*],altGlyph[*],altGlyphDef[*],altGlyphItem[*],animate[*],animateMotion[*],animateTransform[*],circle[*],clipPath[*],color-profile[*],cursor[*],defs[*],desc[*],ellipse[*],feBlend[*],feColorMatrix[*],feComponentTransfer[*],feComposite[*],feConvolveMatrix[*],feDiffuseLighting[*],feDisplacementMap[*],feDistantLight[*],feFlood[*],feFuncA[*],feFuncB[*],feFuncG[*],feFuncR[*],feGaussianBlur[*],feImage[*],feMerge[*],feMergeNode[*],feMorphology[*],feOffset[*],fePointLight[*],feSpecularLighting[*],feSpotLight[*],feTile[*],feTurbulence[*],filter[*],font[*],font-face[*],font-face-format[*],font-face-name[*],font-face-src[*],font-face-uri[*],foreignObject[*],g[*],glyph[*],glyphRef[*],hkern[*],image[*],line[*],linearGradient[*],marker[*],mask[*],metadata[*],missing-glyph[*],mpath[*],path[*],pattern[*],polygon[*],polyline[*],radialGradient[*],rect[*],script[*],set[*],stop[*],style[*],svg[*],switch[*],symbol[*],text[*],textPath[*],title[*],tref[*],tspan[*],use[*],view[*],vkern[*],a[*],animate[*],animateMotion[*],animateTransform[*],circle[*],clipPath[*],defs[*],desc[*],discard[*],ellipse[*],feBlend[*],feColorMatrix[*],feComponentTransfer[*],feComposite[*],feConvolveMatrix[*],feDiffuseLighting[*],feDisplacementMap[*],feDistantLight[*],feDropShadow[*],feFlood[*],feFuncA[*],feFuncB[*],feFuncG[*],feFuncR[*],feGaussianBlur[*],feImage[*],feMerge[*],feMergeNode[*],feMorphology[*],feOffset[*],fePointLight[*],feSpecularLighting[*],feSpotLight[*],feTile[*],feTurbulence[*],filter[*],foreignObject[*],g[*],hatch[*],hatchpath[*],image[*],line[*],linearGradient[*],marker[*],mask[*],metadata[*],mpath[*],path[*],pattern[*],polygon[*],polyline[*],radialGradient[*],rect[*],script[*],set[*],stop[*],style[*],svg[*],switch[*],symbol[*],text[*],textPath[*],title[*],tspan[*],use[*],view[*],g[*],animate[*],animateColor[*],animateMotion[*],animateTransform[*],discard[*],mpath[*],set[*],circle[*],ellipse[*],line[*],polygon[*],polyline[*],rect[*],a[*],defs[*],g[*],marker[*],mask[*],missing-glyph[*],pattern[*],svg[*],switch[*],symbol[*],desc[*],metadata[*],title[*],feBlend[*],feColorMatrix[*],feComponentTransfer[*],feComposite[*],feConvolveMatrix[*],feDiffuseLighting[*],feDisplacementMap[*],feDropShadow[*],feFlood[*],feFuncA[*],feFuncB[*],feFuncG[*],feFuncR[*],feGaussianBlur[*],feImage[*],feMerge[*],feMergeNode[*],feMorphology[*],feOffset[*],feSpecularLighting[*],feTile[*],feTurbulence[*],font[*],font-face[*],font-face-format[*],font-face-name[*],font-face-src[*],font-face-uri[*],hkern[*],vkern[*],linearGradient[*],radialGradient[*],stop[*],circle[*],ellipse[*],image[*],line[*],path[*],polygon[*],polyline[*],rect[*],text[*],use[*],use[*],feDistantLight[*],fePointLight[*],feSpotLight[*],clipPath[*],defs[*],hatch[*],linearGradient[*],marker[*],mask[*],metadata[*],pattern[*],radialGradient[*],script[*],style[*],symbol[*],title[*],hatch[*],linearGradient[*],pattern[*],radialGradient[*],solidcolor[*],a[*],circle[*],ellipse[*],foreignObject[*],g[*],image[*],line[*],path[*],polygon[*],polyline[*],rect[*],svg[*],switch[*],symbol[*],text[*],textPath[*],tspan[*],use[*],g[*],circle[*],ellipse[*],line[*],path[*],polygon[*],polyline[*],rect[*],defs[*],g[*],svg[*],symbol[*],use[*],altGlyph[*],altGlyphDef[*],altGlyphItem[*],glyph[*],glyphRef[*],textPath[*],text[*],tref[*],tspan[*],altGlyph[*],textPath[*],tref[*],tspan[*],clipPath[*],cursor[*],filter[*],foreignObject[*],hatchpath[*],script[*],style[*],view[*],altGlyph[*],altGlyphDef[*],altGlyphItem[*],animateColor[*],cursor[*],font[*],font-face[*],font-face-format[*],font-face-name[*],font-face-src[*],font-face-uri[*],glyph[*],glyphRef[*],hkern[*],missing-glyph[*],tref[*],vkern[*]',
            ],
        ],
        'simple' => [
            'plugins' => 'autoresize directionality emoticons link wordcount',
            'toolbar' => 'removeformat | bold italic | rtl ltr | link emoticons',
            'upload_directory' => null,
        ],

        'template' => [
            'plugins' => 'autoresize template',
            'toolbar' => 'template',
            'upload_directory' => null,
        ],
        /*
        |--------------------------------------------------------------------------
        | Custom Configs
        |--------------------------------------------------------------------------
        |
        | If you want to add custom configurations to directly tinymce
        | You can use custom_configs key as an array
        |
        */

        /*
          'default' => [
            'plugins' => 'advlist autoresize codesample directionality emoticons fullscreen hr image imagetools link lists media table toc wordcount',
            'toolbar' => 'undo redo removeformat | formatselect fontsizeselect | bold italic | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist | forecolor backcolor | blockquote table toc hr | image link media codesample emoticons | wordcount fullscreen',
            'custom_configs' => [
                'allow_html_in_named_anchor' => true,
                'link_default_target' => '_blank',
                'codesample_global_prismjs' => true,
                'image_advtab' => true,
                'image_class_list' => [
                  [
                    'title' => 'None',
                    'value' => '',
                  ],
                  [
                    'title' => 'Fluid',
                    'value' => 'img-fluid',
                  ],
              ],
            ]
        ],
        */

    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | You can add as many as you want of templates to use it in your application.
    |
    | https://www.tiny.cloud/docs/plugins/opensource/template/#templates
    |
    | ex: TinyEditor::make('content')->profiles('template')->templates('example')
    */

    'templates' => [

        'example' => [
            // content
            ['title' => 'Some title 1', 'description' => 'Some desc 1', 'content' => 'My content'],
            // url
            ['title' => 'Some title 2', 'description' => 'Some desc 2', 'url' => 'http://localhost'],
        ],

    ],
];

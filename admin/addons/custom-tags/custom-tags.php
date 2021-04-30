<?php
if ( !defined('K_ENGINE_DIR') ) die(); // cannot be loaded directly
class CustomTags {   
    // Email Guardian
    // https://www.couchcms.com/forum/viewtopic.php?f=8&t=11001
    static function email_guardian( $params, $node ){
        global $FUNCS;
        foreach( $node->children as $child ){
            $html .= $child->get_HTML();
        }
        preg_match_all('/\b[^\s]+@[^\s]+/', strip_tags(htmlspecialchars_decode($html, ENT_QUOTES)), $emails);
        foreach($emails[0] as $email){
            $email = trim(strtolower($email), ".,;:?!\"\'‘’‚“”„‹›-+&#*%$@~`^()|<>[]{}/\\");
            $code = $FUNCS->embed( "<cms:cloak_email email='{$email}' />", $is_code=1 );
            $pos = strpos($html, $email);
            if ($pos !== false) {
                $html = substr_replace($html, $code, $pos, strlen($email));
            }
        }
        return $html;
    }

    // Get Image Size
    // https://www.couchcms.com/forum/viewtopic.php?f=8&t=10386#p26494
    static function get_image_size( $params, $node ){
        if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}
        $image = trim( $params[0]['rhs'] );
        $size = getimagesize($image);
        if (trim( $params[1]['rhs'] ) == 'width'){return $size[0];}
        else if (trim( $params[1]['rhs'] ) == 'height'){return $size[1];}
        else if (trim( $params[1]['rhs'] ) == 'type' || trim( $params[1]['rhs'] ) == 'mime' || trim( $params[1]['rhs'] ) == 'mime-type'){return $size['mime'];}
        else{return $size[3];}
    }
    // Average color of an image
    // https://www.couchcms.com/forum/viewtopic.php?f=8&t=11312
    static function avg_color ( $params, $node ){
        if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}
        $opacity='1';
        $brightness='1';
        foreach($params as $param){
            if (trim($param['lhs']) === 'opacity' ){
                $opacity = trim($param['rhs']);
            }
            if (trim($param['lhs']) === 'brightness' ){
                $brightness = abs(trim($param['rhs']));
            }
        }
        $filename = trim($params[0]['rhs']);    
        $image = imagecreatefromjpeg($filename);
        $width = imagesx($image);
        $height = imagesy($image);
        $pixel = imagecreatetruecolor(1, 1);
        imagecopyresampled($pixel, $image, 0, 0, 0, 0, 1, 1, $width, $height);
        $rgb = imagecolorat($pixel, 0, 0);
        $color = imagecolorsforindex($pixel, $rgb);
        $color['red'] = (intval($color['red'] * $brightness) <= 255 )? intval($color['red'] * $brightness) : 255;
        $color['blue'] = (intval($color['blue'] * $brightness) <= 255 )? intval($color['blue'] * $brightness) : 255;
        $color['green'] = (intval($color['green'] * $brightness) <= 255 )? intval($color['green'] * $brightness) : 255;
        return 'rgba(' . $color['red'] . ', ' . $color['green'] .', ' . $color['blue'] . ', ' .$opacity . ')';
    }

    static function html_minify ( $str, $node ){

        $res = $str[0]['rhs'];
        return trim( preg_replace( "/\s+/" , ' ' , $res ) , " \t\n\r\0\x0B" );
    }

}

$FUNCS->register_tag( 'avg_color', array('CustomTags', 'avg_color') );    
$FUNCS->register_tag( 'get_image_size', array('CustomTags', 'get_image_size') );    
$FUNCS->register_tag( 'email_guardian', array('CustomTags', 'email_guardian') );
$FUNCS->register_tag( 'html_minify', array('CustomTags', 'html_minify') );
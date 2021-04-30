<?php

    ob_start();
    define( 'K_ADMIN', 1 );

    if ( !defined('K_ENGINE_DIR') ) define( 'K_ENGINE_DIR', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/') );

    require_once( K_ENGINE_DIR.'header.php' );
    header( 'Content-Type: text/html; charset='.K_CHARSET );

    $AUTH->check_access( K_ACCESS_LEVEL_ADMIN, 1 );

    $tpl = ( isset($_GET['tpl']) && $FUNCS->is_non_zero_natural($_GET['tpl']) ) ? (int)$_GET['tpl'] : null;
    if( is_null($tpl) ) die( 'No template specified' );
    $fid = ( isset($_GET['fid']) && $FUNCS->is_non_zero_natural($_GET['fid']) ) ? (int)$_GET['fid'] : -1;
    $cid = ( isset($_GET['cid']) && $FUNCS->is_non_zero_natural($_GET['cid']) ) ? (int)$_GET['cid'] : null;
    $rid = ( isset($_GET['rid']) && $FUNCS->is_non_zero_natural($_GET['rid']) ) ? (int)$_GET['rid'] : null;
    $fn = ( isset($_GET['fn']) ) ? $_GET['fn'] : '/';
    $FUNCS->validate_nonce( 'bulk_upload_'.$tpl.'_'.$fid.'_'.$fn );

    // HTTP headers for no cache etc
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    // Settings
    $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
    if( @is_writable($targetDir)!==TRUE ){ //FIX THIS! Will always fail
        $targetDir = $Config['UserFilesAbsolutePath'] . 'tmp';
    }

    $cleanupTargetDir = true; // Remove old files
    $maxFileAge = 5 * 3600; // Temp file age in seconds

    @set_time_limit( 0 );

    // Uncomment this one to fake upload time
    // usleep(5000);

    // Get parameters
    $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
    $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
    $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

    // Clean the fileName for security reasons
    $fileName = preg_replace('/[^\w\._-]+/', '_', $fileName);

    // Make sure the fileName is unique but only if chunking is disabled
    if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
        $ext = strrpos($fileName, '.');
        $fileName_a = substr($fileName, 0, $ext);
        $fileName_b = substr($fileName, $ext);

        $count = 1;
        while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
            $count++;

        $fileName = $fileName_a . '_' . $count . $fileName_b;
    }

    $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

    // Create target dir
    if (!file_exists($targetDir)){
        $oldumask = umask(0);
        @mkdir( $targetDir, 0777 );
        umask( $oldumask );
    }

    // Remove old temp files
    if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
        while (($file = readdir($dir)) !== false) {
            $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

            // Remove temp file if it is older than the max age and is not the current file
            if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                @unlink($tmpfilePath);
            }
        }

        closedir($dir);
    } else
        die('Failed to open temp directory.');


    // Look for the content type header
    if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
        $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

    if (isset($_SERVER["CONTENT_TYPE"]))
        $contentType = $_SERVER["CONTENT_TYPE"];

    // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
    if (strpos($contentType, "multipart") !== false) {
        if (isset($_FILES['NewFile']['tmp_name']) && @is_uploaded_file($_FILES['NewFile']['tmp_name'])) {
            // Open temp file
            $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen($_FILES['NewFile']['tmp_name'], "rb");

                if ($in) {
                    while ($buff = fread($in, 4096)){
                        fwrite($out, $buff);
                    }
                } else{
                    die('Failed to open input stream.');
                }
                fclose($in);
                fclose($out);
                @unlink($_FILES['NewFile']['tmp_name']);
            } else{
                die('Failed to open output stream.');
            }
        } else{
            die('Failed to move uploaded file.');
        }
    } else {
        // Open temp file
        $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
        if ($out) {
            // Read binary input stream and append it to temp file
            $in = fopen("php://input", "rb");

            if ($in) {
                while ($buff = fread($in, 4096)){
                    fwrite($out, $buff);
                }
            } else{
                die('Failed to open input stream.');
            }

            fclose($in);
            fclose($out);
        } else{
            die('Failed to open output stream.');
        }
    }

    // Check if complete file has been uploaded
    if (!$chunks || $chunk == $chunks - 1) {
        @unlink( $filePath ); // delete any previous tmp file of this name
        // Strip the temp .part suffix off
        @rename("{$filePath}.part", $filePath);

        // Time to move the temp file to the right folder
        $_FILES['NewFile']['name'] = $fileName;
        $_FILES['NewFile']['size'] = @filesize( $filePath );
        $_FILES['NewFile']['tmp_name'] = $filePath;
        $_FILES['NewFile']['error'] = 0;

        // get our existing uploader into action
        define( 'K_GALLERY_UPLOAD', 1 );
        require_once( K_ENGINE_DIR. 'includes/fileuploader/connector.php' );

        $_GET['Type'] = 'Image';
        // create destination folder if required
        $fn = trim( implode('/', array_map(array($FUNCS,'get_clean_url'), explode('/', $fn))), '/' );
        $fpath = $Config['UserFilesAbsolutePath'] . 'image/';
        $fpath .= ( $fn ) ? $fn . '/' : '';
        $res = CreateServerFolder( $fpath );
        if( $res ) die( $res );
        // move the file
        global $_K_IMAGE; // will contain either full url of the uploaded image or error
        $_GET['Command'] = 'FileUpload';
        $_GET['CurrentFolder'] = $fn;
        DoResponse();

        // success?
        if( $FUNCS->is_error($_K_IMAGE) ){
            die( $_K_IMAGE->err_msg );
        }

        // Move on to create a cloned page using the uploaded image
        $path_parts = $FUNCS->pathinfo( $fileName );
        $res = create_cloned_page( $tpl, $fid, $cid, $rid, $path_parts['filename'], $_K_IMAGE );
        if( $FUNCS->is_error($res) ){
            die( $res->err_msg );
        }

    }

    function create_cloned_page( $tpl_id, $fid, $cid, $rid, $page_title, $img_url ){
        global $FUNCS;

        // create a single cloned page
        $pg = new KWebpage( $tpl_id, -1 );

        if( $pg->error ){
            return $FUNCS->raise_error( $pg->err_msg );
        }
        // fill fields
        $f = &$pg->_fields['k_page_title']; // title
        $f->store_posted_changes( $page_title );
        unset( $f );
        $f = &$pg->_fields['k_page_folder_id']; // folder
        $f->store_posted_changes( $fid );
        unset( $f );
        $f = &$pg->_fields['k_publish_date']; // publish date
        $f->store_posted_changes( $FUNCS->get_current_desktop_time() );
        unset( $f );

        // find the image field (set 'required' off for all other fields as we go)
        // also find the relation field if specified
        if( $cid && $rid ) $find_related=1;
        for( $x=0; $x<count($pg->fields); $x++ ){
            $f = &$pg->fields[$x];
            if( !$f->system ){
                if( $f->k_type=='image' && $f->name=='gg_image' ){
                    $f->store_posted_changes( $img_url );
                }
                // related?
                if( $find_related ){
                    if( $f->id==$rid && $f->k_type=='relation'){
                        $f->store_posted_changes( $cid );
                        $find_related=0;
                    }
                }
            }
            $f->required = 0;
            unset( $f );
        }

        // save
        $errors = $pg->save();
        if( $errors ){
            $sep = '';
            $str_err = '';
            for( $x=0; $x<count($pg->fields); $x++ ){
                $f = &$pg->fields[$x];
                if( $f->err_msg ){
                    $str_err .= $sep . '<b>' . $f->name . ':</b> ' . $f->err_msg;
                    $sep = '<br/>';
                }
            }
            return $FUNCS->raise_error( $str_err );
        }
        $page_id = $pg->id;
        $pg->destroy();
        unset( $pg );
        return $page_id;
    }

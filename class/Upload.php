<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2017/5/26
 * Time: 17:47
 */

class Upload
{
    public function __construct()
    {

        global $segments;
        $ac = $segments['ac'];
        $this->img = array("image/jpeg", "image/gif", "image/png", "image/bmp", "image/pjpeg", "image/x-png");
        $this->zip = array("application/zip");
        $this->flash = array("application/x-shockwave-flash", "flv-application/octet-stream");
        $this->video = array("mp4","rm","rmvb","wmv","avi","mp4","3gp","mkv","flv","f4v");
    }

    //反馈上传图片

    public function indexFunc($files)
    {
        $images = array();
        foreach ($files as $key => $val) {
            if($val){
                $arr = $this->uploadRunPhoneSS($val, 'uploader/dev/' . date("Y") . '/' . date('m') . '/');
                if($arr['code'] == 2){
                    api_message(20002,$arr['mes']);
                } else if($arr['code'] == 1){
                    $images[$key]=$arr['url'];
                }
            }

        }
        return $images;
    }

    public function index2Func()
    {

        $params = array();
        param_request(array('from_start' => "INT"), '', $params, array('from_start' => 0));
        $from_start = 10;
        $file = $_FILES['file'];
        $path_info = pathinfo($file['name']);
        $file_extension = $path_info["extension"];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = strtolower(finfo_file($finfo, $file["tmp_name"]));
            finfo_close($finfo);
        } else {
            $mimetype = strtolower(mime_content_type($file['tmp_name']));
        }
        $extension = array();
        array_push($extension, $mimetype, $file_extension);
        switch ($extension) {
            case in_array($extension[0], $this->img) == true:
                $arr = $this->uploadRun($_FILES['file'], 'uploader/dev/' .date("Y") . '/' . date('m') . '/');
                break;
            case in_array($extension[0], $this->zip) == true:
                $arr = $this->uploadZip(!empty($_FILES['file']) ? $_FILES['file'] : $_FILES['Filedata'], ROOT . "/log/",  date("Y") . '/' . date('m') . '/');
                break;
            case in_array($extension[0], $this->flash) == true:
                $arr = $this->uploadFlash(!empty($_FILES['file']) ? $_FILES['file'] : $_FILES['Filedata'],  date("Y") . '/' . date('m') . '/');
                break;
            case in_array($extension[1], $this->video) == true:
                $arr = $this->uploadVideo($_FILES['file'],  date("Y") . '/' . date('m') . '/', $from_start);
                break;
            default:
                $arr = array('mes' => '文件类型错误:', 'code' => 2);
        }
        echo json_encode($arr);
        exit;
    }

    public function imagesFunc(){
        $arr = $this->uploadRunImages($_FILES['file'], 'uploader/dev/' . date("Y") . '/' . date('m') . '/');
        echo json_encode($arr);
        exit;
    }

    public function fileFunc()
    {
        $arr = $this->uploadZip(!empty($_FILES['file']) ? $_FILES['file'] : $_FILES['Filedata'], ROOT . "/log/", date("Y") . '/' . date('m') . '/');
        echo json_encode($arr);
        exit;
    }

    public function flashFunc()
    {
        $arr = $this->uploadFlash(!empty($_FILES['file']) ? $_FILES['file'] : $_FILES['Filedata'],  date("Y") . '/' . date('m') . '/');

        echo json_encode($arr);
        exit;
    }

    public function videoFunc()
    {
        $params = array();
        param_request(array('from_start' => "INT"), '', $params, array('from_start' => 0));
        $from_start = 10;

        $arr = $this->uploadVideo($_FILES['file'],  date("Y") . '/' . date('m') . '/', $from_start);

        echo json_encode($arr);
        exit;

    }

    public function gameScreenFunc()
    {
        $arr = $this->uploadRunPhoneSS($_FILES['file'], 'uploader/dev/' . date("Y") . '/' . date('m') . '/');
        echo json_encode($arr);
        exit;
    }

    public function uploadVideo($file, $save_path, $from_start)

    {
        global $_SC;
        $max_file_size_in_bytes = 209715200;                // 2M in bytes
        $uploadErrors = array(
            0 => "文件上传成功",
            1 => "上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置",
            2 => "上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置",
            3 => "上传的文件仅为部分文件",
            4 => "没有文件上传",
            6 => "缺少临时文件夹"
        );
        if (!isset($file)) {
            return array('mes' => '找不到文件', 'code' => 2);
        } else if (isset($file["error"]) && $file["error"] != 0) {
            return array('mes' => $uploadErrors[$file["error"]], 'code' => 2);
        } else if (!isset($file["tmp_name"]) || !@is_uploaded_file($file["tmp_name"])) {
            return array('mes' => '文件无法上传', 'code' => 2);
        } else if (!isset($file['name'])) {
            return array('mes' => '文件名不存在', 'code' => 2);
        }
        $file_size = @filesize($file["tmp_name"]);
        if (!$file_size || $file_size > $max_file_size_in_bytes) {
            return array('mes' => '文件size太大', 'code' => 2);
        }
        if ($file_size <= 0) {
            return array('mes' => '文件大小不能为0', 'code' => 2);
        }
        $path_info = pathinfo($file['name']);
        $file_extension = $path_info["extension"];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = strtolower(finfo_file($finfo, $file["tmp_name"]));
            finfo_close($finfo);
        } else {
            $mimetype = strtolower(mime_content_type($file['tmp_name']));
        }

        if (!in_array($file_extension, Ad_Img::$videoMime)) {
            return array('mes' => '文件类型错误:', 'code' => 2);
        }
        $fmd5 = md5_file($file["tmp_name"]);
        $file_name = $save_path . $fmd5 . '.' . $file_extension;
        $ftp = new Ftp($_SC['ftp_host'], $_SC['ftp_port'], $_SC['ftp_user'], $_SC['ftp_pass']);
        if ($ftp->up_file($file["tmp_name"], $file_name)) {
            $video_img_name = $save_path . $fmd5 . ".jpg";
            $video_img_size = Ad_Img::$type_size[36];
            $video_img_size = explode("_", $video_img_size);
            $ftp_path = Ad_User::$ftp_path;
            $this->convertToFlv($ftp_path['path'] . $file_name, $ftp_path['path'] . $video_img_name, $from_start, $video_img_size[0], $video_img_size[1]);
            return array('mes' => '文件上传成功', 'code' => 1, 'video_url' => $file_name, 'img_url' => $video_img_name);
        } else {
            return array('mes' => $file_name, 'code' => 2);
        }
    }

    //生成视频截图
    public function convertToFlv($input, $output, $start_time, $width, $height)
    {
        $command = "ffmpeg -v 0 -y -i " . $input . " -vframes 1 -ss  " . $start_time . "  -t 0.001 -f mjpeg -s " . $width . "x" . $height . "  $output ";
        exec($command);
    }

    public function uploadFlash($file, $save_path)
    {
        global $_SC;
        $max_file_size_in_bytes = 2097152;                // 2M in bytes
        $uploadErrors = array(
            0 => "文件上传成功",
            1 => "上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置",
            2 => "上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置",
            3 => "上传的文件仅为部分文件",
            4 => "没有文件上传",
            6 => "缺少临时文件夹"
        );
        if (!isset($file)) {
            return array('mes' => '找不到文件', 'code' => 2);
        } else if (isset($file["error"]) && $file["error"] != 0) {
            return array('mes' => $uploadErrors[$file["error"]], 'code' => 2);
        } else if (!isset($file["tmp_name"]) || !@is_uploaded_file($file["tmp_name"])) {
            return array('mes' => '文件无法上传', 'code' => 2);
        } else if (!isset($file['name'])) {
            return array('mes' => '文件名不存在', 'code' => 2);
        }
        $file_size = @filesize($file["tmp_name"]);
        if (!$file_size || $file_size > $max_file_size_in_bytes) {
            return array('mes' => '文件size太大', 'code' => 2);
        }
        if ($file_size <= 0) {
            return array('mes' => '文件大小不能为0', 'code' => 2);
        }
        $path_info = pathinfo($file['name']);
        $file_extension = $path_info["extension"];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = strtolower(finfo_file($finfo, $file["tmp_name"]));
            finfo_close($finfo);
        } else {
            $mimetype = strtolower(mime_content_type($file['tmp_name']));
        }
        if (!in_array($mimetype, Ad_Img::$flashMime)) {
            return array('mes' => '文件类型错误:', 'code' => 2);
        }
        $fmd5 = md5_file($file["tmp_name"]);
        $file_name = $save_path . $fmd5 . '.' . $file_extension;
        $ftp = new Ftp($_SC['ftp_host'], $_SC['ftp_port'], $_SC['ftp_user'], $_SC['ftp_pass']);
        if ($ftp->up_file($file["tmp_name"], $file_name)) {
            $s = getimagesize($file["tmp_name"]);
            return array('mes' => '文件上传成功', 'code' => 1, 'url' => $file_name, 'width' => $s[0], 'height' => $s[1]);
        } else {
            return array('mes' => $file_name, 'code' => 2);
        }
    }

    private function uploadZip($file, $save_path, $ftp_dir)
    {
        global $_SC;
        $max_file_size_in_bytes = 2097152;                // 2M in bytes
        $uploadErrors = array(
            0 => "文件上传成功",
            1 => "上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置",
            2 => "上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置",
            3 => "上传的文件仅为部分文件",
            4 => "没有文件上传",
            6 => "缺少临时文件夹"
        );
        if (!isset($file)) {
            return array('mes' => '找不到文件', 'code' => 2);
        } else if (isset($file["error"]) && $file["error"] != 0) {
            return array('mes' => $uploadErrors[$file["error"]], 'code' => 2);
        } else if (!isset($file["tmp_name"]) || !@is_uploaded_file($file["tmp_name"])) {
            return array('mes' => '文件无法上传', 'code' => 2);
        } else if (!isset($file['name'])) {
            return array('mes' => '文件名不存在', 'code' => 2);
        }
        $file_size = @filesize($file["tmp_name"]);
        if (!$file_size || $file_size > $max_file_size_in_bytes) {
            return array('mes' => '文件size太大', 'code' => 2);
        }
        if ($file_size <= 0) {
            return array('mes' => '文件大小不能为0', 'code' => 2);
        }
        $path_info = pathinfo($file['name']);
        $file_extension = $path_info["extension"];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = strtolower(finfo_file($finfo, $file["tmp_name"]));
            finfo_close($finfo);
        } else {
            $mimetype = strtolower(mime_content_type($file['tmp_name']));
        }
        if (!in_array($mimetype, Ad_Img::$zipMime)) {
            return array('mes' => '文件类型错误:', 'code' => 2);
        }
        $fmd5 = md5_file($file["tmp_name"]);
        $file_name = $save_path . $fmd5 . '.' . $file_extension;
        $_file_name = $ftp_dir . $fmd5;
        if (move_uploaded_file($file["tmp_name"], $file_name)) {
            $file_info = pathinfo($file_name);
            list($dir, $ext) = array($file_info['dirname'] . "/" . $fmd5, $file_info['extension']);
            $files = get_zip_originalsize($file_name, $dir . "/");
            $ftp = new Ftp($_SC['ftp_host'], $_SC['ftp_port'], $_SC['ftp_user'], $_SC['ftp_pass']);
            $e = 1;
            foreach ($files as $file) {
                if (!is_file($save_path . $fmd5 . '/' . $file)) {
                    continue;
                }
                if (!$ftp->up_file($save_path . $fmd5 . '/' . $file, $_file_name . "/" . $file)) {
                    $e = 0;
                }
                $_files[] = $_file_name . "/" . $file;
            }
            if (unlink($file_name) && deldir($dir)) {
                $e = 0;
            }

            return array('mes' => '上传成功并解压', 'code' => 1, 'url' => $_files);
        } else {
            return array('mes' => '上传失败', 'code' => 2);

        }
    }

    private function uploadRun($file, $save_path)
    {
        global $_SC;
        $max_file_size_in_bytes = 2097152;                // 2M in bytes
        $uploadErrors = array(
            0 => "文件上传成功",
            1 => "上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置",
            2 => "上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置",
            3 => "上传的文件仅为部分文件",
            4 => "没有文件上传",
            6 => "缺少临时文件夹"
        );
        if (!isset($file)) {
            return array('mes' => '找不到文件', 'code' => 2);
        } else if (isset($file["error"]) && $file["error"] != 0) {
            return array('mes' => $uploadErrors[$file["error"]], 'code' => 2);
        } else if (!isset($file["tmp_name"]) || !@is_uploaded_file($file["tmp_name"])) {
            return array('mes' => '文件无法上传', 'code' => 2);
        } else if (!isset($file['name'])) {
            return array('mes' => '文件名不存在', 'code' => 2);
        }
        $file_size = @filesize($file["tmp_name"]);
        if (!$file_size || $file_size > $max_file_size_in_bytes) {
            return array('mes' => '文件size太大', 'code' => 2);
        }
        if ($file_size <= 0) {
            return array('mes' => '文件大小不能为0', 'code' => 2);
        }
        $path_info = pathinfo($file['name']);
        $file_extension = $path_info["extension"];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = strtolower(finfo_file($finfo, $file["tmp_name"]));
            finfo_close($finfo);
        } else {
            $mimetype = strtolower(mime_content_type($file['tmp_name']));
        }
        if (!in_array($mimetype, $this->img)) {
            return array('mes' => '文件类型错误:', 'code' => 2);
        }


        $fmd5 = md5_file($file["tmp_name"]);
        $file_name = $save_path . $fmd5 . '.' . $file_extension;

        $ftp = new Ftp($_SC['ftp_host'], $_SC['ftp_port'], $_SC['ftp_user'], $_SC['ftp_pass']);
        if ($ftp->up_file($file["tmp_name"], $file_name)) {

            $s = getimagesize($file["tmp_name"]);
            return array('mes' => '文件上传成功', 'code' => 1, 'url' => $file_name, 'width' => $s[0], 'height' => $s[1]);
        } else {
            return array('mes' => $file_name, 'code' => 2);
        }
    }

    public function uploadRunImages($file, $save_path)
    {
        global $_SC;
        $max_file_size_in_bytes = 2097152; // 2M in bytes
        $uploadErrors = array(0 => "文件上传成功",
            1 => "上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置",
            2 => "上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置",
            3 => "上传的文件仅为部分文件",
            4 => "没有文件上传",
            6 => "缺少临时文件夹"
        );
        if (!isset($file)) {
            return array('mes' => '找不到文件', 'code' => 2);
        } else if (isset($file["error"]) && $file["error"] != 0) {
            return array('mes' => $uploadErrors[$file["error"]], 'code' => 2);
        } else if (!isset($file["tmp_name"]) || !@is_uploaded_file($file["tmp_name"])) {
            return array('mes' => '文件无法上传', 'code' => 2);
        } else if (!isset($file['name'])) {
            return array('mes' => '文件名不存在', 'code' => 2);
        }
        $file_size = @filesize($file["tmp_name"]);
        if (!$file_size || $file_size > $max_file_size_in_bytes) {
            return array('mes' => '文件size太大', 'code' => 2);
        }
        if ($file_size <= 0) {
            return array('mes' => '文件大小不能为0', 'code' => 2);
        }
        $path_info = pathinfo($file['name']);
        $file_extension = $path_info["extension"];

        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = strtolower(finfo_file($finfo, $file["tmp_name"]));
            finfo_close($finfo);
        } else {
            $mimetype = strtolower(mime_content_type($file['tmp_name']));
        }
        if (!in_array($mimetype, Ad_Img:: $mime)) {
            return array('mes' => '文件类型错误', 'code' => 2);
        }

        // 检查文件尺寸，根据position获取尺寸要求
        $imagesize = getimagesize($file["tmp_name"]);
        $position = $_POST['key'];
        $rules = Ad_Img::$type_size;
        $size = explode('_', $rules[$position]);
        //$_width = $size['0'];
        //$_height = $size['1'];

        if ($size['0'] != $imagesize[0] || $size['1'] != $imagesize[1]) {
            return array('mes' => '文件尺寸不符合要求！', 'code' => 2);
        }

        $fmd5 = md5_file($file["tmp_name"]);
        $file_name = $save_path . $fmd5 . '.' . $file_extension;

        $ftp = new Ftp($_SC['ftp_host'], $_SC['ftp_port'], $_SC['ftp_user'], $_SC['ftp_pass']);

        if ($ftp->up_file($file["tmp_name"], $file_name)) {
            $s = getimagesize($file["tmp_name"]);
            return array('mes' => '文件上传成功', 'code' => 1, 'url' => $file_name, 'width' => $s[0], 'height' => $s[1], 'ext' => $file_extension, 'position' => $position);
        } else {
            return array('mes' => $file_name, 'code' => 2);
        }
    }

    private function image_resize($src_file, $dst_file , $new_width , $new_height,$name,$extension) {
        $path_tem="/alidata/code/data/imageCache/".$name.'.'.$extension.$new_width."_".$new_height.'.'.$extension;
        global $_SC;
        $ftp = new Ftp($_SC['ftp_host'], $_SC['ftp_port'], $_SC['ftp_user'], $_SC['ftp_pass']);
        $new_width= intval($new_width);
        $new_height=intval($new_width);
        if($new_width <1 || $new_height <1) {
            echo "params width or height error !";
            exit();
        }
        if(!file_exists($src_file)) {
            echo $src_file . " is not exists !";
            exit();
        }
        // 图像类型
        $type=exif_imagetype($src_file);
        $support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF);
        if(!in_array($type, $support_type,true)) {
            echo "this type of image does not support! only support jpg , gif or png";
            exit();
        }
        //Load image
        switch($type) {
            case IMAGETYPE_JPEG :
                $src_img=imagecreatefromjpeg($src_file);
                break;
            case IMAGETYPE_PNG :
                $src_img=imagecreatefrompng($src_file);
                break;
            case IMAGETYPE_GIF :
                $src_img=imagecreatefromgif($src_file);
                break;
            default:
                echo "Load image error!";
                exit();
        }
        $w=imagesx($src_img);
        $h=imagesy($src_img);
        $ratio_w=1.0 * $new_width / $w;
        $ratio_h=1.0 * $new_height / $h;
        $ratio=1.0;
        // 生成的图像的高宽比原来的都小，或都大 ，原则是 取大比例放大，取大比例缩小（缩小的比例就比较小了）
        if( ($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
            if($ratio_w < $ratio_h) {
                $ratio = $ratio_h ; // 情况一，宽度的比例比高度方向的小，按照高度的比例标准来裁剪或放大
            }else {
                $ratio = $ratio_w ;
            }
            // 定义一个中间的临时图像，该图像的宽高比 正好满足目标要求
            $inter_w=(int)($new_width / $ratio);
            $inter_h=(int) ($new_height / $ratio);
            $inter_img=imagecreatetruecolor($inter_w , $inter_h);
            //var_dump($inter_img);
            imagecopy($inter_img, $src_img, 0,0,0,0,$inter_w,$inter_h);
            // 生成一个以最大边长度为大小的是目标图像$ratio比例的临时图像
            // 定义一个新的图像
            $new_img=imagecreatetruecolor($new_width,$new_height);
            imagecopyresampled($new_img,$inter_img,0,0,0,0,$new_width,$new_height,$inter_w,$inter_h);

//            var_dump($new_img);exit();

            switch($type) {
                case IMAGETYPE_JPEG :

                    imagejpeg($new_img, $path_tem,9); // 存储图像
                    break;
                case IMAGETYPE_PNG :
                    imagepng($new_img,$path_tem,9);
                    break;
                case IMAGETYPE_GIF :
                    imagegif($new_img,$path_tem,9);
                    break;
                default:
                    break;
            }
            $ftp->up_file($path_tem, $dst_file);

        }
        //  目标图像 的一个边大于原图，一个边小于原图 ，先放大平普图像，然后裁剪
        // =if( ($ratio_w < 1 && $ratio_h > 1) || ($ratio_w >1 && $ratio_h <1) )
        else{
            $ratio=$ratio_h>$ratio_w? $ratio_h : $ratio_w; //取比例大的那个值
            // 定义一个中间的大图像，该图像的高或宽和目标图像相等，然后对原图放大
            $inter_w=(int)($w * $ratio);
            $inter_h=(int) ($h * $ratio);
            $inter_img=imagecreatetruecolor($inter_w , $inter_h);
            //将原图缩放比例后裁剪
            imagecopyresampled($inter_img,$src_img,0,0,0,0,$inter_w,$inter_h,$w,$h);
            // 定义一个新的图像
            $new_img=imagecreatetruecolor($new_width,$new_height);
            imagecopy($new_img, $inter_img, 0,0,0,0,$new_width,$new_height);
            switch($type) {
                case IMAGETYPE_JPEG :
                    imagejpeg($new_img, $dst_file,9); // 存储图像
                    break;
                case IMAGETYPE_PNG :
                    imagepng($new_img,$dst_file,9);
                    break;
                case IMAGETYPE_GIF :
                    imagegif($new_img,$dst_file,9);
                    break;
                default:
                    break;
            }
            $ftp->up_file($new_img, $dst_file);


        }
    }

    private function uploadRunPhoneSS($file, $save_path)
    {
        global $_SC;
        $max_file_size_in_bytes = 10485760;//2097152; // 2M in bytes
        $uploadErrors = array(0 => "文件上传成功",
            1 => "上传的文件超过了 php.ini 文件中的 upload_max_filesize directive 里的设置",
            2 => "上传的文件超过了 HTML form 文件中的 MAX_FILE_SIZE directive 里的设置",
            3 => "上传的文件仅为部分文件",
            4 => "没有文件上传",
            6 => "缺少临时文件夹"
        );

        if (!isset($file)) {
            return array('mes' => '找不到文件', 'code' => 2);
        } else if (isset($file["error"]) && $file["error"] != 0) {
            return array('mes' => $uploadErrors[$file["error"]], 'code' => 2);
        } else if (!isset($file["tmp_name"]) || !@is_uploaded_file($file["tmp_name"])) {
            return array('mes' => '文件无法上传', 'code' => 2);
        } else if (!isset($file['name'])) {
            return array('mes' => '文件名不存在', 'code' => 2);
        }
        $file_size = @filesize($file["tmp_name"]);
        if (!$file_size || $file_size > $max_file_size_in_bytes) {
            return array('mes' => '文件size太大', 'code' => 2);
        }
        if ($file_size <= 0) {
            return array('mes' => '文件大小不能为0', 'code' => 2);
        }
        $path_info = pathinfo($file['name']);
        $file_extension = $path_info["extension"];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = strtolower(finfo_file($finfo, $file["tmp_name"]));
            finfo_close($finfo);
        } else {
            $mimetype = strtolower(mime_content_type($file['tmp_name']));
        }
        if (!in_array($mimetype,  $this->img)) {
            return array('mes' => '文件类型错误', 'code' => 2);
        }
        $fmd5 = md5_file($file["tmp_name"]);
        $file_name = $save_path . $fmd5 . '.' . $file_extension;
        $ftp = new Ftp($_SC['ftp_host'], $_SC['ftp_port'], $_SC['ftp_user'], $_SC['ftp_pass']);
        if ($ftp->up_file($file["tmp_name"], $file_name)) {
            $s = getimagesize($file["tmp_name"]);
            return array('mes' => '文件上传成功', 'code' => 1, 'url' => $file_name, 'width' => $s[0], 'height' => $s[1], 'ext' => $file_extension);
        } else {
            return array('mes' => $file_name, 'code' => 2);
        }
    }

}

new Upload();

?>
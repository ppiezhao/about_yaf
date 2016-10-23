<?php
/**
 * 上传处理
 */
class UploadController extends Yaf_Controller_Abstract {
	public function indexAction() {
		$ele = array_keys($_FILES)[0];
		if (!$ele) {
			echo json_encode(array('error'=>'1', 'errmsg'=>'文件不存在'));
			return FALSE;
		}
		if ($_FILES[$ele]) {
			$config = Yaf_Application::app()->getConfig()->imgupload->toArray();
			$dir = '/public/publish/'.date('Ymd');
			if(!$this->destination_exist($config['root'],$dir)){
				$this->create_destination($config['root'],$dir);
			}
			$filename = $_FILES[$ele]['name'];
			$gb_filename = iconv('utf-8','gb2312',$filename);	//名字转换成gb2312处理	
		    $result = explode('.',$gb_filename);
				
			$isMoved = false;  //默认上传失败
			$MAXIMUM_FILESIZE = 1 * 1024 * 1024; 	//文件大小限制	1M = 1 * 1024 * 1024 B;
			$rEFileTypes = "/^\.(jpg|jpeg|gif|png){1}$/i";
			$dir = $dir.'/' . uniqid() . '.'.$result[1]; 
			if ($_FILES[$ele]['size'] <= $MAXIMUM_FILESIZE && 
				preg_match($rEFileTypes, strrchr($gb_filename, '.'))) {	
				$isMoved = @move_uploaded_file ( $_FILES[$ele]['tmp_name'], $config['root'].$dir);		//上传文件
			}	
			if (!$isMoved) {
				echo json_encode(array('error'=>'2', 'errmsg'=>'文件上传失败,'));
			} else {
				$path = str_replace('\\', '/', $dir);
				
				//$filemodel = new FilesModel();
				//$id = $filemodel->addFileThumb($path, $thumbfile);
				//if (!$id) {
				//	echo json_encode(array('error'=>'3', 'errmsg'=>'文件上传失败,'));
				//} else {
					echo json_encode(array('error'=>'0', 'data'=>array('path'=>$path)));
				//}
			}
		}
		return FALSE;
	}
	//目录是否存在
	protected function destination_exist($root,$destination) {
		return is_writable($root.$destination);
	}

	protected function create_destination($root, $destination) {
			return mkdir($root . $destination, 750, true);
         }

}

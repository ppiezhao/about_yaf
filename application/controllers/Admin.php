<?php
/**
 * @name IndexController
 * @author work
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class AdminController extends Yaf_Controller_Abstract {
	public function init(){
		$this->blog = new BlogEntryModel();
		$this->_newstalk = new BlogNewstalkModel();
		$this->_tpl = $this->getView();
		$this->_req = $this->getRequest();
	}
	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/dev/index/index/index/name/work 的时候, 你就会发现不同
     */
	public function indexAction($name = "Stranger") {
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");

		//2. fetch model
		$model = new SampleModel();

		//3. assign
		$this->getView()->assign("content", $model->selectSample());
		$this->getView()->assign("name", $name);

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return TRUE;
	}
	
	
	//发表博文
	public function editBlogAction(){
	}
	
	public function editBlogAjaxAction(){
		$data['type'] = 0;
		$data['data'] = '标题为空！请添加标题！';
		$content = htmlspecialchars($this->_req->getPost('content', ''));
		$title = htmlspecialchars($this->_req->getPost('title', ''));
		$type = (int)$this->_req->getPost('type', 0);
		if(!$content || !$title){
			$data['type'] = 0;
			$data['data'] = '参数错误';
			echo json_encode($data);
			exit;
		}
			
		$parms['abstract'] = $title;
		$parms['img'] = '';
		
		$parms['mainbody'] = $content;
		$parms['status'] = 1;
		$parms['created'] = time();
		$parms['type'] = $type;
		
		$id = $this->blog->insert($parms);
		if($id){
			$data['type'] = 1;
			$data['data'] = '操作成功！';
		}else{
			$data['type'] = 0;
			$data['data'] = '操作失败！';
		}
		echo json_encode($data);
		exit;
	}
	
	
	public function blogAction(){
		//首页点击最多的博文
		/* $entermore = $this->entermore();
		$this->_tpl->assign('entermore',$entermore); */
	}
	
	public function enterMoreAjaxAction(){
		$data['type'] = 0;
		$data['data'] = '暂无数据';
		$entermore = $this->entermore();
		
		if(!empty($entermore)){
			foreach($entermore as $key=>$en){
				$entermore["$key"]['created'] = date("Y:m:d",$en['created']);
			}
			$data['type'] = 1;
			$data['data'] = $entermore;
		}
		echo json_encode($data);
		exit;
	}
	
	public function entermore($start=0,$offset=10){
		$parms = array();
		$parms['where'] = ' status = 1 ';
		$parms['limit'] = ' limit '.$offset.' offset '.$start;
		$parms['order'] = ' abstract desc,created desc ';
		$result = $this->blog->select($parms);
		return $result;
	}

	public function insertAction(){
		$parms['img'] = '/publc/le/img/logo.png';
		$parms['mainbody'] = '这是一个神奇的博客！';
		$parms['abstract'] = '只是一个神奇的博客，欢迎加入！一起前行！';
		$parms['status'] = 1;
		$parms['created'] = time();
		$id = $this->blog->insert($parms);
		echo $id;
		return false;
		
	}

	public function editMoodlistAction(){
		$start = (int)$this->_req->get('page','1');
		//$page = 
		$offset = 10;
		$start = $start - 1;
		$parms['where'] = " status = 1";
		$parms['order'] = " created desc";
		$parms['limit'] = ' limit '.$offset.' offset '.$start;
		$arr = $this->_newstalk->select($parms);	
	}

	//发表或编辑
	public function publishAction(){
		$id = (int)$this->_req->get('id');
		$this->_tpl->assign('id', $id);
		//$this->_tpl->assign();
	}

	public function publishajaxAction(){
		$data['img'] = htmlspecialchars($this->_req->getPost('img',''));
		$data['newstalk'] = htmlspecialchars($this->_req->getPost('text',''));
		$data['created'] = time();
		$data['status'] = 1;	
		$id = $this->_newstalk->insert($data);
		$arr['error'] = 0;
		$arr['errormsg'] = "提交失败";
		if($id){
			$arr['error'] = 1;
			$arr['data'] = $id;
		}

		echo json_encode($arr);
		return false;
	}	
}

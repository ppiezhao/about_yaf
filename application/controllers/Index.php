<?php
/**
 * @name IndexController
 * @author work
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Yaf_Controller_Abstract {
	public function init(){
		$this->blog = new BlogEntryModel();
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
				$content = htmlspecialchars_decode($en['mainbody']);
				
				$search = '/<p.*?>(.*?)<\/p>/si';
				preg_match_all($search ,$content, $r);
				
				if(isset($r[1])){
					foreach($r[1] as $v){
						$a = str_replace("<br/>", "", $v);
						$a = str_replace("&nbsp;", "", $a);
						$a = str_replace("\n", "", $a);
						if($a){
							$entermore["$key"]['mainbody'] = $v;
							break;
						}
					}
					
					
				}
				
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

	public function insertajaxAction(){
		$data['type'] = 0;
		$data['data'] = '标题为空！请添加标题！';
		$content = htmlspecialchars($this->_req->getPost('content', ''));
		$title = htmlspecialchars($this->_req->getPost('title', ''));
		/* $a = str_replace("<br/>", "", $content);
		$a = str_replace("&amp;nbsp;", "", $a);
		$a = str_replace("\n", "", $a); */
		//echo "<br/>";
		//echo $content = str_replace("&lt;", "<", $content);
		//echo "<br/>";
		//echo $content = str_replace("&gt;", ">", $content);
		//echo "<br/>";
		/* for($i=1;$i<=6;$i++){
			$search = '/&lt;h'.$i.'(.*?)&gt;(.*?)&lt;\/h'.$i.'&gt;/si';
			//echo $search = '/<h'.$i.'(.*?)>(.*?)<\/h'.$i.'>/si';
			//echo $a;
			preg_match_all($search ,$a, $r);
			//print_r($r);
			if(!empty($r[2])){
				break;
			}	
		}
		
		if($i>6){
			echo json_encode($data);
			exit;
		} */
		$parms['abstract'] = $title;
		$parms['img'] = '';
		//$parms['img'] = '/public/le/img/06.jpg';
		$parms['mainbody'] = $content;
		$parms['status'] = 1;
		$parms['created'] = time();
		
		//$pattern ='&quot;img.*?src=&quot;(.*?)&quot;';
		/* $pattern ='`&lt;img.*?src=&quot;(.*?)&quot;(.*?)\/&gt;`';
		//echo $content;
		//$content = '<img id="pic" name="pic" src="aaa.jpg" style="width: 640px;">';
		preg_match($pattern,$content,$matches);
		if(!empty($matches[1])){
			$parms['img'] = $matches[1];
		} */
		//print_r($parms);die;
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
		echo 1111;
		
		
		echo $content;
		die;
		$parms['img'] = '/public/le/img/01.jpg';
		$parms['mainbody'] = '这是一个神奇的博客！';
		$parms['abstract'] = '只是一个神奇的博客，欢迎加入！一起前行！';
		$parms['status'] = 1;
		$parms['created'] = time();
		$id = $this->blog->insert($parms);
		echo $id;
		return false;
		
	}


	public function testAction(){
		/* $html = '<h1    3333333>罚款解放咔叽</h1><p>&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;ddddsdddddddddddddddddddddddddddddddddddddddddddddddddddddddds发的卡积分拉夫拉达大幅；拉开分开多久啊；大力开发；大局尽快发了打开了放到大陆的垃圾fall发的<br/></p>';
		$search = '/<h1(.*?)>(.*?)<\/h1>/si';
		preg_match_all($search ,$html, $r);
		print_r($r[2]); */
		$pattern ='`&lt;img.*?src=&quot;(.*?)&quot;(.*?)\/&gt;`';
		//$html = '<img id="pic" name="pic" src="aaa.jpg" style="width: 640px;"><img id="pic" name="pic" 
		//src="aaa.jpg" style="width: 640px;">';
		
		$html = '&lt;img alt=&quot;02.jpg&quot; src=&quot;/ueditor/php/upload/image/20160710/1468165103158250.jpg&quot; title=&quot;1468165103158250.jpg&quot;/&gt;';
		preg_match($pattern,$html,$matches);
		print_r($matches);
		echo "<br/>";
		echo $matches[1];
		return false;
	}
}

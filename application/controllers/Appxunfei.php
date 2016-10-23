<?php
/**
 * @name BlogController
 * @author work
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class AppxunfeiController extends Yaf_Controller_Abstract {
	public function init(){
		$this->blog = new BlogEntryModel();
		$this->_newstalk = new BlogNewstalkModel();
		$this->_ipmodel = new BlogIpModel();
		$this->_read = new BlogReadnumModel();
		$this->_page = new PageModel();
		$this->_tpl = $this->getView();
		$this->_req = $this->getRequest();
		$this->_ip = Utils_Tool::getRealIP();
		$this->_redis = new Utils_Redis();
		$this->insertip();
	}
	
	public function textspeechAction(){
		
		
	}
	
	
	//一小时之内访问同一页面不增加访问量
	public function insertreadnum($bid){
		if(!$bid){
			return 0;
		}
		$ip = $ip = $this->_ip;
		$key = $ip.'insertreadnum'.$bid;
		if($this->_redis->get($key)){
			return 0;
		}
		$this->_redis->set($key,'1',60*60);
		$parms['where'] = " bid = '".$bid."' ";
		
		$is_exit = $this->_read->find($parms);
		unset($parms);
		if($is_exit){
			$where['bid'] = $bid;
			$parms['readnum'] = $is_exit['readnum'] + 1;
			$parms['modified'] = date('Y-m-d H:i:s',time());
			$this->_read->update($parms,$where);
		}else{
			
			$parms['bid'] = $bid;
			$parms['readnum'] = 1;
			$parms['created'] = time();
			$parms['modified'] = date('Y-m-d H:i:s',time());
			$this->_read->insert($parms);
		}
		return 1;
	}
	
	
	//一小时更新一次访问时间
	public function insertip(){
		$ip = $this->_ip;
		$key = $ip.'insert';
		if($this->_redis->get($key)){
			return 0;
		}
		$this->_redis->set($key,'1',60*60);
		$parms['where'] = " ip = '".$ip."' ";
		
		$is_exit = $this->_ipmodel->find($parms);
		unset($parms);
		if($is_exit){
			$where['ip'] = $ip;
			$parms['modified'] = date('Y-m-d H:i:s',time());
			$this->_ipmodel->update($parms,$where);
		}else{
			
			$parms['ip'] = $ip;
			$parms['address'] = $this->_getAddressByIp($ip);
			$parms['created'] = time();
			$parms['modified'] = date('Y-m-d H:i:s',time());
			$this->_ipmodel->insert($parms);
		}
		return 1;
		
	}
	
	private function _getAddressByIp($ip) {
        $url = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_ENCODING, 'utf8');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // ��ȡ��ݷ���
        $location = curl_exec($ch);
        $location = json_decode($location, true);
        curl_close($ch);
        $city = "";
        if (empty($location))
            return "";
        if ($location['data']['city']) {
            $city = $location['data']['city'];
            $city = str_replace('市', '', $city);
        }
        return $city;
    }
	
	
	public function testaAction(){
		$start = 1;
		$offect = 10;
		$totle = 76;
		$page = $this->_page->getPage($start,$offect,$totle);
		echo $page;
		return false;
	}
	/** 
     * 
     *
     * 
     */
	public function indexAction() {
		
        
	}
	
	/** 
     * 
     *
     * 
     */
	public function aboutAction() {
		
        
	}
	
	/** 
     * 
     *
     * 
     */
	public function caselistAction() {
		
        
	}
	
	/** 
     * 
     *
     * 
     */
	public function knowledgeAction() {
		
        
	}
	
	/** 
     * 
     *
     * 
     */
	public function moodlistAction() {
		
        
	}

	public function getmoodlistajaxAction(){
		
		$start = (int)($this->_req->getPost('start', 1));
		$offect = 10;
		$data['type'] = 0;
		$data['data'] = '暂无数据';
		$parms = array();
		$parms['where'] = ' status = 1 ';
		$totle = $this->_newstalk->select($parms,1);
		$data['page'] = $this->_page->getPage($start,$offect,$totle);
		$start = ($start - 1) * $offect;
		unset($parms);
		$parms['where'] = ' status = 1 ';
		$parms['limit'] = ' limit '.$offect.' offset '.$start;
		$parms['order'] = ' created desc ';
		$result = $this->_newstalk->select($parms);
		
		if(!empty($result)){
			foreach($result as $key=>$res){
				$result[$key]['time'] = date("Y:m:d",$res['created']);
			}
			$data['type'] = 1;
			$data['data'] = $result;
		}
		echo json_encode($data);
		exit;
		
	}
	
	/** 
     * 
     *
     * 
     */
	public function newAction() {
		$id = (int)($this->_req->get('id', 0));
		if(!$id){
			die('undefine');
		}
        $parms = array();
		$parms['where'] = " id = '".$id."' and status = 1";
		$parms['limit'] = ' limit 1 ';
		//当前
		
		$result = $this->blog->select($parms);
		if(!$result){
			die('undefine');
		}
		$this->insertreadnum($id);
		$result[0]['time'] = date("Y:m:d",$result[0]['created']);
		$result[0]['abstract'] = htmlspecialchars_decode($result[0]['abstract']);
		$result[0]['mainbody'] = htmlspecialchars_decode($result[0]['mainbody']);
		//前一篇
		$parms['where'] = " id < '".$id."' and status = 1";
		$parms['limit'] = ' limit 1 ';
		$resultpre = $this->blog->select($parms);
		//后一篇
		$parms['where'] = " id > '".$id."' and status = 1";
		$parms['limit'] = ' limit 1 ';
		$resultlast = $this->blog->select($parms);
		$this->getView()->assign('result',$result);
		$this->getView()->assign('resultpre',$resultpre);
		$this->getView()->assign('resultlast',$resultlast);
		
	}
	
	/** 
     * 
     *
     * 
     */
	public function newlistAction() {
		
        
	}
	
	
	public function newlistAjaxAction(){
		$start = (int)($this->_req->getPost('start', 1));
		$offect = 10;
		$data['type'] = 0;
		$data['data'] = '暂无数据';
		$parms = array();
		$parms['where'] = ' status = 1 ';
		$totle = $this->blog->select($parms,1);
		$data['page'] = $this->_page->getPage($start,$offect,$totle);
		$start = ($start - 1) * $offect;
		
		$entermore = $this->entermore($start,$offect);
		
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
	
	/* public function entermore($start=0,$offset=10){
		$parms = array();
		$parms['where'] = ' status = 1 ';
		$parms['limit'] = ' limit '.$offset.' offset '.$start;
		$parms['order'] = ' abstract desc,created desc ';
		$result = $this->blog->select($parms);
		return $result;
	} */
	
	/** 
     * 
     *
     * 
     */
	public function shareAction() {
		
        
	}
	
	/** 
     * 
     *
     * 
     */
	public function templateAction() {
		
        
	}
	
	/** 
     * 
     *
     * 
     */
	public function bookAction() {
		
        
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
				if(isset($r[0][0])){
					$entermore["$key"]['mainbody'] = $r[0][0];
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
		//return 1111;die;
		
		$parms = array();
		$parms['where'] = ' status = 1 ';
		$parms['limit'] = ' limit '.$offset.' offset '.$start;
		$parms['order'] = ' created desc ';
		$result = $this->blog->select($parms);
		return $result;
	}

	public function insertajaxAction(){
		$data['type'] = 0;
		$data['data'] = '标题为空！请添加标题！';
		$content = htmlspecialchars($this->_req->getPost('content', ''));
		
		//echo "<br/>";
		//echo $content = str_replace("&lt;", "<", $content);
		//echo "<br/>";
		//echo $content = str_replace("&gt;", ">", $content);
		//echo "<br/>";
		for($i=1;$i<=6;$i++){
			$search = '/&lt;h'.$i.'(.*?)&gt;(.*?)&lt;\/h'.$i.'&gt;/si';
			//echo $search = '/<h'.$i.'(.*?)>(.*?)<\/h'.$i.'>/si';
			preg_match_all($search ,$content, $r);
			//print_r($r);
			if(!empty($r[2])){
				break;
			}	
		}
		if($i>6){
			echo json_encode($data);
			exit;
		}
		$parms['abstract'] = $r[2][0];
		$parms['img'] = '/public/le/img/01.jpg';
		$parms['mainbody'] = $content;
		$parms['status'] = 1;
		$parms['created'] = time();
		
		//$pattern ='&quot;img.*?src=&quot;(.*?)&quot;';
		$pattern ='`&lt;img.*?src=&quot;(.*?)&quot;(.*?)\/&gt;`';
		//echo $content;
		//$content = '<img id="pic" name="pic" src="aaa.jpg" style="width: 640px;">';
		preg_match($pattern,$content,$matches);
		if(!empty($matches[1])){
			$parms['img'] = $matches[1];
		}
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
	
	//最近selectnewlyAjax
	public function selectnewlyAjaxaction(){
		$offect = 6;
		$start = 0;
		$parms['field'] = 'blog_entry.id,blog_entry.abstract';
		$parms['where'] = ' status = 1 ';
		$parms['limit'] = ' limit '.$offect.' offset '.$start;
		$parms['order'] = ' created desc ';
		$result = $this->blog->select($parms);
		if($result){
			$data['type'] = 1;
			$data['data'] = $result;
		}else{
			$data['type'] = 0;
		}
		echo json_encode($data);
		exit;
	}
	
	//浏览量
	//最近selectnewlyAjax
	public function selectmaxAjaxaction(){
		$offect = 6;
		$start = 0;
		$parms['field'] = 'blog_entry.id,blog_entry.abstract';
		$parms['left'] = ' left join blog_readnum on blog_readnum.bid = blog_entry.id ';
		$parms['where'] = ' blog_entry.status = 1 ';
		$parms['limit'] = ' limit '.$offect.' offset '.$start;
		$parms['order'] = ' blog_entry.created desc ';
		$result = $this->blog->select($parms);
		if($result){
			$data['type'] = 1;
			$data['data'] = $result;
		}else{
			$data['type'] = 0;
		}
		echo json_encode($data);
		exit;
	}
	
}

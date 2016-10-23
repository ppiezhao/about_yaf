<?php
class Ui_Common_BlogHeader extends Ui_Base {
	public function __construct() {
		parent::__construct();
		$this->display('common/blog_header.phtml');
	}
}
<?php

/**
 * Index action
 *
 * @author andrew(at)w(dot)cn
 * @since 23:16 01/04/2012
 */
defined('SYS_ROOT') || die('Access deined');

class indexAction extends publicAction {

	public function showIndex() {
		$this->assign('title', 'Welcome');
		$this->assign('keywords', 'Welcome page');
		$this->assign('description', 'Welcome page, test content');
		$this->assign('content', form()->showCreateForm());
		$this->display('index');
	}

}
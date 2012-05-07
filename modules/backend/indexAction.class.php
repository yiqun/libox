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
		$this->assign('modules', db()->rows("SELECT cat_id, cat_name FROM @__category WHERE parent_id = 0 AND status = 1 ORDER BY sort, cat_id"));
		$this->assign('title', 'Welcome');
		$this->assign('keywords', 'Welcome page');
		$this->assign('description', 'Welcome page, test content');
		$this->display('index');
	}

	public function showTest() {
		sleep(10);
		echo <<<EOF
		<p><strong>Frontend view:</strong> The view&nbsp; you get by writing <a href="http://localhost/wp3/">http://localhost/wp3/</a> in your intenet browser’s address bar is known as frontend of wordpress. This is nothing but the output of your blog what will be seen by your thousands of visitors. See below the frontend of our blog “<strong>wordpress tutorials</strong>”.</p>
EOF;
	}

}
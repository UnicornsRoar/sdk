<?php
/**
 * 空模块
 */

class EmptyAction extends Action {
    public function _empty() {
        $this->error('访问的页面不存在！');
    }
}
?>
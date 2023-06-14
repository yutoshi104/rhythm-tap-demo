<?php

class View{
	private $params = [];
    private $templates = [];

	public function __construct() {
		ob_start();
	}

    public function __destruct() {
    }

    # 変数保存
	public function addParam($key, $value) {
		$this->params[$key] = $value;
	}

    # テンプレート保存
	public function addTemplate($template) {
		$this->templates[] = $template;
	}

    # 変数、テンプレートを読み込み
	private function readBuffer() {
		foreach($this->params as $key => $value){
			$$key = $value;
		}
		foreach($this->templates as $template){
			if(file_exists($template)) {
				include $template;
			}
		}
	}

	# 文字列として出力
	public function getBuffer() {
		$this->readBuffer();
		return ob_get_clean();
	}

	# 表示
	public function display() {
		$this->readBuffer();
		ob_flush();
	}
}

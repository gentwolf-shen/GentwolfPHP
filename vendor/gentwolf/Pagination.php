<?php

namespace gentwolf;

class Pagination {
	private $count;
	private $totalPage;
	private $size;
	private $page;
	private $offset;
	private $prev;
	private $next;
	private $url;

	function __construct($config = null) {
		$config = array_merge(array(
			'count'	=> 0,
			'page'	=> 1,
			'size'	=> 20,
			'url'	=> '?page={page}'
		), $config);

		$this->initialize($config);
	}

	private function initialize($config) {
		$this->count = intval($config['count']);
		$this->page = intval($config['page']);
		$this->size = intval($config['size']);
		$this->url = $config['url'];

		$this->totalPage = ceil($this->count / $this->size);
		$this->page = ($this->page > $this->totalPage || $this->page <= 0) ? 1 : $this->page;
		$this->prev = $this->page > 1 ? $this->page - 1 : 1;
		$this->next = $this->page < $this->totalPage ? $this->page + 1 : $this->totalPage;

		$this->offset = ($this->page - 1) * $this->size;
	}

	public function getPrevUrl() {
		return str_replace('{page}', $this->prev, $this->url);
	}

	public function getNextUrl() {
		return str_replace('{page}', $this->next, $this->url);
	}

	public function getCount() {
		return $this->count;
	}

	public function getTotalPage() {
		return $this->totalPage;
	}

	public function getPage() {
		return $this->page;
	}

	public function getOffset() {
		return $this->offset;
	}

	public function getSize() {
		return $this->size;
	}

	public function getPrev() {
		return $this->prev;
	}

	public function getNext() {
		return $this->next;
	}

	public function getPrevHref() {
		$url = str_replace('{page}', $this->getPrev(), $this->url);
		return '<li><a href="'. $url .'">&laquo;</a></li>';
	}

	public function getNextHref() {
		$url = str_replace('{page}', $this->getNext(), $this->url);
		return '<li><a href="'. $url .'">&raquo;</a></li>';
	}

	private function getHref($page, $text = NULL) {
		if (NULL === $text) $text = $page;
		$url = str_replace('{page}', $page, $this->url);
		return $page != $this->page ? '<li><a href="'. $url .'">'. $text .'</a></li>' : '<li class="active"><a href="javascript:void(0)">'. $text .'</a></li>';
	}

	private function getLoopHref($min, $max) {
		$tmp = false;
		for ($i = $min; $i <= $max; $i++) {
			$tmp[] = $this->getHref($i);
		}
		return implode(' ', $tmp);
	}

	private function getSpace() {
		return '<li class="disabled"><a href="javascript:void(0)">…</a></li>';
	}

	public function showDigg() {
		$tmp[] = '<nav><ul class="pagination">';
		$tmp[] = $this->getPrevHref();

		if ($this->totalPage < 13) {
			$tmp[] = $this->getLoopHref(1, $this->totalPage);
		} else if ($this->page < 9) {
			$tmp[] = $this->getLoopHref(1, 10);
			$tmp[] = $this->getSpace();
			$tmp[] = $this->getHref($this->totalPage - 1);
			$tmp[] = $this->getHref($this->totalPage);
		} else if ($this->page > $this->totalPage - 8) {
			$tmp[] = $this->getHref(1);
			$tmp[] = $this->getHref(2);
			$tmp[] = $this->getSpace();
			$tmp[] = $this->getLoopHref($this->totalPage - 9, $this->totalPage);
		} else {
			$tmp[] = $this->getHref(1);
			$tmp[] = $this->getHref(2);
			$tmp[] = $this->getSpace();
			$tmp[] = $this->getLoopHref($this->page - 5, $this->page + 5);
			$tmp[] = $this->getSpace();
			$tmp[] = $this->getHref($this->totalPage - 1);
			$tmp[] = $this->getHref($this->totalPage);
		}

		$tmp[] = $this->getNextHref();
		$tmp[] = '</ul></nav>';
		return implode(' ', $tmp);
	}

	public function render($style = 1) {
		return $this->showDigg();
	}
}
<?php namespace Koldy;

/**
 * Class for handling page pagination.
 * 
 * @TODO format and reorganize this and refactor and what so evr!
 * 
 * XXX: Do not rely on this class yet!
 *
 */
class Pagination {

	protected $numberOfPageLinks = 5;

	protected $currentPage = null;

	protected $total = null;

	protected $showFirstAndLast = true;

	protected $showPrevAndNext = true;

	protected $itemsPerPage = 20;

	protected $linkCss = null;

	protected $firstCss = 'first';

	protected $prevCss = 'prev';

	protected $nextCss = 'next';

	protected $lastCss = 'last';

	protected $pageCss = 'page';

	protected $selectedCss = 'selected';

	protected $firstText = '&lArr;';

	protected $previousText = '&larr;';

	protected $nextText = '&rarr;';

	protected $lastText = '&rArr;';

	protected $link = '#/page/{page}';
	
	protected $format = '<a href="{href}" class="{class}" data-page="{page}">{text}</a>';

	/**
	 * Create the object
	 * @param int $currentPage
	 * @param int $total
	 */
	public function __construct($currentPage, $total) {
		$this->currentPage = $currentPage;
		$this->total = $total;
	}

	/**
	 * Set how many link will be displayed as number
	 * @param int $numberOfLinksPerPage
	 * @return \Koldy\Pagination
	 */
	public function setLinksPerPage($numberOfLinksPerPage) {
		$this->numberOfPageLinks = $numberOfLinksPerPage;
		return $this;
	}

	/**
	 * Set the URL pattern
	 * @param string $pattern
	 * @return \Koldy\Pagination
	 * @example param #/page/%s
	 */
	public function setUrl($pattern) {
		$this->link = $pattern;
		return $this;
	}

	/**
	 * Set how many records will be displayed per page
	 * @param int $numberOfRecordsPerPage
	 * @return \Koldy\Pagination
	 */
	public function setItemsPerPage($numberOfRecordsPerPage) {
		$this->itemsPerPage = $numberOfRecordsPerPage;
		return $this;
	}

	/**
	 * Set the visible text for first link
	 * @param string $first
	 * @return \Koldy\Pagination
	 */
	public function setTextFirst($first) {
		$this->firstText = $first;
		return $this;
	}

	/**
	 * Set the visible text for previous link
	 * @param string $prev
	 * @return \Koldy\Pagination
	 */
	public function setTextPrevious($prev) {
		$this->previousText = $prev;
		return $this;
	}

	/**
	 * Set the visible text for next link
	 * @param string $next
	 * @return \Koldy\Pagination
	 */
	public function setTextNext($next) {
		$this->nextText = $next;
		return $this;
	}

	/**
	 * Set the visible text for last link
	 * @param string $last
	 * @return \Koldy\Pagination
	 */
	public function setTextLast($last) {
		$this->lastText = $last;
		return $this;
	}

	/**
	 * Set the css class that will be added on the first place to all the <a> links
	 * @param string $defaultCssClass
	 * @return \Koldy\Pagination
	 */
	public function setCssDefault($defaultCssClass) {
		$this->linkCss = $defaultCssClass;
		return $this;
	}

	/**
	 * Set the CSS attribute for first link
	 * @param string $css
	 * @return \Koldy\Pagination
	 */
	public function setCssFirst($css) {
		$this->firstCss = $css;
		return $this;
	}

	/**
	 * Set the CSS attribute for prev link
	 * @param string $css
	 * @return \Koldy\Pagination
	 */
	public function setCssPrev($css) {
		$this->prevCss = $css;
		return $this;
	}

	/**
	 * Set the CSS attribute for next link
	 * @param string $css
	 * @return \Koldy\Pagination
	 */
	public function setCssNext($css) {
		$this->nextCss = $css;
		return $this;
	}

	/**
	 * Set the CSS attribute for last link
	 * @param string $css
	 * @return \Koldy\Pagination
	 */
	public function setCssLast($css) {
		$this->lastCss = $css;
		return $this;
	}

	/**
	 * Set the CSS attribute for each link
	 * @param string $css
	 * @return \Koldy\Pagination
	 */
	public function setCssPage($css) {
		$this->pageCss = $css;
		return $this;
	}

	/**
	 * Set the CSS class for selected page
	 * @param string $selected
	 * @return \Koldy\Pagination
	 */
	public function setCssSelected($selected) {
		$this->selectedCss = $selected;
		return $this;
	}
	
	/**
	 * Set the URL format. Use %s as placeholder for page number
	 * @param string $href
	 * @return \Koldy\Pagination
	 * @example pass #/page/%s
	 */
	public function setLink($href) {
		$this->link = $href;
		return $this;
	}
	
	/**
	 * Set the HTML format
	 * @param string $format
	 * @return \Koldy\Pagination
	 * @example <a href="{href}" class="{class}" data-page="{page}">{text}</a>
	 */
	public function setFormat($format) {
		$this->format = $format;
		return $this;
	}

	/**
	 * Get the CSS for the link
	 * @param string $add
	 * @return string
	 */
	private function getLinkCss($add = null) {
		$css = '';

		if ($this->linkCss !== null) {
			$css .= $this->linkCss . ' ';
		}

		if ($add !== null) {
			$css .= $add;
		}

		return rtrim($css);
	}
	
	/**
	 * Get the link href
	 * @param int $page
	 * @return mixed
	 */
	private function getLinkHref($page) {
		$href = $this->link;
		$href = str_replace('{page}', $page, $href);
		return $href;
	}
	
	/**
	 * Get the HTML for complete link
	 * @param string $text
	 * @param string $page
	 * @param string $css
	 * @return mixed
	 */
	private function getLinkHtml($text, $page, $css) {
		$html = $this->format;
		
		$html = str_replace('{href}', $this->getLinkHref($page), $html);
		$html = str_replace('{text}', $text, $html);
		$html = str_replace('{page}', $page, $html);
		$html = str_replace('{class}', $css, $html);
		
		return $html;
	}

	/**
	 * Generate the HTML
	 * @return string
	 */
	public function generate() {
		$currentPage = $this->currentPage;
		$total = $this->total;

		$html = '';
		$totalPages = ceil($total / $this->itemsPerPage);

		$half = floor($this->numberOfPageLinks / 2);
		$startPage = $currentPage - $half;

		if ($startPage < 1) {
			$startPage = 1;
		}

		$endPage = $startPage + $this->numberOfPageLinks -1;

		if ($endPage > $totalPages) {
			$endPage = $totalPages;
		}

		if ($this->showFirstAndLast) {
			if ($currentPage > 2) {
				$html .= $this->getLinkHtml($this->firstText, 1, $this->getLinkCss($this->firstCss));
			}
		}

		if ($this->showPrevAndNext) {
			if ($currentPage > 1) {
				$html .= $this->getLinkHtml($this->previousText, $currentPage -1, $this->getLinkCss($this->prevCss));
			}
		}

		for ($i = $startPage; $i <= $endPage; $i++) {
			$css = $this->pageCss;

			if ($i == $currentPage) {
				$css .= ' ' . $this->selectedCss;
			}
			
			$html .= $this->getLinkHtml($i, $i, $this->getLinkCss($css));
		}

		if ($this->showPrevAndNext) {
			if ($currentPage < $totalPages) {
				$html .= $this->getLinkHtml($this->nextText, $currentPage +1, $this->getLinkCss($this->nextCss));
			}
		}

		if ($this->showFirstAndLast) {
			if ($currentPage < $totalPages -1) {
				$html .= $this->getLinkHtml($this->lastText, $totalPages, $this->getLinkCss($this->lastCss));
			}
		}

		return $html;
	}

	public function __toString() {
		return $this->generate();
	}
}
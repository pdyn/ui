<?php
/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * @copyright 2010 onwards James McQuillan (http://pdyn.net)
 * @author James McQuillan <james@pdyn.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace pdyn\ui;

use \pdyn\base\Exception;

/**
 * Generates customizable pagination.
 */
class Pagination {
	/** @var int The total number of items. */
	protected $total = null;

	/** @var int The current page. */
	protected $current = null;

	/** @var string The base url for the links. */
	protected $baseurl = '';

	/** @var int The number of items per page. Used to calculate the number of pages from the number of items. */
	protected $perpage = 20;

	/** @var string A space-separated list of additional classes to add to the links. */
	protected $linkclasses = '';

	/**
	 * Constructor.
	 *
	 * @param int $current The current page number.
	 * @param int $total Total number of items to display (number of pages will be calculated automatically)
	 * @param string $baseurl The base url for each of the links. p=[pagenum] will be appended.
	 */
	public function __construct($current, $total, $baseurl) {
		if (!empty($total) && \pdyn\datatype\Validator::intlike($total)) {
			$this->total = (int)$total;
		} else {
			throw new Exception('Bad total items passed to \pdyn\ui\Pagination', Exception::ERR_BAD_REQUEST);
		}

		$this->current = (!empty($current) && \pdyn\datatype\Validator::intlike($current)) ? (int)$current : 1;

		if (!empty($baseurl)) {
			$this->baseurl = $baseurl;
		} else {
			throw new Exception('No base url specified in \pdyn\ui\Pagination', Exception::ERR_BAD_REQUEST);
		}
	}

	/**
	 * Set the number of tiems per page.
	 *
	 * @param int $perpage The number of items per page to set.
	 */
	public function set_items_per_page($perpage) {
		$this->perpage = (!empty($perpage) && \pdyn\datatype\Validator::intlike($perpage)) ? (int)$perpage : 20;
	}

	/**
	 * Get the HTML for a link page link.
	 *
	 * @param int $page The page number.
	 * @param string $text The text to display.
	 * @return string The HTML for a single page link.
	 */
	protected function get_link_html($page, $text) {
		$url = new \pdyn\datatype\Url($this->baseurl);
		$url->addquery('p='.$page);
		return '<a href="'.(string)$url.'" data-page="'.$page.'" class="page_link jump_link '.$this->linkclasses.'">'.$text.'</a>';
	}

	/**
	 * Set the classes that will be used for each link.
	 *
	 * @param string $linkclasses The classes that will be added to each link.
	 */
	public function set_linkclasses($linkclasses) {
		$this->linkclasses = $linkclasses;
	}

	/**
	 * Get the HTML.
	 *
	 * @return string The HTML for the pagination links.
	 */
	public function get_html() {
		$link_separator = '';
		$html = '<span class="page_links"><span>Page:</span>';
		$total_pages = (int)ceil($this->total / $this->perpage);
		$cur_page = ($this->current > $total_pages) ? $total_pages : $this->current;

		if ($cur_page > 1) {
			//$html .= $this->get_link_html(1, '&laquo;');
			$html .= $this->get_link_html(($cur_page - 1), '&lsaquo;');
		} else {
			//$html .= '<span class="cur_page">&laquo;</span>';
			$html .= '<span class="cur_page">&lsaquo;</span>';
		}

		if ($total_pages < 10) {
			//if less than 10 pages, we display links to all
			for ($i = 1; $i <= $total_pages; $i++) {
				$html .= ($i === $cur_page)
					? '<span class="cur_page">'.$i.'</span>'
					: $this->get_link_html($i, $i);
			}
		} else {
			if ($cur_page <= 3 || $cur_page > ($total_pages - 3)) {
				//if we're at the starting or ending of the list, the pages will appear like
				// 1,2,3,4,5...10,11,12,13,14
				for ($i = 1; $i <= 5; $i++) {
					$cur_page_disp = '<span class="cur_page">'.$i.'</span>';
					$not_cur_page_disp = $this->get_link_html($i, $i);
					$html .= ($i === $cur_page) ? $cur_page_disp : $not_cur_page_disp;
					$html .= ($i === 5) ? ' ... ' : $link_separator;
				}

				for ($i = ($total_pages - 5); $i <= $total_pages; $i++) {
					$cur_page_disp = '<span class="cur_page">'.$i.'</span>';
					$not_cur_page_disp = $this->get_link_html($i, $i);
					$html .= ($i === $cur_page) ? $cur_page_disp : $not_cur_page_disp;
					$html .= ($i === $total_pages) ? '' : $link_separator;
				}
			} else {
				//if we're somewhere in the middle of the list, the pages will appear like
				// 1,2...5,6,7...10,11,12
				$cur_plus_three = $cur_page + 3;
				$middle_start = ($cur_page <= 5) ? 3 : ($cur_page - 3);
				$middle_end = ($cur_plus_three >= ($total_pages - 1)) ? ($total_pages - 2) : $cur_plus_three;

				for ($i = 1; $i <= 2; $i++) {
					$html .= ($i === $cur_page)
						? '<span class="cur_page">'.$i.'</span>'
						: $this->get_link_html($i, $i);
					$html .= ($i === 2 && $middle_start != 3) ? ' ... ' : $link_separator;
				}

				for ($i = $middle_start; $i <= $middle_end; $i++) {
					$cur_page_disp = '<span class="cur_page">'.$i.'</span>';
					$not_cur_page_disp = $this->get_link_html($i, $i);
					$html .= ($i === $cur_page) ? $cur_page_disp : $not_cur_page_disp;
					$html .= ($i == $middle_end && ($middle_end + 1) !== ($total_pages - 1)) ? ' ... ' : $link_separator;
				}

				for ($i = ($total_pages - 1); $i <= $total_pages; $i++) {
					$cur_page_disp = '<span class="cur_page">'.$i.'</span>';
					$not_cur_page_disp = $this->get_link_html($i, $i);
					$html .= ($i === $cur_page) ? $cur_page_disp : $not_cur_page_disp;
					$html .= ($i === $total_pages) ? '' : $link_separator;
				}
			}
		}

		if ($cur_page < $total_pages) {
			$html .= $this->get_link_html(($cur_page + 1), '&rsaquo;');
			//$html .= $this->get_link_html($total_pages, '&raquo;');
		} else {
			$html .= '<span class="cur_page">&rsaquo;</span>';
			//$html .= '<span class="cur_page">&raquo;</span>';
		}
		$html .= '</span>';
		return $html;
	}
}

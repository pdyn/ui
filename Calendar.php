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

/**
 * Generates a calendar.
 */
class Calendar {
	/** @var int The year to show. */
	protected $year;

	/** @var int The month to show. */
	protected $month;

	/** @var array An array of daynum=>string, representing text to show for each day. */
	protected $dayvals;

	/**
	 * Constructor.
	 *
	 * @param int $year The year to show.
	 * @param int $month The momth to show.
	 */
	public function __construct($year = null, $month = null) {
		$this->year = (is_numeric($year)) ? $year : date('Y');
		$this->month = (is_numeric($month)) ? $month : date('m');
	}

	/**
	 * Set the text value for each day.
	 *
	 * @param array $dayvals An array of daynum=>string, representing text to show for each day.
	 */
	public function set_dayvals(array $dayvals) {
		$this->dayvals = $dayvals;
	}

	/**
	 * Get the currently set dayvals array.
	 *
	 * @return array The currently set dayvals array.
	 */
	public function get_dayvals() {
		return $this->dayvals;
	}

	/**
	 * Get the HTML for the calendar.
	 *
	 * @param bool $highlight_today Whether or not to highlight today.
	 * @return string The HTML for the calendar.
	 */
	public function get_html($highlight_today = true) {
		$first_of_month = mktime(0, 0, 0, $this->month, 1, $this->year);
		$first_day_of_week = date('w', $first_of_month);
		$days_in_month = date('t', $first_of_month);
		$today_daynum = date('j');
		$ret = '<table class="cal"><tr><th colspan="7"><h5>'.date('F', $first_of_month)
				.'</h5></th></tr><tr><th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th></tr>';
		$weeks = ceil(($days_in_month + $first_day_of_week) / 7);
		$cur_day = 1;
		for ($i = 0; $i < $weeks; $i++) {
			$ret .= '<tr>';
			for ($j = 0; $j < 7; $j++) {
				$cellnum = ($i * 7) + $j;
				$ret .= ($highlight_today === true && $cur_day == $today_daynum && $cellnum >= $first_day_of_week)
					? '<td class="today">' : '<td>';
				if ($cellnum >= $first_day_of_week && $cur_day <= $days_in_month) {
					if (isset($this->dayvals[$cur_day])) {
						$ret .= $this->dayvals[$cur_day];
					} else {
						$ret .= $cur_day;
					}
					$cur_day++;
				}
				$ret .= '</td>';
			}
			$ret .= '</tr>';
		}
		$ret .= '</table>';
		return $ret;
	}
}

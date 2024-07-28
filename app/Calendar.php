<?php
// from: https://codeshack.io/event-calendar-php/

namespace App;

use DateTime;

class Calendar
{
    private $date;
    private $active_year;
    private $active_month;
    private $active_day;
    private $events = [];

    public function __construct($date = null)
    {
        $this->date = DateTime::createFromFormat('m/Y', $date)->getTimestamp();
        $this->active_year = $date !== null ? date('Y', $this->date) : date('Y');
        $this->active_month = $date !== null ? date('m', $this->date) : date('m');
        $this->active_day = $date !== null ? date('d', $this->date) : date('d');
    }

    public function __toString()
    {
        $num_days = date('t', $this->date);
        $num_days_last_month = date('j', strtotime('last day of previous month', $this->date));
        $days = [0 => 'Di', 1 => 'Lu', 2 => 'Ma', 3 => 'Me', 4 => 'Je', 5 => 'Ve', 6 => 'Sa'];
        $first_day_of_week = date('w', strtotime('1-' . $this->active_month . '-' . $this->active_year));

        $html = '<div class="MyCalendar">';
        $html .= '<div class="header">';
        $html .= '<div class="grid no-gap">';
        $html .= '<div class="row">';
        $html .= '<div class="col-4">';
        $html .= '<a href="?date=';
        $html .= date('m/Y', strtotime('-1 month', $this->date));
        $html .= '" ';
        $html .= 'style="font-size: 30px; text-decoration: none"';
        $html .= '>&lt;&lt;</a> &nbsp; ';
        $html .= '</div>';
        $html .= '<div class="col">';

        $html .= "<select id='date' name='date' data-role='select'>";
        for ($i = -12; $i < 12; $i++) {
            $html .= '<option value="';
            $date = strtotime($i.' month', $this->date);
            $html .= date('m/Y', strtotime($i.' month', $this->date));
            $html .= '"';
            if ($i === 0) {
                $html .= ' selected';
            }
            $html .= '>';
            $html .= date('F Y', $date);
            $html .= '</option>';
        }
        $html .= '</select>';

        $html .= '</div>';
        $html .= '<div class="col-4">';
        $html .= ' <a href="?date=';
        $html .= date('m/Y', strtotime('+1 month', $this->date));
        $html .= '" ';
        $html .= 'style="font-size: 30px; text-decoration: none"';
        $html .= '>&gt&gt;</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="days">';
        foreach ($days as $day) {
            $html .= '
                <div class="day_name">
                    ' . $day . '
                </div>
            ';
        }
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '
                <div class="day_num ignore">
                    ' . ($num_days_last_month - $i + 1) . '
                </div>
            ';
        }
        for ($i = 1; $i <= $num_days; $i++) {
            $selected = '';
            if ($i === $this->active_day) {
                $selected = ' selected';
            }
            $html .= '<div class="day_num' . $selected . '">';
            $html .= '<span>' . $i . '</span>';
            foreach ($this->events as $event) {
                for ($d = 0; $d <= $event[2] - 1; $d++) {
                    if (date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day')) === date('y-m-d', strtotime($event[1]))) {
                        $html .= '<a href="/bob/show/' . $event[4] . '"><div class="event' . $event[3] . '">';
                        $html .= $event[0];
                        $html .= '</div></a>';
                    }
                }
            }
            $html .= '</div>';
        }
        $end = 42 - $num_days - max($first_day_of_week, 0);
        for ($i = 1; $i <= $end; $i++) {
            $html .= '
                <div class="day_num ignore">
                    ' . $i . '
                </div>
            ';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public function addEvent($txt, $date, $days = 1, $color = '', $id = null)
    {
        $color = $color ? ' ' . $color : $color;
        $this->events[] = [$txt == null ? "&nbsp" : $txt, $date, $days, $color, $id];
    }
}

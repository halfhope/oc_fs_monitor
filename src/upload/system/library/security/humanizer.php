<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */
namespace Security;
class Humanizer
{

    function __construct($registry)
    {
        $this->language = $registry->get('language');
    }

    public function humanDateDifference($date_start, $date_end)
    {
        $date_start = date_create($date_start);
        $datetime2  = date_create($date_end);

        $interval = date_diff($date_start, $datetime2);

        $result = array();

        if ($interval->d) {
            $result[] = $interval->format("%d " . $this->language->get('text_interval_days'));
        }
        if ($interval->h) {
            $result[] = $interval->format("%h " . $this->language->get('text_interval_hours'));
        }
        if ($interval->i) {
            $result[] = $interval->format("%i " . $this->language->get('text_interval_minutes'));
        }
        if (!$interval->i) {
            $result[] = $this->language->get('text_interval_less_than_a_minute');
        }

        $date = sprintf('%02d:%02d:%02d', $interval->h, $interval->i, $interval->s);
        return "<acronym title='$date'>" . implode(' ', $result) . "</acronym>";
    }

    public function humanBytes($size)
    {
        $filesizename = array(
            " Bytes",
            " KB",
            " MB",
            " GB",
            " TB",
            " PB",
            " EB",
            " ZB",
            " YB"
        );
        return $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
    }

    public function humanDatePrecise($date, $format = 'Y-m-d H:i:s')
    {
        $r = false;
        $a = preg_split("/[:\.\s-]+/", $date);
        $d = time() - strtotime($date);
        if ($d > 0) {
            if ($d < 3600) {
                switch (floor($d / 60)) {
                    case 0:
                    case 1:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_less_than_a_minute_ago') . "</acronym>";
                        break;
                    case 2:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_2_minutes_ago') . "</acronym>";
                        break;
                    case 3:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_3_minutes_ago') . "</acronym>";
                        break;
                    case 4:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_4_minutes_ago') . "</acronym>";
                        break;
                    case 5:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_5_minutes_ago') . "</acronym>";
                        break;
                    default:
                        return "<acronym title='$date'>" . floor($d / 60) . " " . $this->language->get('text_interval_minutes_ago') . "</acronym>";
                        break;
                }
                ;
            } elseif ($d < 18000) {
                switch (floor($d / 3600)) {
                    case 1:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_1_hour_ago') . "</acronym>";
                        break;
                    case 2:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_2_hour_ago') . "</acronym>";
                        break;
                    case 3:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_3_hour_ago') . "</acronym>";
                        break;
                    case 4:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_4_hour_ago') . "</acronym>";
                        break;
                }
                ;
            } elseif ($d < 86400) {
                if (date('d') == $a[2]) {
                    return "<acronym title='$date'>" . $this->language->get('text_interval_today_in') . " {$a[3]}:{$a[4]}</acronym>";
                }
                if (date('d', time() - 86400) == $a[2]) {
                    return "<acronym title='$date'>" . $this->language->get('text_interval_yesterday_in') . " {$a[3]}:{$a[4]}</acronym>";
                }
            }
        } else {
            $d *= -1;
            if ($d < 3600) {
                switch (floor($d / 60)) {
                    case 0:
                    case 1:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_right_now') . "</acronym>";
                        break;
                    case 2:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_in_2_minutes') . "</acronym>";
                        break;
                    case 3:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_in_3_minutes') . "</acronym>";
                        break;
                    case 4:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_in_4_minutes') . "</acronym>";
                        break;
                    case 5:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_in_5_minutes') . "</acronym>";
                        break;
                    default:
                        return "<acronym title='$date'>" . sprintf($this->language->get('text_interval_in_minutes'), floor($d / 60)) . "</acronym>";
                        break;
                }
                ;
            } elseif ($d < 18000) {
                switch (floor($d / 3600)) {
                    case 1:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_in_an_hour') . "</acronym>";
                        break;
                    case 2:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_in_2_hours') . "</acronym>";
                        break;
                    case 3:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_in_3_hours') . "</acronym>";
                        break;
                    case 4:
                        return "<acronym title='$date'>" . $this->language->get('text_interval_in_4_hours') . "</acronym>";
                        break;
                }
                ;
            } elseif ($d < 86400) {
                if (date('d') == $a[2]) {
                    return "<acronym title='$date'>" . $this->language->get('text_interval_today_at') . " {$a[3]}:{$a[4]}</acronym>";
                }
                if (date('d', time() - 86400) == $a[2]) {
                    return "<acronym title='$date'>" . $this->language->get('text_interval_tomorrow_at') . " {$a[3]}:{$a[4]}</acronym>";
                }
            }
            $d *= -1;
        }
        $r = "{$a[2]}.{$a[1]}";
        if ($a[0] != date('Y') OR $d > 0) {
            $r .= '.' . $a[0];
        }
        $r .= " {$a[3]}:{$a[4]}";
        $date = date_format(new \DateTime($date), $format);
        return "<acronym title='$date'>$date</acronym>";
    }

}
?>
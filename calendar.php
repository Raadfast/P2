<?php


function tep_generate_calendar($year, $month, $selected_dates= array(), $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
	$first_of_month = gmmktime(0,0,0,$month,1,$year);
	//remember that mktime will automatically correct if invalid dates are entered
	// for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
	// this provides a built in "rounding" feature to generate_calendar()

	$day_names = array(); #generate all the day names according to the current locale
	for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
		$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

	list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
	$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
	$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

	//Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
	@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
	if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
	if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
	$calendar = '<table class="borderTable">'."\n".
		'<caption class="calendar-month">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>";

	if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
		//if day_name_length is >3, the full name of the day will be printed
		foreach($day_names as $d)
			$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
		$calendar .= "</tr>\n<tr>";
	}

	if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
	for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
		if (strlen($day) == 1) {
			$dday = '0'.$day;
		}
		else
		{
			$dday = $day;
		}
		$search_date = $year.'-'.$month.'-'.$dday;

		if($weekday == 7){
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}
		if(isset($days[$day]) and is_array($days[$day])){
			@list($link, $classes, $content) = $days[$day];
			if(is_null($content))  $content  = $day;
			if (in_array($search_date, $selected_dates)) {
				$calendar .= '<td '.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
					($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'<input name="selected_date[]" value="'.$year.'-'.$month.'-'.$dday.'" type="checkbox" CHECKED></td>';
			}
			else
			{
				if (in_array($search_date,$selected_dates)) {
					$calendar .= '<td '.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
						($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'<input name="selected_date[]" value="'.$year.'-'.$month.'-'.$dday.'" type="checkbox" CHECKED></td>';
				}
				else
				{
					$calendar .= '<td '.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
						($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'<input name="selected_date[]" value="'.$year.'-'.$month.'-'.$dday.'" type="checkbox"></td>';
				}
			}
		}
		else
		{
			if (in_array($search_date, $selected_dates)) {
				$calendar .= "<td  class='borderTable'>".$day."<input name='selected_date[]' value='".$year.'-'.$month.'-'.$dday."' type='checkbox' CHECKED></td>";
			}
			else
			{
				$calendar .= "<td  class='borderTable'>".$day."<input name='selected_date[]' value='".$year.'-'.$month.'-'.$dday."' type='checkbox'></td>";
			}
		}
	}
	if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

	return $calendar."</tr>\n</table>\n";
}




function tep_schedule_calendar($year, $month, $schedule_array, $selected_dates= array(), $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
//	var_dump($schedule_array);
	$first_of_month = gmmktime(0,0,0,$month,1,$year);
	//remember that mktime will automatically correct if invalid dates are entered
	// for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
	// this provides a built in "rounding" feature to generate_calendar()

	$day_names = array(); #generate all the day names according to the current locale
	for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
		$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

	list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
	$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
	$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

	//Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
	@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
	if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
	if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
	$calendar = '<table class="borderTable" width="100%">'."\n".
		'<caption class="calendar-month">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>";

	if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
		//if day_name_length is >3, the full name of the day will be printed
		foreach($day_names as $d)
			$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
		$calendar .= "</tr>\n<tr>";
	}
	if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
	for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){

		if (strlen($day) == 1) {
			$dday = '0'.$day;
		}
		else
		{
			$dday = $day;
		}
		$search_date = $year.'-'.$month.'-'.$dday;
		// test if there are courses on this date from schedule_array
		$calendar_entry_array = array();
		foreach ($schedule_array as $y => $z) {
			if ($search_date == $z['date']) {
				$calendar_entry_array[] = array('id' => $z['id'],
											   'name' => tep_get_name(TABLE_COURSES, $z['course_id']),
											   'max' => $z['max'],
											   'reg' => $z['reg'],
											   'course_id' => $z['course_id']);

			}
		}
		if($weekday == 7){
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}
		if(isset($days[$day]) and is_array($days[$day])){
			@list($link, $classes, $content) = $days[$day];
			if(is_null($content))  $content  = $day;
			if (in_array($search_date, $selected_dates)) {
				$calendar .= '<td width="12%" '.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
					($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).' </td>';
			}
			else
			{
				if (in_array($search_date,$selected_dates)) {
					$calendar .= '<td width="12%" '.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
						($link ? '<a href="'.htmlspecialchars($link).'">'.$content.' </a>' : $content).' </td>';
				}
				else
				{
					$calendar .= '<td width="12%" '.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
						($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).' </td>';
				}
			}
		}
		else
		{
				$calendar .= "<td  class='borderTable' width='12%' valign='top'>".$day;
				if (count($calendar_entry_array) > 0) {
					foreach ($calendar_entry_array as $g => $h) {
						if (isset($_GET['action']) && isset($_GET['id']) && strlen($_GET['action']) > 0) {
							if ($_GET['action'] == 'register') {
								$calendar .= '<br><a href="course_register.php?id='.$h["id"].'&user_id='.$_GET['id'].'">'.$h['name'].'</a>';
							}
						}
						else
						{
							$places=$h['max']-tep_get_status_amount(6,$h['id']);
							If ($places>0)
							$calendar .= '<br><a href="course_register.php?id='.$h["id"].
										'&course_id='.$h['course_id'].'">'.$h['name'].'</a>'.
										'<font size="1">('.$places.')</font>';
							Else $calendar .= '<font color="red"><br>'.$h['name'].
										'<font size="1"> (full)</font>'; 			
						}
					}

				}
				else
				{
					$calendar .= '<br><br><br>';
				}
				$calendar .="</td>";
		}
	}
	if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

	return $calendar."</tr>\n</table>\n";
}





//tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$_GET['id']."'");
/*
*/

function tep_set_new_reg_type($user_id, $calendar_id, $new_status, $registered, $waiting_list, $max_attendance) {

	$scheduled = new scheduled(tep_get_course_id($calendar_id));
	$scheduled->set_calendar_vars($calendar_id);

	$result = tep_db_query("select status from registrations where user_id = '$user_id' and calendar_id = '$calendar_id'");
	$row = tep_db_fetch_array($result);
	$current_status = $row['status'];
	if ($scheduled->cancelled) {
		return false;
	}
	else if ($current_status == $new_status) {
		//no change
		return true;
	}
	else if ($new_status == '4') {
		if ($current_status == '2') {
			$sql_array = array('waiting_list' => ($waiting_list - 1),
							   'mDate' => date("Y-m-d H:i:s"));
			tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$calendar_id."'");
			return true;
		}
		else if (($current_status == '1') || ($current_status == '3')) {
				if ($waiting_list == '0') {
					$sql_array = array('registered' => $registered - 1,
							   		   'mDate' => date("Y-m-d H:i:s"));
					tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$calendar_id."'");
				}
				else
				{
					$sql_array = array('waiting_list' => $waiting_list-1,
								   	   'mDate' => date("Y-m-d H:i:s"));
					tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$calendar_id."'");
					$query = "select id, min(cDate)'cDate' from registrations where calendar_id = '".$calendar_id."' and status='2' group by cDate LIMIT 1";
					$result = tep_db_query($query);
					if (tep_db_num_rows($result) > 0) {
						$row = tep_db_fetch_array($result);
						$sql_array = array('status' => '3',
										   'mDate' => date('Y-m-d H:i:s'));
						tep_db_perform(TABLE_REGISTRATIONS, $sql_array, 'update', "id = '".$row['id']."'");
					}
				}

				return true;
		}
	}
	else if (($new_status == '1') || ($new_status == '3')) {
		if ($registered >= $max_attendance) {
			if (($current_status == '3') && ($new_status == '1')) {
				return true;
			}
			elseif (($new_status == '3') && ($current_status == '1')) {
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if (($current_status == '3') && ($new_status == '1')) {
				return true;
			}
			else if (($new_status == '3') && ($current_status == '1')) {
				return true;
			}
			else if ($current_status == '2') {
				$sql_array  = array('registered' => $registered + 1,
									'mDate' => date("Y-m-d H:i:s"));
				tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$calendar_id."'");
				$sql_array  = array('waiting_list' => $waiting_list - 1,
									'mDate' => date("Y-m-d H:i:s"));
				tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$calendar_id."'");
				return true;
			}
			else
			{
				$sql_array  = array('registered' => $registered + 1,
									'mDate' => date("Y-m-d H:i:s"));
				tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$calendar_id."'");
				return true;
			}
		}
	}
	else if ($new_status == '2') {
		if (($current_status == '1') || ($current_status == '3')) {
			$sql_array = array('registered' => $registered - 1,
							   'mDate' => date("Y-m-d H:i:s"));
			tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$calendar_id."'");
		}
		$sql_array  = array('waiting_list' => $waiting_list + 1,
							'mDate' => date("Y-m-d H:i:s"));
		tep_db_perform(TABLE_CALENDAR, $sql_array, 'update', "id = '".$calendar_id."'");
		return true;
	}

}






?>

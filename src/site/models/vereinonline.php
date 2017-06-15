<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class VereinOnlineModelVereinOnline extends JModelItem
{
	protected $items;

	public function getItems()
	{
		if (!isset($this->items))
		{
			$jinput = JFactory::getApplication()->input;
			$splitYear = $jinput->get('splitYear', 1, 'checkbox');
			$splitMonth = $jinput->get('splitMonth', 1, 'checkbox');

			$this->items = array();
			foreach($this->GetCalendar() as $calItem)
			{
				array_push($this->items, $calItem);
			}
			$this->items = $this->InsertSplitter($this->items, $splitYear, $splitMonth);
		}
		return  $this->items;
	}

	private function GetCalendar()
	{
		$items = array();
		$daten=$this->VereinOnlineRequest("GetEvents", array(
			"typ"=>"1",
			"jahr" => "zukunft"
			));
		if ($daten->error!="") 
		{
			$item = array();
			$item["Datum"] = $text;
			array_push($items, $item);
		}
		else
		{
			foreach($daten as $eventItem)
			{
				$item = array();
				$item["Jahr"] = $this->GetYear($eventItem->datum);
				$item["Monat"] = $this->GetMonth($eventItem->datum);
				$item["Datum"] = $this->FormatDate($eventItem->datum);
				$item["Veranstaltung"] = $eventItem->titel;
				$item["Ort"] = $eventItem->ort;
				array_push($items, $item);
			}
		}

		return $items;
	}

	private function InsertSplitter($items, $splitYear, $splitMonth)
	{
		$year = "";
		$month = "";
		$pimpedItems = array();
		foreach($items as $item)
		{
			if($splitYear && $year !== $item["Jahr"])
			{
				$year = $item["Jahr"];
				$splitter = array();
				$splitter["Datum"] = "$year";
				$splitter["Veranstaltung"] = "";
				$splitter["Ort"] = "";
				array_push($pimpedItems, $splitter);
			}
			if($splitMonth && $month !== $item["Monat"])
			{
				$month = $item["Monat"];
				$splitter = array();
				$splitter["Datum"] = $this->FormatMonth($month);
				$splitter["Veranstaltung"] = "";
				$splitter["Ort"] = "";
				array_push($pimpedItems, $splitter);
			}
			array_push($pimpedItems, $item);
		}
		return $pimpedItems;
	}

	

	private function VereinOnlineRequest($funktion, $daten)
	{
		$jinput = JFactory::getApplication()->input;
		$vo_id = $jinput->get('vo_id', 1, 'text');
		$url = "http://www.vereinonline.org/".$vo_id."/";
		$usr = $jinput->get('vo_user', 1, 'text');
		$pwd = $jinput->get('vo_password', 1, 'password');
		$token = "A/$usr/".md5($pwd);


		$url.="?json";
		$url.="&function=$funktion";
		foreach($daten as $k=>$v) $url.="&$k=".urlencode($v);
		//$url.="&token=$token";

		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$data = json_decode(curl_exec($curl));
		curl_close($curl);
		return $data;
	}


	private function FormatDate($date)
	{
		$parsed = date_parse($date);
		if($parsed->error_count > 0)
			return $date;
		else
		{
			$datValue = mktime(0,0,0,$parsed["month"],$parsed["day"],$parsed["year"]);
			return $this->DayName(date("N", $datValue)) . " " . date("d.m.y", $datValue);
		}
	}

	private function FormatMonth($month)
	{
		switch($month)
		{
			case "01":
				return "Januar";
			case "02":
				return "Februar";
			case "03":
				return "März";
			case "04":
				return "April";
			case "05":
				return "Mai";
			case "06":
				return "Juni";
			case "07":
				return "Juli";
			case "08":
				return "August";
			case "09":
				return "September";
			case "10":
				return "Oktober";
			case "11":
				return "November";
			case "12":
				return "Dezember";
		}
		return "";
	}

	private function GetYear($date)
	{
		$parsed = date_parse($date);
		if($parsed->error_count > 0)
			return "";
		else
		{
			$datValue = mktime(0,0,0,$parsed["month"],$parsed["day"],$parsed["year"]);
			return date("Y", $datValue);
		}
	}
	private function GetMonth($date)
	{
		$parsed = date_parse($date);
		if($parsed->error_count > 0)
			return "";
		else
		{
			$datValue = mktime(0,0,0,$parsed["month"],$parsed["day"],$parsed["year"]);
			return date("m", $datValue);
		}
	}

	private function DayName($weekday)
	{
		switch ($weekday) 
		{
			case "1":
				return "Montag";
			case "2":
				return "Dienstag";
			case "3":
				return "Mittwoch";
			case "4":
				return "Donnerstag";
			case "5":
				return "Freitag";
			case "6":
				return "Samstag";
			case "7":
				return "Sonntag";
			
		}
		return $weekday;
	}
}
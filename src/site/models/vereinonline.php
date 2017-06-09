<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class VereinOnlineModelVereinOnline extends JModelItem
{
	protected $items;

	public function extractColumns($firstColumn)
	{
		$columns = array();
		foreach($firstColumn->getElementsByTagName('td') as $column)
		{
			array_push($columns, $column->textContent);
		}
		return $columns;
	}
	public function tail($doc)
	{
		$rows = array();
		$first =0;
		foreach($doc->getElementsByTagName('tr') as $row)
		{
			if($first==1)
				array_push($rows, $row);
			$first=1;
		}
		return $rows;
	}
	public function extractData($rows, $columns)
	{
		$items = array();
		foreach($rows as $row)
		{
			$item = array();
			for($i=0; $i<count($columns); $i++)
			{
				$text = $row->getElementsByTagName("td")->item($i)->textContent;
				$column = $columns[$i];
				$item[$column] = $text;
			}
			array_push($items, $item);
		}
		return $items;
	}
	public function loadDataFrom($url)
	{
        $doc = new DOMDocument();
        $doc->loadHTMLFile($url);
        $table = $doc->getElementsByTagName('table')->item(0);
        $firstRow =$doc->getElementsByTagName('tr')->item(0);
        $columns = $this->extractColumns($firstRow);
        $rows = $this->tail($table);
        $data = $this->extractData($rows,$columns);
		return $data;
    }

	public function getItems()
	{
		if (!isset($this->items))
		{
			$jinput = JFactory::getApplication()->input;
            $vo_id = $jinput->get('vo_id', 1, 'text');
			$url = "https://www.vereinonline.org/".$vo_id."/?action=events_kalender&year=2017&art=ListeZ";
			$this->items = $this->loadDataFrom($url);
		}
		return $this->items;
	}
}
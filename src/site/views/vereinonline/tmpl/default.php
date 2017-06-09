<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<h1>Termine</h1>
<table>
    <thead>
    <tr>
        <th>Datum</th>
        <th>Veranstaltung</th>
        <th>Ort</th>
    </tr>
    </thead>
    <tbody>
        <?php
            foreach($this->calendarRows as $row)
            {
                $colspan = 1;
                $colspanText = "";
                $columnTag = "td";

                if($row["Veranstaltung"]=== "") $colspan++;
                if($row["Ort"]=== "") $colspan++;

                if($colspan > 1)
                {
                    $colspanText = "colspan='$colspan'";
                    $columnTag = "th";
                }
                    
                echo "<tr>\r\n";
                echo "<$columnTag $colspanText>".$row["Datum"]."</$columnTag>\r\n";
                if($row["Veranstaltung"]!== "")
                    echo "<td>".$row["Veranstaltung"]."</td>\r\n";
                if($row["Ort"]!== "")
                    echo "<td>".$row["Ort"]."</td>\r\n";
                echo "</tr>\r\n";
            }
        ?>
    </tbody>
</table>
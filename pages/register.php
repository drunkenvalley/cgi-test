<form action="api/meter.php?add" method="post" id="insert-form">
    <input type="text" name="meter_id" placeholder="Meter" value="2aea445"/><br/>
    <input type="text" name="customer_id" placeholder="Customer" value="nsd98f"/><br/>
    <input type="text" name="day" placeholder="Date" value="2018-08-26"/>
<?php

for($i = 0; $i < 24; $i++)
{
    $dd = sprintf("%02s",$i); //Just makes sure it's double digit for timestamp.
    echo "
    <div>
        <input class='insert-value' name='T$dd:00:00Z' type='text' 
        placeholder='Value #$i' value='$i' />
    </div>\n";
}
?>
    <input type="submit" id="insert-submit"/>
</form>
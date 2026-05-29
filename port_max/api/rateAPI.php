<?php
if(isset($_GET['data']))
{
	file_put_contents('rate.txt',$_GET['data']) ;
}
?>
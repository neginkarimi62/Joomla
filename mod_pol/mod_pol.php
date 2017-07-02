<html>
<head>
<style>
ul{	
width:300px;	
list-style-type: none;
font-family: Verdana, Arial, Helvetica, sans-serif;	
font-size: 12px;	
line-height: 2em;
}
ul li{	
margin-bottom:2px;
padding:2px;
}
ul li.result{
position: relative;
}
ul li.result span.bar{
background-color: #D8DFEA;  
  display: block;  
  left: 0;   
 position: absolute;   
 top: 0; 
   z-index: -1;
}
</style>
</head>
<body>
 
<?php
defined('_JEXEC') or die;
require_once __DIR__ . '/helper.php';
echo' <h1>The voting system for Books </h1>';
//include 'vote.php';
$vote  = new Vote();
$action='';
//get action
if(isset($_REQUEST['action'])){
$action = $_REQUEST['action'];
}
//vote and show results
if('vote'==$action){		
//vote	
$vote->vote($_REQUEST['id'],$_REQUEST['group_id']);
$totalvotes= $vote->getTotalVotes($_REQUEST['group_id']);
//$vote->showResults($_REQUEST['group_id']);
//show results		
$total=$vote->getTotal($_REQUEST['group_id']);
echo' <h2>This is the result of voting </h2>';
echo '<ul>';

foreach($vote->showResults($_REQUEST['group_id']) as $vote){		
$percent = 0;	
	if(isset($vote['number'])&&!empty($vote['number'])){			$percent = $vote['number']/$total * 100;	
	}	
	
	echo '<li class="result">';		
echo '<span class="bar" style="width:'.$percent.'%;">&nbsp;</span>';		
echo '<span class="label">'.$vote['name'].'&nbsp;(<strong>'.$percent.'%</strong>)</span>';	
	echo '</li>';	
}	 echo '<h4> Total Votes Are:'.$totalvotes.'</h4>';
    echo '</ul>';  
//show options
}else{
	echo '<ul>';	
foreach($vote->getList() as $option){	
	echo '<li>';	
	echo '<a href="'.$_SERVER['PHP_SELF'].'?action=vote&&id='.$option['id']. ',&&group_id=' .$option['group_id'].'">'.$option['name'].    ' <img src="' .$option['image'].'"> </a>';		
echo '</li>';	
}

	echo '</ul>';
	}
	
?>

</body>
</html>
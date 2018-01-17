<html>
<head>

</head>
<body>
 
<?php
defined('_JEXEC') or die;
require_once  __DIR__ . '/helper.php';
$Library  = new Lib();
$userinfo = $Library->register();	
$currentTime = new JDate('now'); 
$ch = $Library->searchDatabase($userinfo['username']);

if(empty($ch)){
    $libCard = $Library->asignLibrarycard();
    if($Library->CheckValidity($libCard->small)){
        $libCardNumber = $Library->updateTable($libCard->small);
        $htmlc2=$params->get('title2');
        echo '<h1 class="rt-center">Book Collection</h1>';
        echo '<p class="rt-center">';
	    echo $htmlc2;
        echo '<br>';
        echo '<span  class="label label-default" style="background-color: #68217A; letter-spacing: 10px">';
        echo $libCard->librarycard; 
        echo '</span>';
        echo '<br>';
        echo '<a class="readon2 rt-big-button" title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Note: You will need the Library card number to borrow the books." target="_blank" href="http://Yoururl.com/">View Collection</a>';
        echo '</p>';
    }
    else {
        $isvalid = 0;
        $id=($libCard->small);
        $count=$Library->countRows();
        $Rows=$count->crows;
        $id1=0;
        while(($isvalid==0)&&($id1<=$Rows)){
            $id1++;
            $id = $id+1;
            $isvalid = $Library->CheckValidity($id);
        }
        if($isvalid==0){
            echo '<h3> Sorry There is no library Card Available.';
        }
        else {
            $Library->updateTable($id);
            $cardlib = $Library->getLibrarycard($id);
            $htmlc2 = $params->get('title2');
            echo '<h1 class="rt-center">Book Collection</h1>';
            echo '<p class="rt-center">';					
		    echo $htmlc2;
            echo '<br>';
            echo '<span class="label label-default" style="background-color: #68217A; letter-spacing: 10px">';
            echo $cardlib;  
            echo '</span>';
            echo '<br>';
            echo '<a class="readon2 rt-big-button" title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Note: You will need the Library card number to borrow the books." target="_blank" href="http://asiaread.lib.overdrive.com/">View Collection</a>';
            echo '</p>';
        }
    }
}
else {
    if (($currentTime)>($ch->expiarydate)){
        echo '<h1 class="rt-center">Book Collection</h1>';
        echo '<p class="rt-center">';
	    $htmlc1=$params->get('title1');
	    echo $htmlc1;
        echo '<br>';
	}
	else {
	    if ($ch->status=='No'){
            echo '<h1 class="rt-center">Book Collection</h1>';
            echo '<p class="rt-center">';					
            $htmlc4 = $params->get('title4');
			echo $htmlc4;
            echo '<br>';
			}
		else {
			$htmlc3 = $params->get('title3');
			echo '<h1 class="rt-center">Book Collection</h1>';
            echo '<p class="rt-center">';
			echo $htmlc3;
            echo '<br>';
			echo '<span class="label label-default" style="background-color: #68217A; letter-spacing: 10px">';
			echo $ch->librarycard;
			echo '</span>';
            echo '<br>';
            echo '<a class="readon2 rt-big-button" title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Note: You will need the Library card number to borrow the books." target="_blank" href="http://asiaread.lib.overdrive.com/">View Collection</a>';
            echo '</p>';
			}
		}
}

?>

</body>
</html>

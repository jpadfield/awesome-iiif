<?php

$aws = file ("./raw_awsome.txt");
$parent = false;
$current = false;
$grp = false;
$list = array();
$mn = 1;
$myfile = fopen("awsome.json", "w");

foreach ($aws as $k => $line)
	{
	// Need to recatch
	$child = false;
	echo $line."";
	if (preg_match("/^[#][#][#][ ]*(.+)[\s]*$/", $line, $m))
		{$current = trim($m[1]);
			$grp = "$parent|$current";
		  echo "$parent|$m[1] \n";
		  $child = true;}
	else if (preg_match("/^[#][#][ ]*(.+)[\s]*$/", $line, $m))
		{$parent = trim($m[1]);
		 $current = trim($m[1]);
		 $grp = "$current";
		 echo trim($m[1])."\n";}

//  - [ContentDM](https://www.oclc.org/en/contentdm/iiif.html)
	else if (preg_match("/^[ ]*[-][ ]*\[([^\]]+)\]\(([^\)]+)\)(.*)[\s]*$/", $line, $m))
		{
		prg(0, $m);
		$m[1] = preg_replace('/["]/', '\"', $m[1]);
		
		$m[3] = preg_replace_callback('/\[([^\]\[]+)\]\(([^\)\(]+)\)/', 'addLinks', $m[3]);
		$m[3] = preg_replace_callback('/[^"\'=]http[^\s]+/', 'addLinks', $m[3]);
		$m[3] = preg_replace('/["]/', '\"', $m[3]);
		$m[3] = preg_replace('/^[\s]*[-]*[\s]*/', '', $m[3]);

		if ($m[3] == ".") {$m[3] = "";}
		
		prg(0, $m);
		
		ob_start();
		echo <<<END
			{
			"groups": ["$grp"],
			"ptitle": "$m[1]",
			"stitle": "",
			"comment": "$m[3]",
			"image": "",
			"link": "$m[2]"
			}
END;
    $list[] = ob_get_contents();
		ob_end_clean(); // Don't send output to client
		
		}
	else if (preg_match("/^[-][ ]*(.+)[\s]*$/", $line, $m))
		{echo "[$mn] MISSED - $line\n";
		 $mn++;}
	else if (trim($line))
		{//echo "[$mn] WEIRD||$line\n";
			//$mn++;
			}
	}

fwrite($myfile, implode(",\n", $list));
fclose($myfile);

//prg(0, $list);
exit;


//////////////////////////

function addLinks($matches) {
	if (count($matches) > 1)
		{echo "## ADDLINKS A ##\n";prg(0, $matches);
		 $out = "<a href='$matches[2]'>$matches[1]</a>";}
	else
		{echo "## ADDLINKS B ##\n";prg(0, $matches);
			$out = "<a href='$matches[0]'>$matches[0]</a>";}
  return($out);
}
 
function prg($exit=false, $alt=false, $noecho=false)
	{
	if ($alt === false) {$out = $GLOBALS;}
	else {$out = $alt;}
	
	ob_start();
	//echo "<pre class=\"wrap\">";
	if (is_object($out))
		{var_dump($out);}
	else
		{print_r ($out);}
	echo "\n";//</pre>";
	$out = ob_get_contents();
	ob_end_clean(); // Don't send output to client
  
	if (!$noecho) {echo $out;}
		
	if ($exit) {exit;}
	else {return ($out);}
	}
//*/
?>

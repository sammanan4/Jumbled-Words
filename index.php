<?php
	ini_set('max_execution_time', 300); //300 seconds = 5 minutes
	include "DBConnect.php";
	$time=0; $queries=0;$StoredWords;
	// write the permutation algorithm

	function next_permutation($length, &$letters){
		//start the algorithm
		for ($i= $length - 1 ; $i > 0; $i--){
			
			//if the string at index i-1 is smaller than string at index i
			if ( $letters[$i-1] < $letters[$i] ){
				
				//from the last string downto the string at index i
				for ($j = $length-1; $j >= $i; $j--){
					
					//if the string at index i-1 is less than string at index j 
					if ( $letters[$i-1] < $letters[$j] ) {
						
						//then swap the 2 strings
						$temp = $letters[$i-1];
						$letters[$i-1] = $letters[$j];
						$letters[$j]= $temp;
						
						//after swapping the two strings
						//reverse the order of the strings from index i to the last
						
						for ($k = $i, $m = 1; $k < ((($length - $i) / 2) + $i); $k++, $m++){
							$temp = $letters[$k];
							$letters[$k] = $letters[$length - $m];
							$letters[$length - $m] = $temp;
						}
						return true;
					}
				}
			}
		}
		return false;
	}

	if(isset($_POST["find"]) && strlen($_POST["alpha"]) > 1){
		
		// split the string into an array

		$str = $_POST["alpha"];

		$length = strlen($str);

		$str = mysqli_real_escape_string($connect, $str); 

		$letters = str_split($str);

		// sort the characters in the array

		sort($letters);

		$str = implode('', $letters);

		// call the permutation algorithm
		do{
		
			// for every next permutation check the database

			for($smaller = 3; $smaller <= $length; $smaller++){
				
				$str_cur = substr($str, 0, $smaller);
				
				if(!isset($StoredWords[$str_cur])) {

					//if the word is not already queried
					$StoredWords[$str_cur] = '';
					$Query = "select word, definition from entries where word = " . "'" . $str_cur . " and length(definition) in (select max(len) from (select length(definition) as len from entries where word = " . "'" . $str_cur . "') as temptable)";
					// $Query = "select * from entries where word = " . "'" . $str_cur . "' LIMIT 1";
					
					$s = time();
					$result = $connect->query($Query);
					$GLOBALS['queries']++;
					$e = time();
					
					$GLOBALS['time'] += $e - $s;

					// if match found, then store in an associative array
					if($row = $result->fetch_assoc()){
									
						$myArr[$str_cur] = $row['definition'];
								
					}
				
				}
			}
		 				
		 	// else carry on the algorithm


		} while (next_permutation($length, $str));

	}
?>

<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
	<title>Jumbled Words</title>
	<link rel="stylesheet" type="text/css" href="Style.css">
</head>
<body>
	<div id="in">
		<h2>Find The Words</h2>
			<form action="#" method="POST">
				<input id="text" type="text" name="alpha" required >
				<input type="submit" name="find" value="Find">
			</form>
	</div>
	<br><br>
	<div class="display">
	<?php
		if(isset($myArr)){
    ?>
            <table>
	        <th>Word</th>
	        <th>Meaning</th>
    <?php
			foreach ($myArr as $key => $value) {
                echo "<tr>";
				echo "<td width=\"10%\" height=\"5%\">" . $key . "</td><td style=\"text-align:left\" height=\"5%\">" . $value . "</td>";
                echo "</tr>";
			}
			echo  $GLOBALS['time'] . "<br>" . $GLOBALS['queries'];
	?>
	</table>
	<?php
        }
    ?>
	</div>
</body>
</html>
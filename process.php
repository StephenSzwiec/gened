<?php
/*
 * process.php
 *
 * Copyright 2019 Stephen Szwiec <stephen.szwiec@ndus.eduz>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 *
 */

 /*
  * This php processes the user input from the main page and queries SQL to return a list of classes
  *
  */

//process.php


if ($_SERVER["REQUEST_METHOD"] == "POST") {//Check it is coming from a form
	$classes = $_POST["creditCheck"];
	$intersect = $_POST["intersectCheck"];
	$servername = "localhost";
	$username = "X";
	$password = "Y";
	$dbname = "Z";

	//create a new connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	//check to see if the connection actually works
	if ($conn->connect_error){
		die("Connection to mySQL failed: " .$conn->connect_error);
	}

	if(empty($classes)){//empty box handling
		die("Error: No boxes clicked. \n Please select credits to check classes.");
	}

	else if(!empty($intersect)) { //if intersect is selected, generate an sql query by looping to select only intersecting values
	    $string = "SELECT * FROM classes WHERE";
        for($x = 0; $x < count($classes); $x++){
            $string = $string . " $classes[$x] = 1 ";
            if($x != count($classes) -1 ){
                $string = $string."AND ";
            }
            else {
                $string = $string.";";
            }
        }
        echo "<h3> The following provide ";
        for($x = 0; $x < count($classes); $x++){
            echo "$classes[$x]";
            if($x != count($classes) -1){
                echo " and ";
            }
            else{
                echo ": </h3>";
            }
        }
        if($result = mysqli_query($conn, $string)){
			if(mysqli_num_rows($result) > 0){
				echo "<table>";
					echo "<tr>";
						echo "<th>Number</th>";
						echo "<th>Class Name</th>";
					echo "</tr>";
					while($row = mysqli_fetch_array($result)){
			    		echo "<tr>";
							echo "<td>" . $row['className'] . "</td>";
							echo "<td>". $row['engName'] . "</td>";
					    echo "</tr>";
				    }
				echo "</table>";
				//free result set
				mysqli_free_result($result);
			}
			else{
				echo "No records matching query were found.";
			}
		}
		else{
			echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
		}
	    //close connection
	    mysqli_close($conn);
    }

	else if(empty($intersect)){ //if union is selected, return both lists
		foreach($classes as $value) {
			$sql = "SELECT * FROM classes WHERE $value = 1";
			echo "<h3> The following provide $value</h3>";
			if($result = mysqli_query($conn, $sql)){
				if(mysqli_num_rows($result) > 0){
					echo "<table>";
						echo "<tr>";
							echo "<th>Number</th>";
							echo "<th>Class Name</th>";
						echo "</tr>";
						while($row = mysqli_fetch_array($result)){
							echo "<tr>";
								echo "<td>" . $row['className'] . "</td>";
								echo "<td>". $row['engName'] . "</td>";
							echo "</tr>";
						}
					echo "</table>";
					//free result set
					mysqli_free_result($result);
				}
				else{
					echo "No records matching query were found.";
				}
			}
			else{
				echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
			}
		}
    	//close connection
	    mysqli_close($conn);
	}

	else{
		die("Error: unhandled state");
	}
}

?>

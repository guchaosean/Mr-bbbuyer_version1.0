<?php

session_start();
$userinputmath=$_POST["userinputmath"];
					
					$piereg_cookie_array =  $_SESSION["checkmathpa"];

					if ($userinputmath==$piereg_cookie_array){ echo "true";}
                    else{echo "false";}
					 


?>
<?php

    $urlParams = explode('/', $_SERVER['REQUEST_URI']);
    $functionName = $urlParams[2];
    $functionName($urlParams);

    function connectToDB()
    {
      $servername = "localhost";
      $username = "root";
      $password = "";
      $database = "radio";
      
      // Create connection
      $conn = mysqli_connect($servername, $username, $password, $database);
      
      // Check connection
      if (!$conn) {
          die("Connection failed: " . mysqli_connect_error());
      }
      //echo "Connected successfully";
      return $conn;
    }


    // http://localhost/radio.php/getChannels
    function getChannels()
    {
       $conn = connectToDB();

       $sql="SELECT * FROM channels";
   
       $result = $conn->query($sql);

       if ($result->num_rows > 0) {
           // output data of each row
           while($row = $result->fetch_assoc()) {
              echo "id: " . $row["id"]. " | Name: " . $row["name"]. " | URL: " . $row["url"]. " | isFavourite: " . $row["isFavourite"] . "<br>";  
            }
       } else {
           echo "0 results";
       }
       $conn->close();
    }

    // http://localhost/radio.php/getChannel/1
    function getChannel($channelId)
    {
       $conn = connectToDB();

       $sql="SELECT * FROM channels WHERE id = " . $channelId[3];
   
       $result = $conn->query($sql);

       if ($result->num_rows > 0) {
           // output data of each row
           while($row = $result->fetch_assoc()) {
              echo "id: " . $row["id"]. " | Name: " . $row["name"]. " | URL: " . $row["url"] . " | isFavourite: " . $row["isFavourite"];   
            }
       } else {
           echo "0 results";
       }
       $conn->close();
    }

    // http://localhost/radio.php/getSelectedChannel
    function getSelectedChannel()
    {
        $conn = connectToDB();

        $sqlCommands="SELECT channel_id FROM commands";

        $resultCommands = $conn->query($sqlCommands);

        $selectedChannelId = 0;

        if ($resultCommands->num_rows > 0) {
            // output data of each row
            while($row = $resultCommands->fetch_assoc()) {
                $selectedChannelId = $row["channel_id"];
            }
        } 

        if ($selectedChannelId == 0)
        {
            echo "Kayıtlı kanal bulunmamakta";
            $conn->close();
            //break;
        }

        $sqlChannels = "SELECT * FROM channels WHERE id = " . $selectedChannelId;

        $resultChannels = $conn->query($sqlChannels);

        if ($resultChannels->num_rows > 0) {
        // output data of each row
        while($row = $resultChannels->fetch_assoc()) {
            echo "id: " . $row["id"]. " | Name: " . $row["name"]. " | URL: " . $row["url"] . " | isFavourite: " . $row["isFavourite"];   
        }
    } 
        $conn->close();
    }

    // http://localhost/radio.php/volumeUp
    function volumeUp()
    {
        $conn = connectToDB();
        $sql="UPDATE commands SET volume_up = 1";
        $conn->query($sql);
        $conn->close();
    }

    // http://localhost/radio.php/volumeDown
    function volumeDown()
    {
        $conn = connectToDB();
        $sql="UPDATE commands SET volume_down = 1";
        $conn->query($sql);
        $conn->close();
    }

    // http://localhost/radio.php/mute
    function mute()
    {
        $conn = connectToDB();
        $isMuted = checkIfMuted($conn);

        if ($isMuted)
        {
            $sql="UPDATE commands SET mute = 0";
        }
        else
        {
            $sql="UPDATE commands SET mute = 1";
        }
        $conn->query($sql);
        $conn->close();
    }

    function checkIfMuted($conn)
    {
        //$conn = connectToDB();
        $sql= "SELECT mute FROM commands";
    
        $result = $conn->query($sql);
 
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {

                if ($row["mute"] == 1)
                {
                    return true;
                }
                else
                {
                    return false;
                } 
             }
        } 
    }

    // http://localhost/radio.php/turnOnOff
    function turnOnOff()
    {
        $conn = connectToDB();
        $isTurnedOn = checkIfTurnedOn($conn);

        if ($isTurnedOn)
        {
            $sql="UPDATE commands SET on_off = 0";
        }
        else
        {
            $sql="UPDATE commands SET on_off = 1";
        }
        $conn->query($sql);
        $conn->close();
    }
    
    function checkIfTurnedOn($conn)
    {
        $sql= "SELECT on_off FROM commands";
    
        $result = $conn->query($sql);
 
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {

                if ($row["on_off"] == 1)
                {
                    return true;
                }
                else
                {
                    return false;
                } 
             }
        } 
    }

    // http://localhost/radio.php/changeChannel/1
    function changeChannel($channelId)
    {
        $conn = connectToDB();
        $sql= "UPDATE commands SET channel_id = " . $channelId[3];
        $conn->query($sql);
        $conn->close();
    }

    // http://localhost/radio.php/addChannel/BEY_FM/http:@@45.32.154.169:9304
    function addChannel($nameAndUrl)
    {
        $conn = connectToDB();

        $name = $nameAndUrl[3];
        $url = $nameAndUrl[4];

        $url = str_replace("@","/",$url);

        $sql= "INSERT INTO channels (name, url) VALUES ('$name','$url')";
        $conn->query($sql);
        $conn->close();
    }

    // http://localhost/radio.php/editChannel/cemil/testURL/4
    function editChannel($idAndNameAndUrl)
    {
        $conn = connectToDB();

        $name = $idAndNameAndUrl[3];
        $url = $idAndNameAndUrl[4];
        $id = $idAndNameAndUrl[5];

        $sql= "UPDATE channels SET name = '$name', url = '$url'  WHERE id = '$id'";
        $conn->query($sql);
        $conn->close();
    }

    // http://localhost/radio.php/deleteChannel/7
    function deleteChannel($id)
    {
        $conn = connectToDB();
        $sql= "DELETE FROM channels WHERE id = $id[3]";
        $conn->query($sql);
        $conn->close();
    }

    // http://localhost/radio.php/getFavourites/
    function getFavourites()
    {
        $conn = connectToDB();

        $sql="SELECT * FROM channels WHERE isFavourite = 1";
    
        $result = $conn->query($sql);
 
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
               echo "id: " . $row["id"]. " - Name: " . $row["name"]. " - URL: " . $row["url"]. "<br>";  
             }
        } else {
            echo "0 results";
        }
        $conn->close();
    } 

    // http://localhost/radio.php/setUnsetFavourites/4
    function setUnsetFavourites($channelId)
    {
        $conn = connectToDB();

        $sql="SELECT * FROM channels WHERE id = $channelId[3]";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
            
            $isFavourite = $row["isFavourite"];

            if ($isFavourite == 0)
            {
                $sqlFav="UPDATE channels SET isFavourite = 1 WHERE id = " . $channelId[3];
            }
            else
            {
                $sqlFav="UPDATE channels SET isFavourite = 0 WHERE id = " . $channelId[3];
            }        
            $conn->query($sqlFav);
            }
        } 
        $conn->close();
    } 





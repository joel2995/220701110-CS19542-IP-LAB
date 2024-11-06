<?php
    // echo $_GET["cid"];
    include "db.php";
    $cid="";
    if(isset($_GET["cid"]))
        $cid=$_GET["cid"];
    elseif(isset($_POST["cid"]))
        $cid=$_POST["cid"];
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $cid=$_POST["cid"];
        if(isset($_POST["createaccount"])){
            // echo "creating Account";
            $atype=$_POST["atype"];
            $sql="insert into account(cid,atype) values('$cid','$atype')";
            if($db->query($sql)===False)   
                echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>
<html>
<head>
    <title>Profile</title>
</head>
<body>
    <?php
        $sql="select * from account where cid='$cid'";
        $result=$db->query($sql);
        if($result->num_rows>0){
            while($row=$result->fetch_assoc()){
                echo "<a href=transaction.php?ano=".$row['ano'].">Account No:".$row['ano']." Account Type:".$row['atype']." Balance:".$row['balance']."</a><br>";
            }
        }
        else{
            echo "NO Account Found";
        }
        // echo $cid;
    ?>
    <form action="/home.php" method="post">
        <input hidden type="text" name="cid" value=<?php echo $cid ?>>
        Account Type: <select name="atype" id="">
            <option value="S">Savings</option>
            <option value="C">Current</option>
        </select><br>
        <input type="submit" name="createaccount" value="Create Account"><br>
    </form>
</body>
</html>
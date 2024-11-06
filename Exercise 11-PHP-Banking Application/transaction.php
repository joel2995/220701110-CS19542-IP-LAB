<?php
    include "db.php";
    $ano="";
    if(isset($_GET["ano"]))
        $ano=$_GET["ano"];
    elseif(isset($_POST["ano"]))
        $ano=$_POST["ano"];
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(isset($_POST["transfer"])){
            $ttype=$_POST["ttype"];
            $amount=(int)$_POST["amount"];
            if($ttype=="D")
                $tt=1;
            else
                $tt=-1;
            $sql="select * from account where ano='$ano'";
            $result=$db->query($sql);
            $row=$result->fetch_assoc();
            $balance=(int)$row["balance"];
            $amt=$balance+$amount*$tt;
            
            if($tt<0 and $balance<$amount ){
                echo "Insufficient Balance<br>";
            }
            else{
                $sql="update account set balance='$amt' where ano='$ano'";
                if($db->query($sql)===False)   
                    echo "Error: " . $sql . "<br>" . $conn->error;

                $sql="insert into transaction (ano,ttype,tamount) values('$ano','$ttype','$amount')";
                if($db->query($sql)===False)   
                    echo "Error: " . $sql . "<br>" . $conn->error;
            }

        }
        
    }
?>
<html>
<head>
    <title>Trancation</title>
</head>
<body>
    <?php
        $sql="select * from account where ano='$ano'";
        $result=$db->query($sql);
        $row=$result->fetch_assoc();
        if($row)
            echo "Account No:".$row['ano']." Account Type:".$row['atype']." Balance:".$row['balance']."<br>"; 
    ?>
    <form action="/transaction.php" method="post">
        <input hidden type="text" name="ano" value=<?php echo $ano ?>>
        Transfer Type: <select name="ttype">
            <option value="D">Deposit</option>
            <option value="W">Withdraw</option>
        </select><br>
        Amount: <input type="text" name="amount"><br>
        <input type="submit" name="transfer" value="Transfer">
    </form>
</body>
</html>
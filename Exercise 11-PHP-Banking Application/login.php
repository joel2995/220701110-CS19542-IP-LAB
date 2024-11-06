<?php
    include "db.php";
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $name=$_POST["name"];
        // $atype=$_POST["atype"];
        $pass=password_hash($_POST["pass"], PASSWORD_DEFAULT);
        if(isset($_POST["createuser"])){
            echo "creating";
            $sql="insert into customer(cname,password) values('$name','$pass')";
            if($db->query($sql)===TRUE){
                echo "User created successfully";
            }
            else    
                echo "Error: " . $sql . "<br>" . $conn->error;
        }
        elseif(isset($_POST["login"])){
            $sql="select * from customer where cname='$name'";
            $result = $db->query($sql);
            if($result->num_rows>0){
                $row=$result->fetch_assoc();
                if(password_verify($_POST["pass"],$row['password']))
                    header("location:home.php?cid=".$row['cid']);
                else
                    echo " password mismatch ";
            }
            else    
                echo " user not found";

        }
    }
?>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <form action="\login.php" method = "POST">
        Name: <input type="text" name="name"><br>
        <!-- Account Type: <select name="atype" id="">
            <option value="S">Savings</option>
            <option value="C">Current</option>
        </select><br> -->
        Password: <input type="text" name="pass" ><br>
        <input type="submit" name="createuser" value="Create User">
        <input type="submit" name="login" value="Login">
    </form>
</body>
</html>
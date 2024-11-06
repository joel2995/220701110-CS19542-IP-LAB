<?php
    include 'db.php';
    $eid="";
    if(isset($_GET["eid"]))
        $eid=$_GET["eid"];
    elseif(isset($_POST["eid"]))
        $eid=$_POST["eid"];
    $sql="select * from employee where empid='$eid'";
    $result = $db->query($sql);
    $row=$result->fetch_assoc();

    // echo "Employee Id:".$row["empid"]."<br>";
    $name=$row["ename"];
    $desig=$row["desig"];
    $dept=$row["dept"];
    // echo "Employee Date Of Join".$row["doj"]."<br>";
    $salary=$row["salary"];
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(isset($_POST["update"])){
            $name=$_POST["name"];
            $designation=$_POST["designation"];
            $department=$_POST["department"];
            $salary=$_POST["salary"];
            $sql="update employee set ename='$name',desig='$designation',dept='$department',salary='$salary' where empid='$eid'";
            if($db->query($sql)===TRUE){
                echo "employee updated successfully<br>";
            }
            else    
                echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>
<html>
<head>
    <title>update</title>
</head>
<body>
    <form action="\update.php" method = "POST">
        <input type="hidden" name="eid" value=<?php echo $eid ?>>
        Name: <input type="text" name="name" value=<?php echo $name ?>><br>
        Designation: <input type="text" name="designation" value="<?php echo $desig ?>"><br>
        Department: <input type="text" name="department" value=<?php echo $dept ?>><br>
        Salary: <input type="text" name="salary" value=<?php echo $salary ?>><br>
        <input type="submit" name="update" value="Update"><br>
    </form>
    <a href="insert.php">Back</a>
</body>
</html>
<?php
    
?>
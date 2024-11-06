<html>
<head>
    <title>Register</title>
</head>
<body>
    <form action="\insert.php" method = "POST">
        Name: <input type="text" name="name"><br>
        Designation: <input type="text" name="designation"><br>
        Department: <input type="text" name="department"><br>
        Salary: <input type="text" name="salary"><br>
        <input type="submit" name="insert" value="Insert"><br>
        Employee Id: <input type="text" name="eid">
        <input type="submit" name="search" value="Search">
    </form>
</body>
</html>
<?php
    include "db.php";
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(isset($_POST["insert"])){
            $name=$_POST["name"];
            $designation=$_POST["designation"];
            $department=$_POST["department"];
            $salary=$_POST["salary"];
            echo "creating";
            $sql="insert into employee(ename,desig,dept,salary) values('$name','$designation','$department','$salary')";
            if($db->query($sql)===TRUE){
                echo "employee inserted successfully<br>";
                $eid=$db->insert_id;
                $sql="select * from employee where empid='$eid'";
                $result = $db->query($sql);
                if($result->num_rows>0){
                    $row=$result->fetch_assoc();
                    echo "Employee Id:".$row["empid"]."<br>";
                    echo "Employee Name:".$row["ename"]."<br>";
                    echo "Employee Designation:".$row["desig"]."<br>";
                    echo "Employee Department:".$row["dept"]."<br>";
                    echo "Employee Date Of Join".$row["doj"]."<br>";
                    echo "Employee salary:".$row["salary"]."<br>";
                }
            }
            else    
                echo "Error: " . $sql . "<br>" . $conn->error;
        }
        elseif(isset($_POST["search"])){
            $eid=$_POST["eid"];
            $sql="select * from employee where empid='$eid'";
            $result = $db->query($sql);
            if($result->num_rows>0){
                header("location:update.php?eid=".$eid);
                // $row=$result->fetch_assoc();

                // echo "Employee Id:".$row["empid"]."<br>";
                // echo "Employee Name:".$row["ename"]."<br>";
                // echo "Employee Designation:".$row["desig"]."<br>";
                // echo "Employee Department:".$row["dept"]."<br>";
                // echo "Employee Date Of Join".$row["doj"]."<br>";
                // echo "Employee salary:".$row["salary"]."<br>";
            }
            else    
                echo " user not found";
        }
    }
?>
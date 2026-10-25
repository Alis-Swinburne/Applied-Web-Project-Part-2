<?php

// This include is for creating tables if they do not exist, call it after setting and verifying $conn in the file you are using tables in

$sql = "CREATE TABLE IF NOT EXISTS `Jobs` (
  `Job_Code` varchar(6) NOT NULL,
  `Job_Title` text NOT NULL,
  `Salary` int(10) NOT NULL,
  `Description` longtext NOT NULL,
  `Min_Experience` int(2) DEFAULT NULL,
  PRIMARY KEY (`Job_Code`)
)"; 

if (!mysqli_query($conn, $sql)) {
    error_log("User table setup failed: ". mysqli_error($conn));
    die("Database setup failed: ". mysqli_error($conn));
}
else {
    // insert admin user automatically as first entry in db
    $sql = "INSERT INTO `Jobs` (`Job_Code`, `Job_Title`, `Salary`, `Description`, `Min_Experience`) VALUES
            ('DAT551', 'Financial Data Analyst', 110000, 'Translate customer transaction data and financial trends into actionable business insights and drive product strategies, fraud prevention models, and user engagement', 2),
            ('DEV042', 'Senior Full Stack Developer', 155000, 'Lead the engineering of our core web applications, ensuring high scalability, performance, and security across all platforms', 5),
            ('SEC918', 'Cybersecurity Analyst', 130000, 'Continuously monitoring threats, assessing vulnerabilities, and enforcing security compliance across all operational systems', 3)";
    mysqli_query($conn, $sql);
}

?>
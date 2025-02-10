<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .profile-container {
            width: 100%;
            max-width: 1200px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            margin: 20px auto;
        }
        .profile-header {
            text-align: center;
            border-bottom: 3px solid black;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .profile-header h2 {
            margin: 5px 0;
            text-transform: uppercase;
        }
        .profile-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .profile-picture {
            width: 150px;
            height: 180px;
            border: 3px solid black;
            object-fit: cover;
        }
        .profile-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .profile-table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }
        .label {
            font-weight: bold;
            text-transform: uppercase;
            width: 30%;
            background-color: #f8f8f8;
        }
    </style>
</head>
<body>
<?php 
    $status_kawin = [];
    $status_kawin['TK'] = "Tidak Kawin";
    $status_kawin['K'] = "Kawin";
    $status_kawin['M'] = "Meninggal";
    $status_kawin['CM'] = "Cerai/Mati";
    $status_kawin['CH'] = "Cerai/Hidup";

    $pass_photo = ($jemaat->pass_photo != "") ? $jemaat->pass_photo : "storage/jemaat/file/no-image.jpg";
?>
    <div class="profile-container">
        <div class="profile-header">
            <h2>GKPI Griya Permata</h2>
            <h3>Personal Profile Report</h3>
        </div>

        <div class="profile-content">
            <!-- <img src="profile.jpg" alt="Profile Picture" class="profile-picture"> -->
            <img src="{{ asset($pass_photo) }}"  alt="Profile Picture" class="profile-picture" width="240px" />
            <table class="profile-table">
                <tr><td class="label">Name:</td><td><?php echo $jemaat['name'];?></td></tr>
                <tr><td class="label">Nick Name:</td><td><?php echo $jemaat['nick_name'];?></td></tr>
                <tr><td class="label">Gender:</td><td><?php echo ( $jemaat['jenis_kelamin'] == "0" ) ? "MALE" : "FEMALE";?></td></tr>
                <tr><td class="label">Birth Place:</td><td><?php echo $jemaat['birth_place'];?></td></tr>
                <tr><td class="label">Birth Date:</td><td><?php echo $jemaat['birth_date'];?></td></tr>
                <tr><td class="label">Address:</td><td><?php echo $jemaat['address'];?></td></tr>
                <tr><td class="label">Phone Number:</td><td><?php echo $jemaat['mobile_no'];?></td></tr>
                <tr><td class="label">Email Address:</td><td><?php echo $jemaat['email'];?></td></tr>
                <tr><td class="label">Marital Status:</td><td><?php echo $status_kawin[$jemaat['marital_status']];?></td></tr>
                <tr><td class="label">Spouse Name:</td><td><?php echo $jemaat['spouse_name'];?></td></tr>
                <tr><td class="label">Married Date:</td><td><?php echo $jemaat['marriage_date'];?></td></tr>
                <tr><td class="label">Role in Family:</td><td><?php echo $jemaat['role'];?></td></tr>
                <tr><td class="label">Family Group:</td><td><?php echo $jemaat->family->family_name;?></td></tr>
                <tr><td class="label">Status in Church:</td><td><?php echo $jemaat['member_type'];?></td></tr>
                <tr><td class="label">Baptise Status:</td><td><?php echo $jemaat['baptise_status'];?></td></tr>
                <tr><td class="label">Previous Church:</td><td><?php echo $jemaat['previous_church'];?></td></tr>
                <tr><td class="label">Remark:</td><td><?php echo $jemaat['remark'];?></td></tr>
            </table>
        </div>
    </div>

</body>
</html>

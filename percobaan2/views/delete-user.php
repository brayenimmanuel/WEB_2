<?php
require_once __DIR__ . '/../models/pegawai.php';

use models\Pegawai;

// Check if ID is provided in the URL
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete the user
    Pegawai::delete($id);
    
    // Redirect back to the list page
    header("Location: list-user.php");
    exit;
} else {
    // If no ID is provided, redirect to the list page
    header("Location: list-user.php");
    exit;
}
?>
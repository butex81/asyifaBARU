<?php 

/***
* e-Pasien from version 0.1 Beta
* Last modified: 06 April 2018
* Author : drg. Faisol Basoro
* Email : drg.faisol@basoro.org
*
* File : config.php
* Description : Main config, function and helper
* Licence under GPL
***/

if (preg_match ('/config.php/', basename($_SERVER['PHP_SELF']))) die ('Unable to access this script directly from browser!');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sik');

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); 

define('VERSION', '1.5 Beta');
define('URL', '');
define('DIR', '');
define('HARIDAFTAR', '03'); // Batasi hari pendaftaran 3 hari kedepan
define('LIMITJAM', '12:00:00'); // Batasi jam pendaftaran
define('SIGNUP', 'ENABLE'); // ENABLE atau DISABLE pendaftaran pasien baru
define('KODE_BERKAS', '002'); // Kode katergori berkas digital
define('UKURAN_BERKAS', '5000000'); // Ukuran berkas digital dalam byte
define('PENGADUAN', 'ENABLE'); // ENABLE atau DISABLE fitur pengaduan pasien. Lihat file includes/pengaduan.php untuk membuat tabel pengaduannya.

function escape($string) {
    global $connection;
    return mysqli_real_escape_string($connection, $string);
}

function query($sql) {
    global $connection;
    $query = mysqli_query($connection, $sql);
    confirm($query);
    return $query;
}

function confirm($query) {
    global $connection;
    if(!$query) {
        die("Query failed! " . mysqli_error($connection));
    }
}

function fetch_array($result) {
    return mysqli_fetch_array($result);
}

function fetch_assoc($result) {
    return mysqli_fetch_assoc($result);
}

function num_rows($result) {
    return mysqli_num_rows($result);
}

// Get date and time
date_default_timezone_set('Asia/Jakarta');
$month      = date('Y-m');
$date       = date('Y-m-d');
$time       = date('H:i:s');
$date_time  = date('Y-m-d H:i:s');

// Namahari
$hari=fetch_array(query("SELECT DAYNAME(current_date())"));
$namahari="";
if($hari[0]=="Sunday"){
    $namahari="AKHAD";
}else if($hari[0]=="Monday"){
    $namahari="SENIN";
}else if($hari[0]=="Tuesday"){
   	$namahari="SELASA";
}else if($hari[0]=="Wednesday"){
    $namahari="RABU";
}else if($hari[0]=="Thursday"){
    $namahari="KAMIS";
}else if($hari[0]=="Friday"){
    $namahari="JUMAT";
}else if($hari[0]=="Saturday"){
    $namahari="SABTU";
}

// Get settings 
$getSettings = query("SELECT * FROM setting");
$dataSettings = fetch_assoc($getSettings);

// htmlentities remove #$%#$%@ values
function clean($string) {
    return htmlentities($string);
}

// redirect to another page
function redirect($location) {
    return header("Location: {$location}");
}

// add message to session
function set_message($message) {
    if(!empty($message)) {
        $_SESSION['message'] = $message;
    } else {
        $message = "";
    }
}

// display session message
function display_message() {
    if(isset($_SESSION['message'])) {
        echo '<div class="alert bg-pink alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$_SESSION['message'].'</div>';
        unset($_SESSION['message']);
    }
}

// show errors
function validation_errors($error) {
    $errors = '<div class="alert bg-pink alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$error.'</div>';
    return $errors;
}

// check if nik exits
function nik_exits($no_ktp) {
    $sql = "SELECT no_ktp FROM pasien WHERE no_ktp = '$no_ktp' ";
    $result = query($sql);
    // check if we found something
    if(num_rows($result) == 1) {
        return true;
    } else {
        return false;
    }
}
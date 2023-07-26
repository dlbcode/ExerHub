<?php
require 'php/db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all public workouts
$public_workouts = query("SELECT * FROM workouts WHERE is_public = 1");

// Fetch all workouts created by the current user
$user_workouts = query("SELECT * FROM workouts WHERE user_id = $user_id");

// Fetch all selected workouts for the current user
$selected_workouts_result = query("SELECT workout_id FROM user_selected_workouts WHERE user_id = $user_id");
$selected_workouts = array();
while ($row = $selected_workouts_result->fetch_assoc()) {
    $selected_workouts[] = $row['workout_id'];
}

function display_workouts($workouts, $selected_workouts) {
  while ($workout = $workouts->fetch_assoc()) {
      $checked = in_array($workout['id'], $selected_workouts) ? 'checked' : '';
      echo "<li>
              <label>
                  <input type='checkbox' class='workout-checkbox filled-in' data-workout-id='{$workout['id']}' $checked />
                  <span>{$workout['name']}</span>
              </label>
            </li>";
  }
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <?php include 'php/header.php'; ?>
    <title>ExerHub - Select Workouts</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dark">
<nav>
<div class="nav-wrapper">
  <span class="brand-logo" style="margin-left: 60px"><a href="index.html"><i class="material-icons">home</i>/</a><span class="sub-page-name">Select Workouts</span></span>
    <a href="index.html" data-target="side-nav" class="show-on-large sidenav-trigger"><i class="material-icons">menu</i></a>
    <ul class="right" id="top-nav"></ul>
</div>
</nav>
  <ul class="sidenav" id="side-nav"></ul>
  <main class="container">
    <h5>Select Workouts</h5>
    <h6>Public Workouts</h6>
    <ul>
        <?php display_workouts($public_workouts, $selected_workouts); ?>
    </ul>
    <h2>Your Workouts</h2>
    <ul>
        <?php display_workouts($user_workouts, $selected_workouts); ?>
    </ul>
  </main>
  <script src="js/nav.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
  $(document).ready(function() {
      $('.workout-checkbox').change(function() {
          var workout_id = $(this).data('workout-id');
          var selected = $(this).is(':checked');
          $.post('php/workout_selection.php', { workout_id: workout_id, selected: selected });
      });
  });
  </script>
</body>
</html>
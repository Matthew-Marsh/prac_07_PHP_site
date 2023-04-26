<?php
session_start();
$correct_answer = strval($_SESSION["correct_answer"]);
$user_answer = strtolower(strval($_POST['answer']));

if ($correct_answer == $user_answer) {
    header('location: win_page.php');
} else {
    header('location: index.php');
    $_SESSION["answer_incorrect"] = true;
}

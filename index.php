<?php
// Make a HTTP request to the quiz site API to retrieve the quiz data
$url = "https://opentdb.com/api.php?amount=20&category=27&difficulty=easy";
$response = file_get_contents($url);

// Parse the JSON response into a PHP array
$data = json_decode($response, true);
$quiz = array();

// Get Question from the Quiz data array
list($question, $possible_answers) = get_question($data["results"], $quiz);

session_start();
$_SESSION["correct_answer"] = strtolower(strval($question["correct_answer"]));
$_SESSION["answer_incorrect"] = $_SESSION["answer_incorrect"] ?? false;

/**
 * @param $results
 * @param array $quiz
 * @return array
 */
function get_question($results, array $quiz): array
{
    foreach ($results as $datum) {
        $question = $datum["question"];
        $possible_answers = array_merge([$datum["correct_answer"]], $datum["incorrect_answers"]);
        shuffle($possible_answers);
        $correct_answer = $datum["correct_answer"];
        $quiz[] = array(
            "question" => $question,
            "correct_answer" => $correct_answer,
            "possible_answers" => $possible_answers
        );
    }

    try {
        $question_index = random_int(0, count($quiz) - 1);
        $question = $quiz[$question_index];
    } catch (Exception $e) {
        echo "Exception Error: ", $e;
    }
    return array($question, $possible_answers);
}

?>

<!doctype html>
<html lang="">
    <head>
        <meta charset="UTF-8">
        <title>Random Animal Quiz</title>
        <link href="styles.css" rel="stylesheet" type="text/css" media="screen">
    </head>

    <body>
    <?php include( "nav.php" ); ?>
        <?php if ($_SESSION["answer_incorrect"]) {
                echo "<p>Sorry that was incorrect, try another.</p>";
                $_SESSION["answer_incorrect"] = false;
            }
        ?>
        <h3><?php echo $question['question'] ?></h3>
        <form action="check_answer.php" method="post">
            <?php
                foreach ($question['possible_answers'] as $possible_answer) {
                    echo "<label><input type='radio' name='answer' value='" . $possible_answer . "'>" . $possible_answer . "</label><br>";
                }
            ?>
            <br>
            <input type="submit" name="submit" value="Submit">
        </form>
    </body>
    <?php include( "footer.php" ); ?>
</html>


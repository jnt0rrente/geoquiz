class SingleQuizPage {
    constructor() {

    }

    solve(id) {
        var answers = [];
        var solutions = [];
        $("input[type='radio']:checked").each(function() {
            answers.push($(this).val());
        });

        $.ajax({
            url: '/quiz.php',
            method: 'GET',
            contentType: 'text',
            dataType: 'application/json',
            data: id,
            success: function(data) {
                solutions = JSON.parse(data);
            },
            error: function(err) {

            }
        });

        console.log(JSON.stringify(solutions));
    }
}

var sqp = new SingleQuizPage();
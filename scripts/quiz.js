class SingleQuizPage {
    constructor() {

    }

    solve(id) {
        var answers = [];
        var solutions;

        $("input[type='radio']:checked").each(function() {
            answers.push($(this).val());
        });

        $.ajax({
            url: '/quiz.php',
            method: 'GET',
            contentType: 'application/json',
            dataType: 'application/json',
            data: JSON.stringify({
                "id": id
            }),
            success: function(data) {
                solutions = JSON.parse(data);
            },
            error: function(err) {
                console.error(JSON.stringify(err));
            }
        });

        console.log(JSON.stringify(answers));
        console.log(JSON.stringify(solutions));
    }
}

var sqp = new SingleQuizPage();
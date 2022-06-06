class SingleQuizPage {
    constructor() {

    }

    solve() {
        var answers = [];
        $("input[type='radio']:checked").each(function() {
            answers.push($(this).value());
        });

        console.log(JSON.stringify(answers));
    }
}

var sqp = new SingleQuizPage();
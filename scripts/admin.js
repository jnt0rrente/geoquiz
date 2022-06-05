"use strict"
class UploadManager {
    constructor(handler) {
        this.acceptedType = /text.xml/;
        this.handler = handler;
    }

    read(files) {
        var file = files[0];

        if (!file.type.match(this.acceptedType)) {
            alert("mal tipo");
            return;
        }


        var reader = new FileReader();
        reader.onload = function() {
            try {
                console.log(reader.result);
                this.fileContent = reader.result;
            } catch (exception) {
                console.log("Error: " + exception.message);
            }
        }.bind(this);
        reader.readAsText(file);


        console.log(this.fileContent);
        this.parsedQuiz = new FormlParser().parse(this.fileContent);

    }

    upload() {
        var quiz = this.parsedQuiz;
        $.ajax({
            url: '/quiz_upload.php',
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: quiz,
            success: this.onUploadSuccess,
            error: this.onUploadError
        });
    }

    onUploadSuccess(data) {
        console.log("success:");
        console.log(data);
        alert("olee");
    }

    onUploadError(error) {
        console.log(error);
        alert("cagaste");
    }

}

//recibe el xml plano y devuelve un objeto de javascript
class FormlParser {
    parse(content) {
        var quiz = {};

        var parsedXML = new DOMParser().parseFromString(content, "text/xml");

        quiz.title = parsedXML.getElementsByTagName("title")[0].nodeValue;
        console.log(JSON.stringify(parsedXML.getElementsByTagName("title")));
        quiz.description = parsedXML.getElementsByTagName("description")[0].nodeValue;
        quiz.questions = [];

        var xmlQuestions = parsedXML.getElementsByTagName("question");
        for (let i = 0; i < xmlQuestions.length; i++) {
            var jsonQuestion = {};
            jsonQuestion.text = xmlQuestions[i].getAttribute("text");
            jsonQuestion.correct_option = xmlQuestions[i].getAttribute("accepted");
            jsonQuestion.options = [];

            var options = xmlQuestions[i].children;
            for (let j = 0; j < options.length; j++) {
                jsonQuestion.options.push(options[j].getAttribute("text"));
            }
            quiz.questions.push(jsonQuestion);
        }

        console.log(JSON.stringify(quiz));
        return quiz;
    }
}

var formlHandler = new FormlParser();
var uploadManager = new UploadManager();
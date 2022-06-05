"use strict"
class UploadManager {
    constructor(handler) {
        this.acceptedType = /text.xml/;
        this.handler = handler;
    }

    read(files) {
        var file = files[0];
        var content = "";

        if (!file.type.match(this.acceptedType)) {
            alert("mal tipo");
            return;
        }


        var reader = new FileReader();
        reader.onload = function() {
            try {
                var fileContent = reader.result;
                this.save(fileContent);
            } catch (exception) {
                console.error("FileReader error: " + exception.message);
            }
        }.bind(this)
        reader.readAsText(file);
    }

    save(fileContent) {
        this.fileContent = fileContent;
    }

    upload() {
        var quiz = new FormlParser().parse(this.fileContent);;
        $.ajax({
            url: '/quiz_upload.php',
            method: 'POST',
            contentType: 'application/json',
            dataType: 'text',
            data: JSON.stringify({
                "quiz": quiz
            }),
            success: this.onUploadSuccess,
            error: this.onUploadError
        });
    }

    onUploadSuccess(response) {
        console.log("Server response: " + response);
    }

    onUploadError(error) {
        console.error("Server error. Response: " + JSON.stringify(error));
    }

}

//recibe el xml plano y devuelve un objeto de javascript
class FormlParser {
    parse(content) {
        var quiz = {};
        var parsedXML = new DOMParser().parseFromString(content, "text/xml");

        quiz.title = parsedXML.getElementsByTagName("title")[0].childNodes[0].nodeValue;
        quiz.description = parsedXML.getElementsByTagName("description")[0].childNodes[0].nodeValue;

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

        return quiz;
    }
}

var formlHandler = new FormlParser();
var uploadManager = new UploadManager();
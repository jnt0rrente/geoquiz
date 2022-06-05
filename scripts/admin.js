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
                var fileContent = reader.result;
                this.upload(fileContent);
            } catch (exception) {
                console.log("Error: " + exception.message);
            }
        }.bind(this)

        reader.readAsText(file);
    }

    upload(fileContent) {
        quiz = new FormlParser().parse(fileContent);
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

    onUploadError() {
        alert("cagaste");
    }

}

//recibe el xml plano y devuelve un objeto de javascript
class FormlParser {
    parse(content) {
        var quiz = {};

        var parsedXML = new DOMParser().parseFromString(content, "text/xml");

        quiz.title = parsedXML.getElementsByTagName("title")[0].nodeValue;
        quiz.description = parsedXML.getElementsByTagName("description")[0].nodeValue;
        quiz.questions = [];

        parsedXML.getElementsByTagName("question").forEach(question => {
            jsonQuestion = {};
            jsonQuestion.text = question.getAttribute("text");
            jsonQuestion.correct_option = question.getAttribute("accepted");

            quiz.questions.push(jsonQuestion);
        });

        return quiz;
    }
}

var formlHandler = new FormlParser();
var uploadManager = new UploadManager();
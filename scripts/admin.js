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
                console.log("Error: " + exception.message);
            }
        }.bind(this)
        reader.readAsText(file);
    }

    save(fileContent) {
        console.log(fileContent);
        this.fileContent = fileContent;
    }

    upload() {
        var quiz = new FormlParser().parse(this.fileContent);;
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
        quiz.questions = [];

        var parsedXML = new DOMParser().parseFromString(content, "text/xml");

        console.log(parsedXML.getElementsByTagName("title")[0].childNodes[0].nodeValue);
        quiz.title = parsedXML.getElementsByTagName("title")[0].childNodes[0].nodeValue;
        quiz.description = parsedXML.getElementsByTagName("description")[0].childNodes[0].nodeValue;

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
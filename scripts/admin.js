"use strict"
class UploadManager {
    constructor(handler) {
        this.acceptedType = /text.xml/;
        this.handler = handler;
        this.adminCoordinates = null;
    }

    read(files) { //lee un único archivo desde el html
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
                $("p:last").text("Upload status: File read correctly");
                $("input[type=button]").prop("disabled", false);
            } catch (exception) {
                $("p:last").text("Upload status: File reader error");
            }
        }.bind(this)
    }


    //guarda una string en el objeto
    save(fileContent) {
        this.fileContent = fileContent;
    }

    //hace la llamada de AJAX para enviar el contenido del archivo al backend
    upload() {
        try {
            var quiz = this.handler.parse(this.fileContent);
        } catch (exception) {
            $("p:last").text("Upload status: Parsing error");
        }
        quiz.restrictions = this.getSelectedRestrictions();

        $.ajax({
            url: '/upload.php',
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

    //devuelve un array con las restricciones que se eligen para el cuestionario
    getSelectedRestrictions() {
        let restrictedOn = []
        $("input[type=checkbox]:checked").each(function() {
            restrictedOn.push(this.name);
        });
        return restrictedOn;
    }

    onUploadSuccess(response) {
        console.log("Server response: " + response);

        $("p:last").text("Upload status: Success!");
    }

    onUploadError(error) {
        console.error("Server error. Response: " + JSON.stringify(error));

        $("p:last").text("Upload status: Error.");
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
var uploadManager = new UploadManager(formlHandler);
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
                this.handler.handle(fileContent);
            } catch (exception) {
                console.log("Error: " + exception.message);
            }
        }.bind(this)

        reader.readAsText(file);
    }

    upload() {
        $.ajax(

        );
    }

}

class FormlHandler {
    constructor() {

    }

    handle(content) {
        this.rawFile = content;
        console.log(content);
    }

}

var formlHandler = new FormlHandler();
var uploadManager = new UploadManager(formlHandler);
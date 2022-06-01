"use strict"
class UploadManager {
    constructor() {
        this.acceptedType = /text.xml/;
        this.handler = new FormlParser();
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

}

class FormlHandler {
    constructor() {

    }


}

var uploadManager = new UploadManager();
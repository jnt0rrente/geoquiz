"use strict"
class UploadManager {
    constructor() {
        this.acceptedType = /text.xml/
        this
    }

    read() {
        var file = document.getElementById("fileUpload").files[0];

        if (file.type.match(this.acceptedType)) {
            var fr = new FileReader();
            fr.onload = this.onFileLoad;
            fr.readAsText(file);
        } else {
            alert("tipo malo");
        }
    }
}
class QuizListManager {

    constructor() {
        this.apikey = "e1d215c28c8c49389af3cc37b829c5f5";
        this.url = "https://api.opencagedata.com/geocode/v1/json";
    }

    buildUrl(lat, long) {
        let returnUrl = this.url +
            "?key=" + this.apikey +
            "&q=" + encodeURIComponent(lat + "," + long);
    }

    getContinentForCoordinates(data) {
        let coords = data.coords;

        $.ajax({
            url: this.buildUrl(coords.latitude, coords.longitude),
            method: 'GET',
            dataType: 'json',
            success: this.callSuccess,
            error: this.callError
        });
    }

    callSuccess(data) {
        console.log(data);
        let returnedObject = JSON.parse(data);
    }

    saveRegion(loc) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.locationSuccess, this.locationError).bind(this);
            $("input[type=submit]").prop("disabled", false);
        } else {
            console.log("Location unavailable. Cannot proceed.");
        }
    }

    locationSuccess(data) {
        console.log("Location working.");
        this.getContinentForCoordinates(data);
    }

    locationError(err) {
        console.log("Location permission rejected.");
    }

}

let qlm = new QuizListManager();
window.onload = qlm.saveRegion();
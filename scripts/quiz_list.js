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

    getContinentForCoordinates(latitude, longitude) {
        $.ajax({
            url: this.buildUrl(latitude, longitude),
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

    saveRegion() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.locationSuccess.bind(this), this.locationError);
            $("input[type=submit]").prop("disabled", false);
        } else {
            console.log("Location unavailable. Cannot proceed.");
        }
    }

    locationSuccess(data) {
        console.log("Location working.");
        getContinentForCoordinates(data.coords.latitude, data.coords.longitude);
    }

    locationError(err) {
        console.log("Location permission rejected.");
    }

}

let qlm = new QuizListManager();
window.onload = qlm.saveRegion();
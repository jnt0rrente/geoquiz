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

    loadContinent() {
        let url = this.buildUrl(this.latitude, this.longitude);
        console.log(url);
        $.ajax({
            url: url,
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

    getCoordinates() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.locationSuccess.bind(this), this.locationError);
            console.log("Location working.");

            $("input[type=submit]").prop("disabled", false);
        } else {
            console.log("Location unavailable. Cannot proceed.");
        }
    }

    locationSuccess(data) {
        this.latitude = data.coords.latitude;
        this.longitude = data.coords.longitude;
        $("input[type=button]").prop("disabled", false);
    }

    locationError(err) {
        switch (err.code) {
            case error.PERMISSION_DENIED:
                console.error("Location permission denied.");
                break;
            case error.POSITION_UNAVAILABLE:
                console.error("Location error: position unavailable.");
                break;
            case error.TIMEOUT:
                console.error("Location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                console.error("Location error: unknown.");
                break;
        }
    }

}

let qlm = new QuizListManager();
qlm.getCoordinates();
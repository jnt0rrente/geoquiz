class ReverseGeocoder {
    constructor() {
        this.apikey = "e1d215c28c8c49389af3cc37b829c5f5";
        this.url = "https://api.opencagedata.com/geocode/v1/json";
    }

    getContinentForCoordinates(lat, long) {

    }

    buildUrl(lat, long) {
        let returnUrl = this.url +
            "?key=" + this.apikey +
            "&q=" + encodeURIComponent(lat + "," + long);
    }
}

class QuizListManager {

    checkLocationRestriction() {
        let rev = new ReverseGeocoder();
    }

}

let qlm = new QuizListManager();
window.onload(qlm.checkLocationRestriction());